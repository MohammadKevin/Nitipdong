import 'package:flutter/material.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';

class AuthProvider with ChangeNotifier {
  UserModel? _user;
  bool _isLoading = false;
  String? _errorMessage;

  UserModel? get user => _user;
  bool get isAuthenticated => _user != null;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  void clearError() {
    _errorMessage = null;
    notifyListeners();
  }

  Future<void> checkAuth() async {
    _isLoading = true;
    notifyListeners();

    final token = await ApiService.getToken();
    if (token != null && token.isNotEmpty) {
      _user = await ApiService.getProfile();
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<bool> login(String loginIdentifier, String password) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    final result = await ApiService.login(loginIdentifier, password);
    _isLoading = false;

    if (result['success'] == true) {
      _user = result['user'];
      notifyListeners();
      return true;
    } else {
      _errorMessage = result['message'];
      notifyListeners();
      return false;
    }
  }

  Future<Map<String, dynamic>> register(
      String name, String email, String password, String passwordConfirmation,
      {String? phone}) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    final result = await ApiService.register(name, email, password, passwordConfirmation, phone: phone);
    _isLoading = false;

    if (result['success'] == true) {
      _user = result['user'];
      notifyListeners();
      return {
        'success': true,
        'user': _user,
        'otp_preview': result['otp_preview'],
      };
    } else {
      _errorMessage = result['message'];
      notifyListeners();
      return {
        'success': false,
        'message': _errorMessage,
      };
    }
  }

  Future<bool> verifyOtp(String identifier, String otpCode) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    final result = await ApiService.verifyOtp(identifier, otpCode);
    _isLoading = false;

    if (result['success'] == true) {
      _user = result['user'];
      notifyListeners();
      return true;
    } else {
      _errorMessage = result['message'];
      notifyListeners();
      return false;
    }
  }

  Future<Map<String, dynamic>> resendOtp(String identifier) async {
    _errorMessage = null;
    final result = await ApiService.resendOtp(identifier);
    if (result['success'] != true) {
      _errorMessage = result['message'];
      notifyListeners();
    }
    return result;
  }

  Future<void> logout() async {
    await ApiService.logout();
    _user = null;
    notifyListeners();
  }
}
