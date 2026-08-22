import 'package:flutter/material.dart';
import '../../models/product_model.dart';
import '../../services/api_service.dart';
import '../../theme/app_theme.dart';
import '../../widgets/product_card.dart';

class FlashSaleListScreen extends StatefulWidget {
  const FlashSaleListScreen({Key? key}) : super(key: key);

  @override
  State<FlashSaleListScreen> createState() => _FlashSaleListScreenState();
}

class _FlashSaleListScreenState extends State<FlashSaleListScreen> {
  List<ProductModel> _flashSaleProducts = [];
  bool _isLoading = true;
  String _title = 'Flash Sale Kilat';

  @override
  void initState() {
    super.initState();
    _fetchFlashSale();
  }

  Future<void> _fetchFlashSale() async {
    setState(() => _isLoading = true);
    final data = await ApiService.getFlashSale();
    setState(() {
      if (data['success'] == true) {
        _flashSaleProducts = data['items'] ?? [];
        _title = data['title'] ?? 'Flash Sale Kilat';
      }
      _isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(_title),
      ),
      body: RefreshIndicator(
        onRefresh: _fetchFlashSale,
        color: AppTheme.primary,
        child: _isLoading
            ? const Center(child: CircularProgressIndicator(color: AppTheme.primary))
            : _flashSaleProducts.isEmpty
                ? const Center(
                    child: Text(
                      'Tidak ada promo Flash Sale aktif saat ini.',
                      style: TextStyle(color: AppTheme.textMuted),
                    ),
                  )
                : GridView.builder(
                    padding: const EdgeInsets.all(16),
                    gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 2,
                      crossAxisSpacing: 10,
                      mainAxisSpacing: 10,
                      childAspectRatio: 0.62,
                    ),
                    itemCount: _flashSaleProducts.length,
                    itemBuilder: (context, index) {
                      return ProductCard(product: _flashSaleProducts[index]);
                    },
                  ),
      ),
    );
  }
}
