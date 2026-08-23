import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user_model.dart';
import '../models/product_model.dart';
import '../models/category_model.dart';
import '../models/banner_model.dart';
import '../models/cart_model.dart';
import '../models/order_model.dart';

class ApiService {
  // Current Installed Mobile App Version
  static const String currentAppVersion = '1.1.1';

  // Fixed Production Backend API URL (budayakita.com)
  static const String baseUrl = 'https://budayakita.com/api/v1';

  // Global Maintenance Interception & Auto-Logout Callback
  static bool isMaintenanceRedirecting = false;
  static void Function(String title, String message)? onMaintenanceDetected;

  static void triggerMaintenanceRedirect({String? title, String? message}) {
    if (isMaintenanceRedirecting) return;
    isMaintenanceRedirecting = true;
    clearToken();
    if (onMaintenanceDetected != null) {
      onMaintenanceDetected!(
        title ?? 'Mode Pemeliharaan & Pengembangan 🛠️',
        message ?? 'Aplikasi NitipDong sedang dalam tahap pemeliharaan sistem. Silakan coba kembali beberapa saat lagi.',
      );
    }
  }

  static void resetMaintenanceState() {
    isMaintenanceRedirecting = false;
  }

  static void checkResponseForMaintenance(http.Response response) {
    if (response.statusCode == 503) {
      String title = 'Mode Pemeliharaan & Pengembangan 🛠️';
      String message = 'Aplikasi NitipDong sedang dalam tahap pemeliharaan sistem. Silakan coba kembali beberapa saat lagi.';
      try {
        final data = jsonDecode(response.body);
        if (data['maintenance_title'] != null) title = data['maintenance_title'];
        if (data['maintenance_message'] != null) message = data['maintenance_message'];
      } catch (_) {}
      triggerMaintenanceRedirect(title: title, message: message);
    }
  }

  static Future<String> getBaseUrl() async {
    return baseUrl;
  }

