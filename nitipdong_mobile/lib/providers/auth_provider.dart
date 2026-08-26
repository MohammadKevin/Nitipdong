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
      if (_user != null) {
        await ApiService.setBiometricEnabledLocally(_user!.biometricEnabled);
        await ApiService.setBiometricTypeLocally(_user!.biometricType);
      }
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

  Future<bool> loginWithGoogle({
    required String email,
    required String name,
    required String googleId,
    String? avatar,
  }) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    final result = await ApiService.loginWithGoogle(
      email: email,
      name: name,
      googleId: googleId,
      avatar: avatar,
    );
    _isLoading = false;

    if (result['success'] == true) {
      _user = result['user'];
      if (_user != null) {
        await ApiService.setBiometricEnabledLocally(_user!.biometricEnabled);
        await ApiService.setBiometricTypeLocally(_user!.biometricType);
      }
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

  Future<bool> updateBiometric(bool enabled, {String? type}) async {
    final success = await ApiService.toggleBiometric(enabled, type: type);
    if (success && _user != null) {
      _user = UserModel(
        id: _user!.id,
        name: _user!.name,
        email: _user!.email,
        phone: _user!.phone,
        role: _user!.role,
        avatarUrl: _user!.avatarUrl,
        biometricEnabled: enabled,
        biometricType: type ?? _user!.biometricType,
        cartCount: _user!.cartCount,
        wishlistCount: _user!.wishlistCount,
        ordersCount: _user!.ordersCount,
      );
      notifyListeners();
    }
    return success;
  }

  Future<void> logout() async {
    await ApiService.setBiometricEnabledLocally(false);
    await ApiService.logout();
    _user = null;
    notifyListeners();
  }
}
