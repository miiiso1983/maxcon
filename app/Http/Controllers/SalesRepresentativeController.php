<?php

namespace App\Http\Controllers;

use App\Models\SalesRepresentative;
use App\Models\CustomerVisit;
use App\Models\SalesOrder;
use App\Models\PaymentCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesRepresentativeController extends Controller
{
    /**
     * Display the sales representatives dashboard
     */
    public function dashboard()
    {
        $tenantId = Auth::user()->tenant_id;

        // إحصائيات عامة
        $stats = [
            'total_reps' => SalesRepresentative::forTenant($tenantId)->active()->count(),
            'today_visits' => CustomerVisit::whereDate('visit_date', today())
                ->whereHas('salesRepresentative', function($q) use ($tenantId) {
                    $q->where('tenant_id', $tenantId);
                })->count(),
            'today_orders' => SalesOrder::whereDate('order_date', today())
                ->whereHas('salesRepresentative', function($q) use ($tenantId) {
                    $q->where('tenant_id', $tenantId);
                })->count(),
            'today_collections' => PaymentCollection::whereDate('collection_date', today())
                ->whereHas('salesRepresentative', function($q) use ($tenantId) {
                    $q->where('tenant_id', $tenantId);
                })->sum('amount'),
        ];

        // قائمة المندوبين مع آخر الزيارات
        $representatives = SalesRepresentative::forTenant($tenantId)
            ->with(['visits' => function($q) {
                $q->orderBy('visit_date', 'desc')->limit(1);
            }])
            ->get();

        return view('sales-representatives.dashboard', compact('stats', 'representatives'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenantId = Auth::user()->tenant_id;

        $representatives = SalesRepresentative::forTenant($tenantId)
            ->with(['user', 'visits', 'orders', 'collections'])
            ->paginate(15);

        return view('sales-representatives.index', compact('representatives'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sales-representatives.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:sales_representatives,email',
            'phone' => 'required|string|max:20',
            'employee_code' => 'required|string|unique:sales_representatives,employee_code',
            'hire_date' => 'required|date',
            'base_salary' => 'nullable|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'assigned_areas' => 'nullable|array',
            'address' => 'nullable|string',
            'national_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $representative = SalesRepresentative::create([
            'tenant_id' => Auth::user()->tenant_id,
            'user_id' => Auth::id(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'employee_code' => $request->employee_code,
            'hire_date' => $request->hire_date,
            'base_salary' => $request->base_salary ?? 0,
            'commission_rate' => $request->commission_rate ?? 0,
            'assigned_areas' => $request->assigned_areas,
            'address' => $request->address,
            'national_id' => $request->national_id,
            'notes' => $request->notes,
            'status' => 'active',
        ]);

        return redirect()->route('sales-representatives.index')
            ->with('success', 'تم إضافة المندوب التجاري بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(SalesRepresentative $salesRepresentative)
    {
        $salesRepresentative->load([
            'latestVisits.customer',
            'latestOrders.customer',
            'latestCollections.customer',
            'latestReminders.customer'
        ]);

        // إحصائيات المندوب
        $stats = [
            'total_visits' => $salesRepresentative->visits()->count(),
            'total_orders' => $salesRepresentative->orders()->count(),
            'total_collections' => $salesRepresentative->collections()->sum('amount'),
            'pending_reminders' => $salesRepresentative->reminders()->where('status', 'pending')->count(),
        ];

        return view('sales-representatives.show', compact('salesRepresentative', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesRepresentative $salesRepresentative)
    {
        return view('sales-representatives.edit', compact('salesRepresentative'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalesRepresentative $salesRepresentative)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:sales_representatives,email,' . $salesRepresentative->id,
            'phone' => 'required|string|max:20',
            'employee_code' => 'required|string|unique:sales_representatives,employee_code,' . $salesRepresentative->id,
            'hire_date' => 'required|date',
            'base_salary' => 'nullable|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'assigned_areas' => 'nullable|array',
            'address' => 'nullable|string',
            'national_id' => 'nullable|string',
            'status' => 'required|in:active,inactive,suspended',
            'notes' => 'nullable|string',
        ]);

        $salesRepresentative->update($request->all());

        return redirect()->route('sales-representatives.index')
            ->with('success', 'تم تحديث بيانات المندوب بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesRepresentative $salesRepresentative)
    {
        $salesRepresentative->delete();

        return redirect()->route('sales-representatives.index')
            ->with('success', 'تم حذف المندوب بنجاح');
    }

    /**
     * Get visits data for AJAX requests
     */
    public function getVisits(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $date = $request->get('date', today()->format('Y-m-d'));
        $repId = $request->get('rep_id');

        $query = CustomerVisit::with(['salesRepresentative', 'customer'])
            ->whereHas('salesRepresentative', function($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->whereDate('visit_date', $date);

        if ($repId) {
            $query->where('sales_representative_id', $repId);
        }

        $visits = $query->get()->map(function($visit) {
            return [
                'id' => $visit->id,
                'representative_name' => $visit->salesRepresentative->name,
                'customer_name' => $visit->customer->name,
                'visit_date' => $visit->visit_date->format('Y-m-d H:i'),
                'location_address' => $visit->location_address,
                'visit_status' => $visit->visit_status,
                'visit_type' => $visit->visit_type,
            ];
        });

        return response()->json($visits);
    }

    /**
     * Export reports
     */
    public function export(Request $request)
    {
        // تصدير التقارير إلى Excel/PDF
        // سيتم تنفيذها لاحقاً
        return response()->json(['message' => 'Export functionality will be implemented']);
    }
}
