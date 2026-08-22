import 'package:flutter/material.dart';
import '../models/cart_model.dart';
import '../services/api_service.dart';

class CartProvider with ChangeNotifier {
  List<CartItemModel> _items = [];
  double _subtotal = 0.0;
  int _itemCount = 0;
  bool _isLoading = false;

  List<CartItemModel> get items => _items;
  double get subtotal => _subtotal;
  int get itemCount => _itemCount;
  bool get isLoading => _isLoading;

  Future<void> fetchCart() async {
    _isLoading = true;
    notifyListeners();

    final result = await ApiService.getCart();
    if (result['success'] == true) {
      _items = result['items'];
      _subtotal = result['subtotal'];
      _itemCount = result['item_count'];
    }

    _isLoading = false;
    notifyListeners();
  }

  Future<bool> addToCart(int productId, int quantity, {String? variant}) async {
    final success = await ApiService.addToCart(productId, quantity, variant: variant);
    if (success) {
      await fetchCart();
    }
    return success;
  }

  Future<void> updateQuantity(int cartId, int newQty) async {
    final success = await ApiService.updateCartItem(cartId, newQty);
    if (success) {
      await fetchCart();
    }
  }

  Future<void> removeItem(int cartId) async {
    final success = await ApiService.removeCartItem(cartId);
    if (success) {
      _items.removeWhere((item) => item.id == cartId);
      _itemCount = _items.length;
      _subtotal = _items.fold(0.0, (sum, i) => sum + i.subtotal);
      notifyListeners();
    }
  }
}