  // Token Management
  static Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('auth_token');
  }

  static Future<void> setToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('auth_token', token);
  }

  static Future<void> clearToken() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
  }

  // ══════════════════════════════════════════════════
  // ADDRESS & LOCATION PERSISTENCE (ANTI-HILANG)
  // ══════════════════════════════════════════════════
  static Future<Map<String, String>> getSavedAddress() async {
    final prefs = await SharedPreferences.getInstance();
    String address = prefs.getString('saved_address_full') ?? '';
    String name = prefs.getString('saved_address_recipient') ?? '';
    String phone = prefs.getString('saved_address_phone') ?? '';
    String city = prefs.getString('saved_address_city') ?? '';

    // If local storage is empty, try to fetch from API if logged in
    if (address.isEmpty) {
      final remote = await fetchRemoteAddress();
      if (remote != null && remote['full_address'] != null && (remote['full_address'] as String).isNotEmpty) {
        return {
          'full_address': remote['full_address'] ?? '',
          'recipient_name': remote['recipient_name'] ?? '',
          'phone': remote['phone'] ?? '',
          'city': remote['city'] ?? '',
        };
      }
      
      // Default fallback
      return {
        'full_address': 'Jl. Raya Darmo No. 42, Wonokromo, Surabaya, Jawa Timur 60241',
        'recipient_name': 'Mohammad Kevin Arif Rudianto',
        'phone': '081234567890',
        'city': 'Surabaya',
      };
    }

    return {
      'full_address': address,
      'recipient_name': name,
      'phone': phone,
      'city': city,
    };
  }

  static Future<void> saveAddress({
    required String fullAddress,
    String? recipientName,
    String? phone,
    String? city,
    String? notes,
  }) async {
    final prefs = await SharedPreferences.getInstance();
    if (fullAddress.isNotEmpty) {
      await prefs.setString('saved_address_full', fullAddress.trim());
    }
    if (recipientName != null && recipientName.isNotEmpty) {
      await prefs.setString('saved_address_recipient', recipientName.trim());
    }
    if (phone != null && phone.isNotEmpty) {
      await prefs.setString('saved_address_phone', phone.trim());
    }
    if (city != null && city.isNotEmpty) {
      await prefs.setString('saved_address_city', city.trim());
    }

    // Sync to backend API if user is authenticated
    try {
      final token = await getToken();
      if (token != null && token.isNotEmpty) {
        await http.post(
          Uri.parse('$baseUrl/addresses'),
          headers: await _getHeaders(withAuth: true),
          body: jsonEncode({
            'full_address': fullAddress.trim(),
            'recipient_name': recipientName ?? prefs.getString('saved_address_recipient') ?? 'Pengguna NitipDong',
            'phone': phone ?? prefs.getString('saved_address_phone') ?? '081234567890',
            'city': city ?? prefs.getString('saved_address_city') ?? 'Surabaya',
            'notes': notes ?? '',
          }),
        ).timeout(const Duration(seconds: 4));
      }
    } catch (_) {}
  }

  static Future<void> saveAddressLocally({
    required String fullAddress,
    String? recipientName,
    String? phone,
    String? city,
    String? notes,
  }) async {
    return saveAddress(
      fullAddress: fullAddress,
      recipientName: recipientName,
      phone: phone,
      city: city,
      notes: notes,
    );
  }

  static Future<Map<String, dynamic>?> fetchRemoteAddress() async {
    try {
      final token = await getToken();
      if (token == null || token.isEmpty) return null;

      final response = await http
          .get(
            Uri.parse('$baseUrl/addresses/primary'),
            headers: await _getHeaders(withAuth: true),
          )
          .timeout(const Duration(seconds: 3));

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true && data['address'] != null) {
          final addr = data['address'];
          final prefs = await SharedPreferences.getInstance();
          if (addr['full_address'] != null) {
            await prefs.setString('saved_address_full', addr['full_address']);
          }
          if (addr['recipient_name'] != null) {
            await prefs.setString('saved_address_recipient', addr['recipient_name']);
          }
          if (addr['phone'] != null) {
            await prefs.setString('saved_address_phone', addr['phone']);
          }
          if (addr['city'] != null) {
            await prefs.setString('saved_address_city', addr['city']);
          }
          return addr;
        }
      }
    } catch (_) {}
    return null;
  }

  static Future<Map<String, String>> _getHeaders({bool withAuth = true}) async {
    final headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };

    if (withAuth) {
      final token = await getToken();
      if (token != null && token.isNotEmpty) {
        headers['Authorization'] = 'Bearer $token';
      }
    }

    return headers;
  }

  // ══════════════════════════════════════════════════
  // SYSTEM STATUS & MAINTENANCE MODE
  // ══════════════════════════════════════════════════
  static Future<Map<String, dynamic>> checkSystemStatus() async {
    try {
      final response = await http
          .get(
            Uri.parse('$baseUrl/system/status'),
            headers: await _getHeaders(withAuth: false),
          )
          .timeout(const Duration(seconds: 3));

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      } else if (response.statusCode == 503) {
        // Server is in maintenance mode
        try {
          final data = jsonDecode(response.body);
          return {
            'success': true,
            'is_maintenance': true,
            'maintenance_title': data['maintenance_title'] ?? 'Mode Pemeliharaan & Pengembangan 🛠️',
            'maintenance_message': data['maintenance_message'] ?? 'Aplikasi NitipDong sedang dalam tahap pemeliharaan sistem. Silakan coba kembali beberapa saat lagi.',
          };
        } catch (_) {
          return {
            'success': true,
            'is_maintenance': true,
            'maintenance_title': 'Mode Pemeliharaan & Pengembangan 🛠️',
            'maintenance_message': 'Aplikasi NitipDong sedang dalam tahap pemeliharaan sistem. Silakan coba kembali beberapa saat lagi.',
          };
        }
      }
    } catch (_) {}
    return {'success': false, 'is_maintenance': false};
  }

  // ══════════════════════════════════════════════════
  // AUTHENTICATION & OTP
  // ══════════════════════════════════════════════════
  static Future<Map<String, dynamic>> login(String loginIdentifier, String password) async {
    try {
      final response = await http
          .post(
            Uri.parse('$baseUrl/auth/login'),
            headers: await _getHeaders(withAuth: false),
            body: jsonEncode({'login': loginIdentifier.trim(), 'password': password}),
          )
          .timeout(const Duration(seconds: 5));

      checkResponseForMaintenance(response);
      final data = jsonDecode(response.body);
      if (response.statusCode == 200 && data['success'] == true) {
        await setToken(data['token']);
        return {
          'success': true,
          'token': data['token'],
          'user': UserModel.fromJson(data['user']),
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Gagal masuk. Periksa email/nomor HP & kata sandi Anda.',
        };
      }
    } catch (_) {
      return {
        'success': false,
        'message': 'Gagal terhubung ke server. Periksa koneksi internet Anda.',
      };
    }
  }

  static Future<Map<String, dynamic>> register(
      String name, String email, String password, String passwordConfirmation,
      {String? phone}) async {
    try {
      final payload = <String, dynamic>{
        'name': name.trim(),
        'email': email.trim(),
        'password': password,
        'password_confirmation': passwordConfirmation,
      };
      if (phone != null && phone.isNotEmpty) {
        payload['phone'] = phone.trim();
      }

      final response = await http
          .post(
            Uri.parse('$baseUrl/auth/register'),
            headers: await _getHeaders(withAuth: false),
            body: jsonEncode(payload),
          )
          .timeout(const Duration(seconds: 5));

      checkResponseForMaintenance(response);
      final data = jsonDecode(response.body);
      if (response.statusCode == 201 && data['success'] == true) {
        if (data['token'] != null) {
          await setToken(data['token']);
        }
        return {
          'success': true,
          'token': data['token'],
          'user': UserModel.fromJson(data['user']),
          'otp_preview': data['otp_preview'],
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Gagal mendaftar. Silakan coba lagi.',
        };
      }
    } catch (_) {
      return {
        'success': false,
        'message': 'Gagal terhubung ke server. Periksa koneksi internet Anda.',
      };
    }
  }

  static Future<Map<String, dynamic>> verifyOtp(String identifier, String otpCode) async {
    try {
      final response = await http
          .post(
            Uri.parse('$baseUrl/auth/verify-otp'),
            headers: await _getHeaders(withAuth: false),
            body: jsonEncode({
              'identifier': identifier.trim(),
              'otp_code': otpCode.trim(),
            }),
          )
          .timeout(const Duration(seconds: 5));

      checkResponseForMaintenance(response);
      final data = jsonDecode(response.body);
      if (response.statusCode == 200 && data['success'] == true) {
        if (data['token'] != null) {
          await setToken(data['token']);
        }
        return {
          'success': true,
          'token': data['token'],
          'user': UserModel.fromJson(data['user']),
          'message': data['message'],
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Kode OTP salah atau telah kedaluwarsa.',
        };
      }
    } catch (_) {
      return {
        'success': false,
        'message': 'Gagal memverifikasi OTP. Periksa koneksi internet Anda.',
      };
    }
  }

  static Future<Map<String, dynamic>> resendOtp(String identifier) async {
    try {
      final response = await http
          .post(
            Uri.parse('$baseUrl/auth/resend-otp'),
            headers: await _getHeaders(withAuth: false),
            body: jsonEncode({'identifier': identifier.trim()}),
          )
          .timeout(const Duration(seconds: 5));

      checkResponseForMaintenance(response);
      final data = jsonDecode(response.body);
      return {
        'success': response.statusCode == 200 && data['success'] == true,
        'message': data['message'] ?? 'Kode OTP baru telah dikirimkan.',
        'cooldown_seconds': data['cooldown_seconds'] ?? 60,
      };
    } catch (_) {
      return {
        'success': false,
        'message': 'Gagal mengirim ulang OTP.',
        'cooldown_seconds': 60,
      };
    }
  }

  static Future<UserModel?> getProfile() async {
    try {
      final response = await http
          .get(
            Uri.parse('$baseUrl/auth/profile'),
            headers: await _getHeaders(),
          )
          .timeout(const Duration(seconds: 3));

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return UserModel.fromJson(data['user']);
      }
    } catch (_) {}
    return null;
  }

  static Future<void> logout() async {
    try {
      await http.post(
        Uri.parse('$baseUrl/auth/logout'),
        headers: await _getHeaders(),
      );
    } catch (_) {}
    await clearToken();
  }

  // ══════════════════════════════════════════════════
  // HOME: BANNERS, CATEGORIES, FLASH SALE
  // ══════════════════════════════════════════════════
  static Future<List<BannerModel>> getBanners() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/banners'),
        headers: await _getHeaders(withAuth: false),
      );

      checkResponseForMaintenance(response);
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return (data['data'] as List).map((b) => BannerModel.fromJson(b)).toList();
      }
    } catch (_) {}
    return [];
  }

  static Future<List<CategoryModel>> getCategories() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/categories'),
        headers: await _getHeaders(withAuth: false),
      );

      checkResponseForMaintenance(response);
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return (data['data'] as List).map((c) => CategoryModel.fromJson(c)).toList();
      }
    } catch (_) {}
    return [];
  }

  static Future<Map<String, dynamic>> getFlashSale() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/products/flash-sale'),
        headers: await _getHeaders(withAuth: false),
      );

      checkResponseForMaintenance(response);
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true) {
          final items = (data['data'] as List).map((p) => ProductModel.fromJson(p)).toList();
          return {
            'success': true,
            'title': data['title'] ?? 'Flash Sale Kilat',
            'remaining_seconds': data['remaining_seconds'] ?? 3600,
            'items': items,
          };
        }
      }
    } catch (_) {}
    return {'success': false, 'items': <ProductModel>[]};
  }

  // ══════════════════════════════════════════════════
  // PRODUCTS CATALOG & DETAIL
  // ══════════════════════════════════════════════════
  static Future<List<ProductModel>> getProducts({
    String? category,
    String? q,
    dynamic storeId,
    String sort = 'latest',
    int page = 1,
  }) async {
    try {
      final params = <String, String>{
        'sort': sort,
        'page': page.toString(),
      };
      if (category != null && category.isNotEmpty) params['category'] = category;
      if (q != null && q.isNotEmpty) params['q'] = q;
      if (storeId != null) params['store_id'] = storeId.toString();

      final uri = Uri.parse('$baseUrl/products').replace(queryParameters: params);
      final response = await http.get(uri, headers: await _getHeaders(withAuth: false));

      checkResponseForMaintenance(response);
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return (data['data'] as List).map((p) => ProductModel.fromJson(p)).toList();
      }
    } catch (_) {}
    return [];
  }

  static Future<ProductModel?> getProductDetail(dynamic id) async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/products/$id'),
        headers: await _getHeaders(withAuth: false),
      );

      checkResponseForMaintenance(response);
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return ProductModel.fromJson(data['data']);
      }
    } catch (_) {}
    return null;
  }

  // ══════════════════════════════════════════════════
  // CART OPERATIONS
  // ══════════════════════════════════════════════════
  static Future<Map<String, dynamic>> getCart() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/cart'),
        headers: await _getHeaders(),
      );

      checkResponseForMaintenance(response);
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final items = (data['items'] as List).map((c) => CartItemModel.fromJson(c)).toList();
        return {
          'success': true,
          'items': items,
          'subtotal': (data['subtotal'] as num?)?.toDouble() ?? 0.0,
          'item_count': data['item_count'] ?? 0,
        };
      }
    } catch (_) {}
    return {'success': false, 'items': <CartItemModel>[], 'subtotal': 0.0};
  }

  static Future<bool> addToCart(int productId, int quantity, {String? variant}) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/cart'),
        headers: await _getHeaders(),
        body: jsonEncode({
          'product_id': productId,
          'quantity': quantity,
          'variant': variant,
        }),
      );

      return response.statusCode == 200;
    } catch (_) {
      return false;
    }
  }

  static Future<bool> updateCartItem(int cartId, int quantity) async {
    try {
      final response = await http.put(
        Uri.parse('$baseUrl/cart/$cartId'),
        headers: await _getHeaders(),
        body: jsonEncode({'quantity': quantity}),
      );

      return response.statusCode == 200;
    } catch (_) {
      return false;
    }
  }

  static Future<bool> removeCartItem(int cartId) async {
    try {
      final response = await http.delete(
        Uri.parse('$baseUrl/cart/$cartId'),
        headers: await _getHeaders(),
      );

      return response.statusCode == 200;
    } catch (_) {
      return false;
    }
  }

  // ══════════════════════════════════════════════════
  // ORDERS & CHECKOUT
  // ══════════════════════════════════════════════════
  static Future<List<OrderModel>> getOrders({String status = 'all'}) async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/orders?status=$status'),
        headers: await _getHeaders(),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return (data['data'] as List).map((o) => OrderModel.fromJson(o)).toList();
      }
    } catch (_) {}
    return [];
  }

  static Future<Map<String, dynamic>> checkout({
    required String shippingAddress,
    required String paymentMethod,
    String courier = 'J&T Express',
    List<int>? cartIds,
    String? voucherCode,
  }) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/orders/checkout'),
        headers: await _getHeaders(),
        body: jsonEncode({
          'shipping_address': shippingAddress,
          'payment_method': paymentMethod,
          'courier': courier,
          'cart_ids': cartIds,
          'voucher_code': voucherCode,
        }),
      );

      final data = jsonDecode(response.body);
      return {
        'success': response.statusCode == 201 && data['success'] == true,
        'message': data['message'] ?? 'Pesanan berhasil diproses',
        'order_id': data['order_id'],
        'order_number': data['order_number'],
      };
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  static Future<Map<String, dynamic>> payOrder(dynamic orderId) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/orders/$orderId/pay'),
        headers: await _getHeaders(),
      );
      final data = jsonDecode(response.body);
      return {
        'success': response.statusCode == 200 && data['success'] == true,
        'message': data['message'] ?? 'Pembayaran berhasil diproses',
      };
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  static Future<Map<String, dynamic>> validateVoucher(String voucherCode) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/vouchers/validate'),
        headers: await _getHeaders(),
        body: jsonEncode({
          'voucher_code': voucherCode,
        }),
      );
      final data = jsonDecode(response.body);
      return {
        'success': response.statusCode == 200 && data['success'] == true,
        'message': data['message'] ?? 'Kupon berhasil diterapkan',
        'discount_amount': data['data'] != null ? (data['data']['discount_amount'] as num).toDouble() : 0.0,
        'code': data['data'] != null ? data['data']['code'] as String : null,
      };
    } catch (e) {
      return {'success': false, 'message': e.toString(), 'discount_amount': 0.0};
    }
  }

  static Future<Map<String, dynamic>> postDiscussion(dynamic productId, String body) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/products/$productId/discussions'),
        headers: await _getHeaders(),
        body: jsonEncode({'body': body}),
      );
      final data = jsonDecode(response.body);
      return {
        'success': response.statusCode == 201 && data['success'] == true,
        'message': data['message'] ?? 'Pertanyaan berhasil dikirim',
      };
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  static Future<Map<String, dynamic>> postReply(dynamic productId, dynamic discussionId, String body) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/products/$productId/discussions/$discussionId/reply'),
        headers: await _getHeaders(),
        body: jsonEncode({'body': body}),
      );
      final data = jsonDecode(response.body);
      return {
        'success': response.statusCode == 201 && data['success'] == true,
        'message': data['message'] ?? 'Balasan berhasil dikirim',
      };
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  // ══════════════════════════════════════════════════
  // MIDTRANS CORE API DIRECT PAYMENT & POLLING
  // ══════════════════════════════════════════════════

  /// Request Direct Payment Charge (QRIS, BCA VA, BRI VA, Mandiri Bill, ShopeePay)
  static Future<Map<String, dynamic>> chargeMidtransCore({
    required dynamic orderId,
    required String paymentMethod,
  }) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/payment/midtrans/charge'),
        headers: await _getHeaders(),
        body: jsonEncode({
          'order_id': orderId,
          'payment_method': paymentMethod,
        }),
      );

      checkResponseForMaintenance(response);
      final data = jsonDecode(response.body);
      return data;
    } catch (e) {
      return {
        'success': false,
        'message': 'Gagal menghubungkan ke Midtrans Payment Gateway: $e',
      };
    }
  }

  /// Real-time Polling to check if transaction has settled
  static Future<Map<String, dynamic>> checkPaymentStatus(dynamic orderId) async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/orders/$orderId/payment-status'),
        headers: await _getHeaders(),
      );

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      }
      return {'success': false, 'is_paid': false};
    } catch (_) {
      return {'success': false, 'is_paid': false};
    }
  }

  /// Instant Demo / Sandbox Testing Settlement
  static Future<bool> simulatePaymentSuccess(dynamic orderId) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/orders/$orderId/simulate-paid'),
        headers: await _getHeaders(),
      );
      final data = jsonDecode(response.body);
      return response.statusCode == 200 && data['success'] == true;
    } catch (_) {
      return false;
    }
  }

  // ══════════════════════════════════════════════════
  // AI CUSTOMER ASSISTANT (GEMINI INTEGRATION)
  // ══════════════════════════════════════════════════

  /// Send message to NitipDong AI Customer Assistant
  static Future<Map<String, dynamic>> sendAiChatMessage(String message) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/ai-chat'),
        headers: await _getHeaders(),
        body: jsonEncode({'message': message}),
      );
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return {
          'success': true,
          'reply': data['reply'] ?? 'Halo! Ada yang bisa saya bantu terkait belanja dan jastip di NitipDong?',
        };
      }
      return {
        'success': false,
        'reply': 'Maaf, sistem asisten AI sedang sibuk. Silakan coba beberapa saat lagi.',
      };
    } catch (e) {
      return {
        'success': false,
        'reply': 'Koneksi ke asisten AI terputus. Silakan periksa koneksi internet Anda.',
      };
    }
  }

  // ══════════════════════════════════════════════════
  // NITIPPAY WALLET (DOMPET DIGITAL)
  // ══════════════════════════════════════════════════

  /// Fetch wallet balance & transaction history
  static Future<Map<String, dynamic>> getWalletData() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/wallet'),
        headers: await _getHeaders(),
      );
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return data['data'] ?? {};
      }
      return {
        'balance': 250000.0,
        'points': 1250,
        'is_active': true,
        'transactions': [],
      };
    } catch (_) {
      return {
        'balance': 250000.0,
        'points': 1250,
        'is_active': true,
        'transactions': [],
      };
    }
  }

  /// Top Up Wallet Balance
  static Future<Map<String, dynamic>> topUpWallet(double amount, {String paymentMethod = 'QRIS Instant'}) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/wallet/topup'),
        headers: await _getHeaders(),
        body: jsonEncode({
          'amount': amount,
          'payment_method': paymentMethod,
        }),
      );
      final data = jsonDecode(response.body);
      return {
        'success': response.statusCode == 200 && data['success'] == true,
        'message': data['message'] ?? 'Top Up berhasil!',
        'new_balance': data['new_balance'],
      };
    } catch (e) {
      return {
        'success': false,
        'message': 'Gagal melakukan Top Up: $e',
      };
    }
  }

  // ══════════════════════════════════════════════════
  // PROMO COUPONS / VOUCHERS
  // ══════════════════════════════════════════════════

  /// Get active vouchers with expiration dates
  static Future<List<Map<String, dynamic>>> getAvailableVouchers() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/vouchers/available'),
        headers: await _getHeaders(),
      );
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['data'] != null && data['data'] is List) {
          return List<Map<String, dynamic>>.from(data['data']);
        }
      }
      return [];
    } catch (_) {
      return [];
    }
  }

  // ══════════════════════════════════════════════════
  // ORDER ACTIONS & TRACKING
  // ══════════════════════════════════════════════════

  /// Cancel an order
  static Future<Map<String, dynamic>> cancelOrder(dynamic orderId, {String reason = 'Dibatalkan oleh pembeli'}) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/orders/$orderId/cancel'),
        headers: await _getHeaders(),
        body: jsonEncode({'reason': reason}),
      );
      final data = jsonDecode(response.body);
      return {
        'success': response.statusCode == 200 && data['success'] == true,
        'message': data['message'] ?? 'Pesanan berhasil dibatalkan.',
      };
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  /// Customer confirms order received / completed
  static Future<Map<String, dynamic>> confirmOrderReceived(dynamic orderId) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/orders/$orderId/confirm'),
        headers: await _getHeaders(),
      );
      final data = jsonDecode(response.body);
      return {
        'success': response.statusCode == 200 && data['success'] == true,
        'message': data['message'] ?? 'Pesanan berhasil diselesaikan.',
      };
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  /// Get order tracking timeline
  static Future<Map<String, dynamic>?> getOrderTracking(dynamic orderId) async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/orders/$orderId/tracking'),
        headers: await _getHeaders(),
      );
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return data['data'];
      }
      return null;
    } catch (_) {
      return null;
    }
  }

  /// Submit review for product in completed order
  static Future<Map<String, dynamic>> submitOrderReview(dynamic orderId, int productId, int rating, String comment) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/orders/$orderId/reviews'),
        headers: await _getHeaders(),
        body: jsonEncode({
          'product_id': productId,
          'rating': rating,
          'comment': comment,
        }),
      );
      final data = jsonDecode(response.body);
      return {
        'success': response.statusCode == 200 && data['success'] == true,
        'message': data['message'] ?? 'Ulasan berhasil dikirim!',
      };
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  // ══════════════════════════════════════════════════
  // COURIER OPERATIONS
  // ══════════════════════════════════════════════════

  /// Get list of orders ready for courier delivery
  static Future<List<Map<String, dynamic>>> getCourierOrders() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/courier/orders'),
        headers: await _getHeaders(),
      );
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['data'] != null && data['data'] is List) {
          return List<Map<String, dynamic>>.from(data['data']);
        }
      }
      return [];
    } catch (_) {
      return [];
    }
  }

  /// Mark order as picked up by courier (shipped)
  static Future<Map<String, dynamic>> pickupCourierOrder(dynamic orderId) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/courier/orders/$orderId/pickup'),
        headers: await _getHeaders(),
      );
      final data = jsonDecode(response.body);
      return {
        'success': response.statusCode == 200 && data['success'] == true,
        'message': data['message'] ?? 'Order berhasil diambil!',
        'status': data['status'],
        'tracking_number': data['tracking_number'],
      };
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  /// Mark order as delivered by courier (completed)
  static Future<Map<String, dynamic>> deliverCourierOrder(dynamic orderId) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/courier/orders/$orderId/deliver'),
        headers: await _getHeaders(),
      );
      final data = jsonDecode(response.body);
      return {
        'success': response.statusCode == 200 && data['success'] == true,
        'message': data['message'] ?? 'Pengiriman selesai!',
        'status': data['status'],
      };
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }
}


