<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HelpController extends Controller
{
    /**
     * عرض صفحة المساعدة الرئيسية
     */
    public function index()
    {
        return view('admin.help.index');
    }

    /**
     * عرض التوثيق
     */
    public function documentation()
    {
        return view('admin.help.documentation');
    }

    /**
     * عرض توثيق API
     */
    public function apiDocs()
    {
        return view('admin.help.api-docs');
    }

    /**
     * عرض سجل التغييرات
     */
    public function changelog()
    {
        return view('admin.help.changelog');
    }

    /**
     * عرض صفحة الدعم
     */
    public function support()
    {
        return view('admin.help.support');
    }
}
