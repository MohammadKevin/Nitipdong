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
  static const String currentAppVersion = '1.0.1';

  // Default API Base URL (Production - budayakita.com)
  // Can be configured dynamically from app UI or loaded from SharedPreferences
  static String baseUrl = 'https://budayakita.com/api/v1';

  static Future<String> getBaseUrl() async {
    final prefs = await SharedPreferences.getInstance();
    final savedUrl = prefs.getString('api_base_url');
    if (savedUrl != null && savedUrl.isNotEmpty) {
      baseUrl = savedUrl;
    }
    return baseUrl;
  }

  static Future<void> setBaseUrl(String url) async {
    baseUrl = url.trim().replaceAll(RegExp(r'/+$'), '');
    if (!baseUrl.endsWith('/api/v1')) {
      if (baseUrl.endsWith('/api')) {
        baseUrl = '$baseUrl/v1';
      } else {
        baseUrl = '$baseUrl/api/v1';
      }
    }
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('api_base_url', baseUrl);
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
          .timeout(const Duration(seconds: 2));

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      }
    } catch (_) {}
    return {'success': false, 'is_maintenance': false};
  }

  // ══════════════════════════════════════════════════
  // AUTHENTICATION
  // ══════════════════════════════════════════════════
  static Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final response = await http
          .post(
            Uri.parse('$baseUrl/auth/login'),
            headers: await _getHeaders(withAuth: false),
            body: jsonEncode({'email': email, 'password': password}),
          )
          .timeout(const Duration(seconds: 5));

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
          'message': data['message'] ?? 'Gagal masuk. Periksa email & kata sandi Anda.',
        };
      }
    } catch (_) {
      return {
        'success': false,
        'message': 'Gagal terhubung ke server. Periksa koneksi atau IP server Anda.',
      };
    }
  }

  static Future<Map<String, dynamic>> register(
      String name, String email, String password, String passwordConfirmation) async {
    try {
      final response = await http
          .post(
            Uri.parse('$baseUrl/auth/register'),
            headers: await _getHeaders(withAuth: false),
            body: jsonEncode({
              'name': name,
              'email': email,
              'password': password,
              'password_confirmation': passwordConfirmation,
            }),
          )
          .timeout(const Duration(seconds: 5));

      final data = jsonDecode(response.body);
      if (response.statusCode == 201 && data['success'] == true) {
        await setToken(data['token']);
        return {
          'success': true,
          'token': data['token'],
          'user': UserModel.fromJson(data['user']),
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
        'message': 'Gagal terhubung ke server. Periksa koneksi atau IP server Anda.',
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
}
