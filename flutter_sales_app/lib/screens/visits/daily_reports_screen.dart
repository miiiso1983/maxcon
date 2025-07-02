import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import '../../models/sales_representative.dart';
import '../../services/api_service.dart';
import '../../services/location_service.dart';
import '../../services/notification_service.dart';
import 'package:intl/intl.dart';

class DailyReportsScreen extends StatefulWidget {
  const DailyReportsScreen({Key? key}) : super(key: key);

  @override
  State<DailyReportsScreen> createState() => _DailyReportsScreenState();
}

class _DailyReportsScreenState extends State<DailyReportsScreen> {
  final ApiService _apiService = ApiService();
  final LocationService _locationService = LocationService.instance;
  final NotificationService _notificationService = NotificationService.instance;
  
  List<CustomerVisit> _visits = [];
  List<Customer> _customers = [];
  bool _isLoading = true;
  DateTime _selectedDate = DateTime.now();

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() => _isLoading = true);
    
    try {
      final customers = await _apiService.getCustomers();
      final visits = await _apiService.getVisits(date: _selectedDate);
      
      setState(() {
        _customers = customers;
        _visits = visits;
        _isLoading = false;
      });
    } catch (e) {
      setState(() => _isLoading = false);
      _showErrorSnackBar('خطأ في تحميل البيانات: $e');
    }
  }

  Future<void> _createNewVisit() async {
    final result = await showModalBottomSheet<CustomerVisit>(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => NewVisitBottomSheet(customers: _customers),
    );

    if (result != null) {
      try {
        final createdVisit = await _apiService.createVisit(result);
        setState(() {
          _visits.insert(0, createdVisit);
        });
        
        await _notificationService.showInstantNotification(
          id: createdVisit.id ?? 0,
          title: 'تم إنشاء زيارة جديدة',
          body: 'تم تسجيل زيارة العميل بنجاح',
        );
        
        _showSuccessSnackBar('تم إنشاء الزيارة بنجاح');
      } catch (e) {
        _showErrorSnackBar('خطأ في إنشاء الزيارة: $e');
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('التقارير اليومية'),
        backgroundColor: Colors.amber[700],
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.calendar_today),
            onPressed: _selectDate,
          ),
        ],
      ),
      body: Column(
        children: [
          // Date Selector
          Container(
            padding: const EdgeInsets.all(16),
            color: Colors.grey[100],
            child: Row(
              children: [
                Icon(Icons.calendar_today, color: Colors.amber[700]),
                const SizedBox(width: 8),
                Text(
                  DateFormat('EEEE، d MMMM yyyy', 'ar').format(_selectedDate),
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const Spacer(),
                Text(
                  '${_visits.length} زيارة',
                  style: TextStyle(
                    color: Colors.grey[600],
                    fontSize: 14,
                  ),
                ),
              ],
            ),
          ),
          
          // Visits List
          Expanded(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator())
                : _visits.isEmpty
                    ? _buildEmptyState()
                    : RefreshIndicator(
                        onRefresh: _loadData,
                        child: ListView.builder(
                          padding: const EdgeInsets.all(16),
                          itemCount: _visits.length,
                          itemBuilder: (context, index) {
                            final visit = _visits[index];
                            return _buildVisitCard(visit);
                          },
                        ),
                      ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: _createNewVisit,
        backgroundColor: Colors.amber[700],
        child: const Icon(Icons.add, color: Colors.white),
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(
            Icons.assignment_outlined,
            size: 80,
            color: Colors.grey[400],
          ),
          const SizedBox(height: 16),
          Text(
            'لا توجد زيارات لهذا اليوم',
            style: TextStyle(
              fontSize: 18,
              color: Colors.grey[600],
            ),
          ),
          const SizedBox(height: 8),
          Text(
            'اضغط على + لإضافة زيارة جديدة',
            style: TextStyle(
              fontSize: 14,
              color: Colors.grey[500],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildVisitCard(CustomerVisit visit) {
    final customer = _customers.firstWhere(
      (c) => c.id == visit.customerId,
      orElse: () => Customer(
        id: 0,
        name: 'عميل غير معروف',
        email: '',
        phone: '',
        address: '',
        customerCode: '',
        customerType: '',
        creditLimit: 0,
        currentBalance: 0,
        status: '',
      ),
    );

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                CircleAvatar(
                  backgroundColor: _getStatusColor(visit.visitStatus),
                  radius: 20,
                  child: Icon(
                    _getStatusIcon(visit.visitStatus),
                    color: Colors.white,
                    size: 20,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        customer.name,
                        style: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      Text(
                        DateFormat('HH:mm').format(visit.visitDate),
                        style: TextStyle(
                          color: Colors.grey[600],
                          fontSize: 14,
                        ),
                      ),
                    ],
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  decoration: BoxDecoration(
                    color: _getStatusColor(visit.visitStatus).withOpacity(0.1),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Text(
                    _getStatusText(visit.visitStatus),
                    style: TextStyle(
                      color: _getStatusColor(visit.visitStatus),
                      fontSize: 12,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ],
            ),
            
            if (visit.visitNotes != null && visit.visitNotes!.isNotEmpty) ...[
              const SizedBox(height: 12),
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.grey[100],
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Row(
                  children: [
                    Icon(Icons.note, color: Colors.grey[600], size: 16),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Text(
                        visit.visitNotes!,
                        style: TextStyle(
                          color: Colors.grey[700],
                          fontSize: 14,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ],
            
            if (visit.latitude != null && visit.longitude != null) ...[
              const SizedBox(height: 12),
              Row(
                children: [
                  Icon(Icons.location_on, color: Colors.red[400], size: 16),
                  const SizedBox(width: 4),
                  Text(
                    'الموقع: ${visit.latitude!.toStringAsFixed(4)}, ${visit.longitude!.toStringAsFixed(4)}',
                    style: TextStyle(
                      color: Colors.grey[600],
                      fontSize: 12,
                    ),
                  ),
                ],
              ),
            ],
            
            const SizedBox(height: 12),
            Row(
              children: [
                if (visit.orderCreated)
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: Colors.green.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.shopping_cart, color: Colors.green, size: 12),
                        const SizedBox(width: 4),
                        const Text(
                          'تم إنشاء طلب',
                          style: TextStyle(
                            color: Colors.green,
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ],
                    ),
                  ),
                
                if (visit.paymentCollected) ...[
                  if (visit.orderCreated) const SizedBox(width: 8),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: Colors.blue.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.payment, color: Colors.blue, size: 12),
                        const SizedBox(width: 4),
                        const Text(
                          'تم التحصيل',
                          style: TextStyle(
                            color: Colors.blue,
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
                
                const Spacer(),
                IconButton(
                  icon: const Icon(Icons.more_vert),
                  onPressed: () => _showVisitOptions(visit),
                  iconSize: 20,
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'completed':
        return Colors.green;
      case 'in_progress':
        return Colors.blue;
      case 'scheduled':
        return Colors.orange;
      case 'cancelled':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  IconData _getStatusIcon(String status) {
    switch (status) {
      case 'completed':
        return Icons.check;
      case 'in_progress':
        return Icons.access_time;
      case 'scheduled':
        return Icons.schedule;
      case 'cancelled':
        return Icons.cancel;
      default:
        return Icons.help;
    }
  }

  String _getStatusText(String status) {
    switch (status) {
      case 'completed':
        return 'مكتملة';
      case 'in_progress':
        return 'جارية';
      case 'scheduled':
        return 'مجدولة';
      case 'cancelled':
        return 'ملغية';
      default:
        return 'غير معروف';
    }
  }

  Future<void> _selectDate() async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: _selectedDate,
      firstDate: DateTime.now().subtract(const Duration(days: 365)),
      lastDate: DateTime.now().add(const Duration(days: 30)),
    );
    
    if (picked != null && picked != _selectedDate) {
      setState(() {
        _selectedDate = picked;
      });
      _loadData();
    }
  }

  void _showVisitOptions(CustomerVisit visit) {
    showModalBottomSheet(
      context: context,
      builder: (context) => Container(
        padding: const EdgeInsets.all(16),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            ListTile(
              leading: const Icon(Icons.edit),
              title: const Text('تعديل الزيارة'),
              onTap: () {
                Navigator.pop(context);
                // TODO: Implement edit visit
              },
            ),
            ListTile(
              leading: const Icon(Icons.location_on),
              title: const Text('عرض الموقع'),
              onTap: () {
                Navigator.pop(context);
                // TODO: Show location on map
              },
            ),
            ListTile(
              leading: const Icon(Icons.delete),
              title: const Text('حذف الزيارة'),
              onTap: () {
                Navigator.pop(context);
                // TODO: Implement delete visit
              },
            ),
          ],
        ),
      ),
    );
  }

  void _showSuccessSnackBar(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: Colors.green,
      ),
    );
  }

  void _showErrorSnackBar(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: Colors.red,
      ),
    );
  }
}

class NewVisitBottomSheet extends StatefulWidget {
  final List<Customer> customers;

  const NewVisitBottomSheet({Key? key, required this.customers}) : super(key: key);

  @override
  State<NewVisitBottomSheet> createState() => _NewVisitBottomSheetState();
}

class _NewVisitBottomSheetState extends State<NewVisitBottomSheet> {
  final _formKey = GlobalKey<FormState>();
  final _notesController = TextEditingController();
  final _purposeController = TextEditingController();
  
  Customer? _selectedCustomer;
  String _visitType = 'planned';
  bool _isLoading = false;
  Position? _currentLocation;

  @override
  void initState() {
    super.initState();
    _getCurrentLocation();
  }

  Future<void> _getCurrentLocation() async {
    final location = await LocationService.instance.getCurrentLocation();
    setState(() {
      _currentLocation = location;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      height: MediaQuery.of(context).size.height * 0.8,
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.amber[700],
              borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
            ),
            child: Row(
              children: [
                const Icon(Icons.add_location, color: Colors.white),
                const SizedBox(width: 8),
                const Text(
                  'زيارة جديدة',
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const Spacer(),
                IconButton(
                  icon: const Icon(Icons.close, color: Colors.white),
                  onPressed: () => Navigator.pop(context),
                ),
              ],
            ),
          ),
          
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Customer Selection
                    const Text(
                      'العميل',
                      style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 8),
                    DropdownButtonFormField<Customer>(
                      value: _selectedCustomer,
                      decoration: InputDecoration(
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
                        hintText: 'اختر العميل',
                      ),
                      items: widget.customers.map((customer) {
                        return DropdownMenuItem(
                          value: customer,
                          child: Text(customer.name),
                        );
                      }).toList(),
                      onChanged: (customer) {
                        setState(() {
                          _selectedCustomer = customer;
                        });
                      },
                      validator: (value) {
                        if (value == null) {
                          return 'يرجى اختيار العميل';
                        }
                        return null;
                      },
                    ),
                    
                    const SizedBox(height: 16),
                    
                    // Visit Type
                    const Text(
                      'نوع الزيارة',
                      style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 8),
                    DropdownButtonFormField<String>(
                      value: _visitType,
                      decoration: InputDecoration(
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
                      ),
                      items: const [
                        DropdownMenuItem(value: 'planned', child: Text('مخططة')),
                        DropdownMenuItem(value: 'unplanned', child: Text('غير مخططة')),
                        DropdownMenuItem(value: 'follow_up', child: Text('متابعة')),
                        DropdownMenuItem(value: 'collection', child: Text('تحصيل')),
                      ],
                      onChanged: (value) {
                        setState(() {
                          _visitType = value!;
                        });
                      },
                    ),
                    
                    const SizedBox(height: 16),
                    
                    // Visit Purpose
                    const Text(
                      'غرض الزيارة',
                      style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 8),
                    TextFormField(
                      controller: _purposeController,
                      decoration: InputDecoration(
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
                        hintText: 'اكتب غرض الزيارة',
                      ),
                      maxLines: 2,
                    ),
                    
                    const SizedBox(height: 16),
                    
                    // Visit Notes
                    const Text(
                      'ملاحظات الزيارة',
                      style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 8),
                    TextFormField(
                      controller: _notesController,
                      decoration: InputDecoration(
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
                        hintText: 'اكتب ملاحظاتك هنا',
                      ),
                      maxLines: 3,
                    ),
                    
                    const SizedBox(height: 16),
                    
                    // Location Info
                    if (_currentLocation != null)
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: Colors.green.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(color: Colors.green.withOpacity(0.3)),
                        ),
                        child: Row(
                          children: [
                            const Icon(Icons.location_on, color: Colors.green),
                            const SizedBox(width: 8),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  const Text(
                                    'تم تحديد الموقع الحالي',
                                    style: TextStyle(
                                      color: Colors.green,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  Text(
                                    'خط العرض: ${_currentLocation!.latitude.toStringAsFixed(6)}',
                                    style: const TextStyle(fontSize: 12),
                                  ),
                                  Text(
                                    'خط الطول: ${_currentLocation!.longitude.toStringAsFixed(6)}',
                                    style: const TextStyle(fontSize: 12),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      )
                    else
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: Colors.orange.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(color: Colors.orange.withOpacity(0.3)),
                        ),
                        child: Row(
                          children: [
                            const Icon(Icons.location_off, color: Colors.orange),
                            const SizedBox(width: 8),
                            const Expanded(
                              child: Text(
                                'جاري تحديد الموقع...',
                                style: TextStyle(color: Colors.orange),
                              ),
                            ),
                            IconButton(
                              icon: const Icon(Icons.refresh, color: Colors.orange),
                              onPressed: _getCurrentLocation,
                            ),
                          ],
                        ),
                      ),
                  ],
                ),
              ),
            ),
          ),
          
          // Submit Button
          Container(
            padding: const EdgeInsets.all(16),
            child: SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: _isLoading ? null : _submitVisit,
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.amber[700],
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                ),
                child: _isLoading
                    ? const SizedBox(
                        height: 20,
                        width: 20,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                        ),
                      )
                    : const Text(
                        'حفظ الزيارة',
                        style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                      ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Future<void> _submitVisit() async {
    if (!_formKey.currentState!.validate()) return;
    
    setState(() => _isLoading = true);
    
    try {
      final visit = CustomerVisit(
        salesRepresentativeId: 1, // TODO: Get from auth provider
        customerId: _selectedCustomer!.id,
        visitDate: DateTime.now(),
        latitude: _currentLocation?.latitude,
        longitude: _currentLocation?.longitude,
        visitType: _visitType,
        visitStatus: 'completed',
        visitPurpose: _purposeController.text.isNotEmpty ? _purposeController.text : null,
        visitNotes: _notesController.text.isNotEmpty ? _notesController.text : null,
        checkInTime: DateTime.now(),
      );
      
      Navigator.pop(context, visit);
    } catch (e) {
      setState(() => _isLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('خطأ في حفظ الزيارة: $e'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  @override
  void dispose() {
    _notesController.dispose();
    _purposeController.dispose();
    super.dispose();
  }
}
