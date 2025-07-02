import 'dart:convert';
import 'dart:io';
import 'package:dio/dio.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/sales_representative.dart';

class ApiService {
  static const String baseUrl = 'http://127.0.0.1:8000/api';
  late Dio _dio;
  String? _authToken;

  ApiService() {
    _dio = Dio(BaseOptions(
      baseUrl: baseUrl,
      connectTimeout: const Duration(seconds: 30),
      receiveTimeout: const Duration(seconds: 30),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    ));

    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        if (_authToken != null) {
          options.headers['Authorization'] = 'Bearer $_authToken';
        }
        handler.next(options);
      },
      onError: (error, handler) {
        print('API Error: ${error.message}');
        handler.next(error);
      },
    ));

    _loadAuthToken();
  }

  Future<void> _loadAuthToken() async {
    final prefs = await SharedPreferences.getInstance();
    _authToken = prefs.getString('auth_token');
  }

  Future<void> _saveAuthToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('auth_token', token);
    _authToken = token;
  }

  Future<void> clearAuthToken() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
    _authToken = null;
  }

  // Authentication
  Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final response = await _dio.post('/auth/login', data: {
        'email': email,
        'password': password,
      });

      if (response.statusCode == 200) {
        final data = response.data;
        if (data['token'] != null) {
          await _saveAuthToken(data['token']);
        }
        return data;
      } else {
        throw Exception('Login failed');
      }
    } catch (e) {
      throw Exception('Login error: $e');
    }
  }

  Future<void> logout() async {
    try {
      await _dio.post('/auth/logout');
    } catch (e) {
      print('Logout error: $e');
    } finally {
      await clearAuthToken();
    }
  }

  // Sales Representative
  Future<SalesRepresentative> getSalesRepProfile() async {
    try {
      final response = await _dio.get('/sales-representative/profile');
      return SalesRepresentative.fromJson(response.data['data']);
    } catch (e) {
      throw Exception('Failed to get profile: $e');
    }
  }

  // Customers
  Future<List<Customer>> getCustomers({String? search}) async {
    try {
      final response = await _dio.get('/customers', queryParameters: {
        if (search != null) 'search': search,
      });
      
      final List<dynamic> data = response.data['data'];
      return data.map((json) => Customer.fromJson(json)).toList();
    } catch (e) {
      throw Exception('Failed to get customers: $e');
    }
  }

  Future<Customer> getCustomer(int id) async {
    try {
      final response = await _dio.get('/customers/$id');
      return Customer.fromJson(response.data['data']);
    } catch (e) {
      throw Exception('Failed to get customer: $e');
    }
  }

  // Products
  Future<List<Product>> getProducts({String? search, String? category}) async {
    try {
      final response = await _dio.get('/products', queryParameters: {
        if (search != null) 'search': search,
        if (category != null) 'category': category,
      });
      
      final List<dynamic> data = response.data['data'];
      return data.map((json) => Product.fromJson(json)).toList();
    } catch (e) {
      throw Exception('Failed to get products: $e');
    }
  }

  // Customer Visits
  Future<CustomerVisit> createVisit(CustomerVisit visit) async {
    try {
      final response = await _dio.post('/visits', data: visit.toJson());
      return CustomerVisit.fromJson(response.data['data']);
    } catch (e) {
      throw Exception('Failed to create visit: $e');
    }
  }

  Future<List<CustomerVisit>> getVisits({
    DateTime? date,
    int? customerId,
    String? status,
  }) async {
    try {
      final response = await _dio.get('/visits', queryParameters: {
        if (date != null) 'date': date.toIso8601String().split('T')[0],
        if (customerId != null) 'customer_id': customerId,
        if (status != null) 'status': status,
      });
      
      final List<dynamic> data = response.data['data'];
      return data.map((json) => CustomerVisit.fromJson(json)).toList();
    } catch (e) {
      throw Exception('Failed to get visits: $e');
    }
  }

  Future<CustomerVisit> updateVisit(int id, CustomerVisit visit) async {
    try {
      final response = await _dio.put('/visits/$id', data: visit.toJson());
      return CustomerVisit.fromJson(response.data['data']);
    } catch (e) {
      throw Exception('Failed to update visit: $e');
    }
  }

  // Sales Orders
  Future<SalesOrder> createOrder(SalesOrder order) async {
    try {
      final response = await _dio.post('/orders', data: order.toJson());
      return SalesOrder.fromJson(response.data['data']);
    } catch (e) {
      throw Exception('Failed to create order: $e');
    }
  }

  Future<List<SalesOrder>> getOrders({
    DateTime? date,
    int? customerId,
    String? status,
  }) async {
    try {
      final response = await _dio.get('/orders', queryParameters: {
        if (date != null) 'date': date.toIso8601String().split('T')[0],
        if (customerId != null) 'customer_id': customerId,
        if (status != null) 'status': status,
      });
      
      final List<dynamic> data = response.data['data'];
      return data.map((json) => SalesOrder.fromJson(json)).toList();
    } catch (e) {
      throw Exception('Failed to get orders: $e');
    }
  }

  Future<SalesOrder> getOrder(int id) async {
    try {
      final response = await _dio.get('/orders/$id');
      return SalesOrder.fromJson(response.data['data']);
    } catch (e) {
      throw Exception('Failed to get order: $e');
    }
  }

  // Payment Collections
  Future<PaymentCollection> createPayment(PaymentCollection payment) async {
    try {
      final response = await _dio.post('/payments', data: payment.toJson());
      return PaymentCollection.fromJson(response.data['data']);
    } catch (e) {
      throw Exception('Failed to create payment: $e');
    }
  }

  Future<List<PaymentCollection>> getPayments({
    DateTime? date,
    int? customerId,
    String? status,
  }) async {
    try {
      final response = await _dio.get('/payments', queryParameters: {
        if (date != null) 'date': date.toIso8601String().split('T')[0],
        if (customerId != null) 'customer_id': customerId,
        if (status != null) 'status': status,
      });
      
      final List<dynamic> data = response.data['data'];
      return data.map((json) => PaymentCollection.fromJson(json)).toList();
    } catch (e) {
      throw Exception('Failed to get payments: $e');
    }
  }

  // Collection Reminders
  Future<CollectionReminder> createReminder(CollectionReminder reminder) async {
    try {
      final response = await _dio.post('/reminders', data: reminder.toJson());
      return CollectionReminder.fromJson(response.data['data']);
    } catch (e) {
      throw Exception('Failed to create reminder: $e');
    }
  }

  Future<List<CollectionReminder>> getReminders({
    DateTime? date,
    int? customerId,
    String? status,
  }) async {
    try {
      final response = await _dio.get('/reminders', queryParameters: {
        if (date != null) 'date': date.toIso8601String().split('T')[0],
        if (customerId != null) 'customer_id': customerId,
        if (status != null) 'status': status,
      });
      
      final List<dynamic> data = response.data['data'];
      return data.map((json) => CollectionReminder.fromJson(json)).toList();
    } catch (e) {
      throw Exception('Failed to get reminders: $e');
    }
  }

  Future<CollectionReminder> updateReminder(int id, CollectionReminder reminder) async {
    try {
      final response = await _dio.put('/reminders/$id', data: reminder.toJson());
      return CollectionReminder.fromJson(response.data['data']);
    } catch (e) {
      throw Exception('Failed to update reminder: $e');
    }
  }

  // Invoices
  Future<List<Invoice>> getInvoices({
    int? customerId,
    String? status,
    DateTime? fromDate,
    DateTime? toDate,
  }) async {
    try {
      final response = await _dio.get('/invoices', queryParameters: {
        if (customerId != null) 'customer_id': customerId,
        if (status != null) 'status': status,
        if (fromDate != null) 'from_date': fromDate.toIso8601String().split('T')[0],
        if (toDate != null) 'to_date': toDate.toIso8601String().split('T')[0],
      });
      
      final List<dynamic> data = response.data['data'];
      return data.map((json) => Invoice.fromJson(json)).toList();
    } catch (e) {
      throw Exception('Failed to get invoices: $e');
    }
  }

  Future<Invoice> getInvoice(int id) async {
    try {
      final response = await _dio.get('/invoices/$id');
      return Invoice.fromJson(response.data['data']);
    } catch (e) {
      throw Exception('Failed to get invoice: $e');
    }
  }

  Future<String> getInvoicePdf(int id) async {
    try {
      final response = await _dio.get('/invoices/$id/pdf');
      return response.data['pdf_url'];
    } catch (e) {
      throw Exception('Failed to get invoice PDF: $e');
    }
  }

  // Dashboard Statistics
  Future<Map<String, dynamic>> getDashboardStats() async {
    try {
      final response = await _dio.get('/dashboard/stats');
      return response.data['data'];
    } catch (e) {
      throw Exception('Failed to get dashboard stats: $e');
    }
  }

  // File Upload
  Future<String> uploadFile(File file, String type) async {
    try {
      String fileName = file.path.split('/').last;
      FormData formData = FormData.fromMap({
        'file': await MultipartFile.fromFile(file.path, filename: fileName),
        'type': type,
      });

      final response = await _dio.post('/upload', data: formData);
      return response.data['file_url'];
    } catch (e) {
      throw Exception('Failed to upload file: $e');
    }
  }
}
