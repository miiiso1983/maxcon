import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/sales_representative.dart';
import '../services/api_service.dart';

class AuthProvider with ChangeNotifier {
  final ApiService _apiService = ApiService();
  
  SalesRepresentative? _currentUser;
  bool _isLoading = false;
  bool _isAuthenticated = false;
  String? _errorMessage;

  SalesRepresentative? get currentUser => _currentUser;
  bool get isLoading => _isLoading;
  bool get isAuthenticated => _isAuthenticated;
  String? get errorMessage => _errorMessage;

  AuthProvider() {
    _checkAuthStatus();
  }

  Future<void> _checkAuthStatus() async {
    _setLoading(true);
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');
      
      if (token != null) {
        // Try to get user profile to verify token is still valid
        _currentUser = await _apiService.getSalesRepProfile();
        _isAuthenticated = true;
      }
    } catch (e) {
      // Token is invalid, clear it
      await _clearAuthData();
    } finally {
      _setLoading(false);
    }
  }

  Future<bool> login(String email, String password) async {
    _setLoading(true);
    _clearError();

    try {
      final response = await _apiService.login(email, password);
      
      if (response['success'] == true && response['user'] != null) {
        _currentUser = SalesRepresentative.fromJson(response['user']);
        _isAuthenticated = true;
        
        // Save user data locally
        await _saveUserData(_currentUser!);
        
        _setLoading(false);
        return true;
      } else {
        _setError(response['message'] ?? 'فشل في تسجيل الدخول');
        _setLoading(false);
        return false;
      }
    } catch (e) {
      _setError('خطأ في الاتصال: ${e.toString()}');
      _setLoading(false);
      return false;
    }
  }

  Future<void> logout() async {
    _setLoading(true);
    
    try {
      await _apiService.logout();
    } catch (e) {
      print('Logout error: $e');
    }
    
    await _clearAuthData();
    _setLoading(false);
  }

  Future<void> _saveUserData(SalesRepresentative user) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('user_data', user.toJson().toString());
  }

  Future<void> _clearAuthData() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
    await prefs.remove('user_data');
    
    _currentUser = null;
    _isAuthenticated = false;
    notifyListeners();
  }

  void _setLoading(bool loading) {
    _isLoading = loading;
    notifyListeners();
  }

  void _setError(String error) {
    _errorMessage = error;
    notifyListeners();
  }

  void _clearError() {
    _errorMessage = null;
    notifyListeners();
  }

  Future<void> refreshProfile() async {
    if (!_isAuthenticated) return;
    
    try {
      _currentUser = await _apiService.getSalesRepProfile();
      await _saveUserData(_currentUser!);
      notifyListeners();
    } catch (e) {
      print('Error refreshing profile: $e');
    }
  }

  // Update user profile locally
  void updateUserProfile(SalesRepresentative updatedUser) {
    _currentUser = updatedUser;
    _saveUserData(updatedUser);
    notifyListeners();
  }

  // Check if user has specific permission
  bool hasPermission(String permission) {
    // Implement permission checking logic based on user role
    return _currentUser?.isActive ?? false;
  }

  // Get user's assigned areas
  List<String> getUserAreas() {
    return _currentUser?.assignedAreas ?? [];
  }

  // Get user's employee code
  String? getEmployeeCode() {
    return _currentUser?.employeeCode;
  }

  // Get user's commission rate
  double getCommissionRate() {
    return _currentUser?.commissionRate ?? 0.0;
  }
}
