import 'package:flutter/material.dart';
import '../models/product_model.dart';
import '../models/category_model.dart';
import '../models/banner_model.dart';
import '../services/api_service.dart';

class ProductProvider with ChangeNotifier {
  List<BannerModel> _banners = [];
  List<CategoryModel> _categories = [];
  List<ProductModel> _flashSaleItems = [];
  List<ProductModel> _products = [];
  bool _isLoading = false;
  String _selectedCategory = '';
  String _searchQuery = '';
  int _flashSaleRemainingSeconds = 0;

  List<BannerModel> get banners => _banners;
  List<CategoryModel> get categories => _categories;
  List<ProductModel> get flashSaleItems => _flashSaleItems;
  List<ProductModel> get products => _products;
  bool get isLoading => _isLoading;
  String get selectedCategory => _selectedCategory;
  int get flashSaleRemainingSeconds => _flashSaleRemainingSeconds;

  Future<void> fetchHomeData() async {
    _isLoading = true;
    notifyListeners();

    try {
      final bannerFuture = ApiService.getBanners();
      final categoryFuture = ApiService.getCategories();
      final flashSaleFuture = ApiService.getFlashSale();
      final productFuture = ApiService.getProducts();

      _banners = await bannerFuture;
      _categories = await categoryFuture;
      
      final flashData = await flashSaleFuture;
      if (flashData['success'] == true) {
        _flashSaleItems = flashData['items'];
        _flashSaleRemainingSeconds = flashData['remaining_seconds'];
      }

      _products = await productFuture;
    } catch (_) {}

    _isLoading = false;
    notifyListeners();
  }

  Future<void> filterByCategory(String slug) async {
    _selectedCategory = slug;
    _isLoading = true;
    notifyListeners();

    _products = await ApiService.getProducts(category: slug, q: _searchQuery);
    _isLoading = false;
    notifyListeners();
  }

  Future<void> search(String query) async {
    _searchQuery = query;
    _isLoading = true;
    notifyListeners();

    _products = await ApiService.getProducts(category: _selectedCategory, q: query);
    _isLoading = false;
    notifyListeners();
  }
}
