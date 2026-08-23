import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../theme/app_theme.dart';
import '../../services/api_service.dart';

class ProductModerationScreen extends StatefulWidget {
  const ProductModerationScreen({Key? key}) : super(key: key);

  @override
  State<ProductModerationScreen> createState() => _ProductModerationScreenState();
}

class _ProductModerationScreenState extends State<ProductModerationScreen> {
  bool _isLoading = true;
  List<dynamic> _products = [];
  List<dynamic> _filteredProducts = [];
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _loadProducts();
    _searchController.addListener(_onSearchChanged);
  }

  @override
  void dispose() {
    _searchController.removeListener(_onSearchChanged);
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadProducts() async {
    setState(() => _isLoading = true);
    final products = await ApiService.getAdminProducts();
    if (mounted) {
      setState(() {
        _products = products;
        _filteredProducts = products;
        _isLoading = false;
      });
    }
  }

  void _onSearchChanged() {
    final query = _searchController.text.toLowerCase();
    setState(() {
      _filteredProducts = _products.where((product) {
        final name = (product['name'] ?? '').toString().toLowerCase();
        final store = (product['store_name'] ?? '').toString().toLowerCase();
        return name.contains(query) || store.contains(query);
      }).toList();
    });
  }

  Future<void> _handleToggleStatus(int id, String name, int index) async {
    final res = await ApiService.toggleProductStatus(id);
    if (mounted && res['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(res['message']),
          backgroundColor: AppTheme.success,
          behavior: SnackBarBehavior.floating,
        ),
      );
      setState(() {
        // Update local status instantly
        _filteredProducts[index]['is_active'] = res['is_active'];
      });
    }
  }

  String _formatCurrency(double amount) {
    final formatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    return formatter.format(amount);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: const Text('Moderasi Katalog Produk'),
      ),
      body: Column(
        children: [
          // Search box
          Container(
            padding: const EdgeInsets.all(12),
            color: Colors.white,
            border: Border(bottom: BorderSide(color: Colors.grey.shade200)),
            child: TextField(
              controller: _searchController,
              style: const TextStyle(fontSize: 13),
              decoration: InputDecoration(
                hintText: 'Cari nama produk atau nama toko...',
                prefixIcon: const Icon(Icons.search_rounded, size: 20),
                contentPadding: const EdgeInsets.symmetric(vertical: 8),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
                fillColor: Colors.grey.shade50,
                filled: true,
              ),
            ),
          ),

          Expanded(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator())
                : _filteredProducts.isEmpty
                    ? Center(
                        child: Padding(
                          padding: const EdgeInsets.all(32),
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Container(
                                padding: const EdgeInsets.all(20),
                                decoration: BoxDecoration(color: Colors.blue.shade50, shape: BoxShape.circle),
                                child: const Icon(Icons.inventory_2_rounded, size: 56, color: Colors.blue),
                              ),
                              const SizedBox(height: 16),
                              const Text(
                                'Produk Tidak Ditemukan',
                                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppTheme.textPrimary),
                              ),
                              const SizedBox(height: 4),
                              const Text(
                                'Tidak ada produk yang cocok dengan pencarian Anda.',
                                style: TextStyle(fontSize: 12, color: AppTheme.textSecondary),
                                textAlign: TextAlign.center,
                              ),
                            ],
                          ),
                        ),
                      )
                    : RefreshIndicator(
                        onRefresh: _loadProducts,
                        child: ListView.builder(
                          padding: const EdgeInsets.all(12),
                          itemCount: _filteredProducts.length,
                          itemBuilder: (context, index) {
                            final product = _filteredProducts[index];
                            final isActive = product['is_active'] == true;

                            return Container(
                              margin: const EdgeInsets.only(bottom: 10),
                              padding: const EdgeInsets.all(12),
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(14),
                                border: Border.all(color: Colors.grey.shade200),
                              ),
                              child: Row(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  // Product image
                                  ClipRRect(
                                    borderRadius: BorderRadius.circular(8),
                                    child: Container(
                                      width: 60,
                                      height: 60,
                                      color: Colors.grey.shade100,
                                      child: product['image'] != null
                                          ? Image.network(
                                              product['image'],
                                              fit: BoxFit.cover,
                                              errorBuilder: (_, __, ___) => const Icon(Icons.image_not_supported_rounded, size: 30, color: Colors.grey),
                                            )
                                          : const Icon(Icons.image_rounded, size: 30, color: Colors.grey),
                                    ),
                                  ),
                                  const SizedBox(width: 12),

                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          product['name'] ?? 'Nama Produk',
                                          maxLines: 1,
                                          overflow: TextOverflow.ellipsis,
                                          style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppTheme.textPrimary),
                                        ),
                                        const SizedBox(height: 2),
                                        Text(
                                          'Toko: ${product['store_name']}',
                                          style: const TextStyle(fontSize: 11, color: AppTheme.primaryDark, fontWeight: FontWeight.w600),
                                        ),
                                        const SizedBox(height: 2),
                                        Text(
                                          'Kat: ${product['category_name']}  |  Stok: ${product['stock']}',
                                          style: const TextStyle(fontSize: 10.5, color: AppTheme.textSecondary),
                                        ),
                                        const SizedBox(height: 4),
                                        Text(
                                          _formatCurrency((product['price'] as num?)?.toDouble() ?? 0.0),
                                          style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: AppTheme.accentOrange),
                                        ),
                                      ],
                                    ),
                                  ),

                                  // Status switch
                                  Column(
                                    crossAxisAlignment: CrossAxisAlignment.end,
                                    children: [
                                      Switch.adaptive(
                                        activeColor: AppTheme.success,
                                        value: isActive,
                                        onChanged: (_) => _handleToggleStatus(product['id'], product['name'] ?? 'Produk', index),
                                      ),
                                      Text(
                                        isActive ? 'Aktif' : 'Nonaktif',
                                        style: TextStyle(
                                          fontSize: 10,
                                          fontWeight: FontWeight.bold,
                                          color: isActive ? AppTheme.success : Colors.red,
                                        ),
                                      )
                                    ],
                                  )
                                ],
                              ),
                            );
                          },
                        ),
                      ),
          ),
        ],
      ),
    );
  }
}
