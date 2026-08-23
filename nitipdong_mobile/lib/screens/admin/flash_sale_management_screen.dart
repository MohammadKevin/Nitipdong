import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../theme/app_theme.dart';
import '../../services/api_service.dart';

class FlashSaleManagementScreen extends StatefulWidget {
  const FlashSaleManagementScreen({Key? key}) : super(key: key);

  @override
  State<FlashSaleManagementScreen> createState() => _FlashSaleManagementScreenState();
}

class _FlashSaleManagementScreenState extends State<FlashSaleManagementScreen> {
  bool _isLoading = true;
  List<dynamic> _flashSales = [];
  List<dynamic> _products = [];
  
  final TextEditingController _titleController = TextEditingController();
  final TextEditingController _priceController = TextEditingController();
  final TextEditingController _stockController = TextEditingController();
  
  int? _selectedProductId;

  @override
  void initState() {
    super.initState();
    _loadFlashSalesData();
  }

  @override
  void dispose() {
    _titleController.dispose();
    _priceController.dispose();
    _stockController.dispose();
    super.dispose();
  }

  Future<void> _loadFlashSalesData() async {
    setState(() => _isLoading = true);
    final sales = await ApiService.getAdminFlashSales();
    final prods = await ApiService.getAdminProducts();
    if (mounted) {
      setState(() {
        _flashSales = sales;
        _products = prods.where((p) => p['is_active'] == true).toList();
        _isLoading = false;
      });
    }
  }

  Future<void> _handleToggle(int id, int index) async {
    final res = await ApiService.toggleFlashSale(id);
    if (mounted && res['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(res['message']),
          backgroundColor: AppTheme.success,
          behavior: SnackBarBehavior.floating,
        ),
      );
      setState(() {
        _flashSales[index]['is_active'] = !_flashSales[index]['is_active'];
      });
    }
  }

  void _showAddSaleDialog() {
    _titleController.clear();
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Text('Buat Flash Sale Baru', style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextField(
              controller: _titleController,
              decoration: InputDecoration(
                labelText: 'Judul Event',
                hintText: 'Contoh: Diskon Kemerdekaan',
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
              ),
            ),
            const SizedBox(height: 10),
            const Text(
              'Catatan: Waktu mulai adalah saat ini, dan berakhir dalam 24 jam ke depan (dapat diubah di web).',
              style: TextStyle(fontSize: 10.5, color: AppTheme.textSecondary),
            )
          ],
        ),
        actions: [
          TextButton(
            child: const Text('Batal'),
            onPressed: () => Navigator.pop(ctx),
          ),
          ElevatedButton(
            child: const Text('Buat', style: TextStyle(color: Colors.white)),
            onPressed: () async {
              final title = _titleController.text.trim();
              if (title.isEmpty) return;

              Navigator.pop(ctx);
              setState(() => _isLoading = true);

              final start = DateTime.now().toIso8601String();
              final end = DateTime.now().add(const Duration(hours: 24)).toIso8601String();

              final res = await ApiService.storeFlashSale(title, start, end);
              if (mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text(res['message']),
                    backgroundColor: res['success'] == true ? AppTheme.success : Colors.red,
                    behavior: SnackBarBehavior.floating,
                  ),
                );
                _loadFlashSalesData();
              }
            },
          ),
        ],
      ),
    );
  }

  void _showAddItemDialog(int flashSaleId) {
    _priceController.clear();
    _stockController.text = '10';
    _selectedProductId = _products.isNotEmpty ? _products[0]['id'] : null;

    showDialog(
      context: context,
      builder: (ctx) => StatefulBuilder(
        builder: (context, setDialogState) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          title: const Text('Tambah Produk ke Event', style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold)),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              // Product Dropdown
              DropdownButtonFormField<int>(
                value: _selectedProductId,
                decoration: InputDecoration(
                  labelText: 'Pilih Produk',
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                ),
                items: _products.map<DropdownMenuItem<int>>((p) {
                  return DropdownMenuItem<int>(
                    value: p['id'],
                    child: Text(
                      '${p['name']} (${p['store_name']})',
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(fontSize: 12),
                    ),
                  );
                }).toList(),
                onChanged: (val) {
                  setDialogState(() {
                    _selectedProductId = val;
                  });
                },
              ),
              const SizedBox(height: 12),

              TextField(
                controller: _priceController,
                keyboardType: TextInputType.number,
                decoration: InputDecoration(
                  labelText: 'Harga Flash Sale',
                  prefixText: 'Rp ',
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                ),
              ),
              const SizedBox(height: 12),

              TextField(
                controller: _stockController,
                keyboardType: TextInputType.number,
                decoration: InputDecoration(
                  labelText: 'Kuota Stok Promo',
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                ),
              ),
            ],
          ),
          actions: [
            TextButton(
              child: const Text('Batal'),
              onPressed: () => Navigator.pop(ctx),
            ),
            ElevatedButton(
              child: const Text('Tambah', style: TextStyle(color: Colors.white)),
              onPressed: () async {
                final price = double.tryParse(_priceController.text) ?? 0.0;
                final stock = int.tryParse(_stockController.text) ?? 0;
                if (price <= 0 || stock <= 0 || _selectedProductId == null) return;

                Navigator.pop(ctx);
                setState(() => _isLoading = true);

                final res = await ApiService.addFlashSaleItem(flashSaleId, _selectedProductId!, price, stock);
                if (mounted) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text(res['message']),
                      backgroundColor: res['success'] == true ? AppTheme.success : Colors.red,
                      behavior: SnackBarBehavior.floating,
                    ),
                  );
                  _loadFlashSalesData();
                }
              },
            ),
          ],
        ),
      ),
    );
  }

  Future<void> _handleRemoveItem(int flashSaleId, int itemId) async {
    setState(() => _isLoading = true);
    final res = await ApiService.removeFlashSaleItem(flashSaleId, itemId);
    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(res['message']),
          backgroundColor: res['success'] == true ? AppTheme.success : Colors.red,
          behavior: SnackBarBehavior.floating,
        ),
      );
      _loadFlashSalesData();
    }
  }

  String _formatDate(String isoString) {
    try {
      final date = DateTime.parse(isoString);
      return DateFormat('dd MMM yyyy, HH:mm').format(date);
    } catch (_) {
      return isoString;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: const Text('Manajemen Flash Sale'),
        actions: [
          IconButton(
            icon: const Icon(Icons.add_rounded),
            onPressed: _showAddSaleDialog,
          ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _flashSales.isEmpty
              ? Center(
                  child: Padding(
                    padding: const EdgeInsets.all(32),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Container(
                          padding: const EdgeInsets.all(20),
                          decoration: BoxDecoration(color: Colors.red.shade50, shape: BoxShape.circle),
                          child: const Icon(Icons.flash_on_rounded, size: 56, color: Colors.red),
                        ),
                        const SizedBox(height: 16),
                        const Text(
                          'Belum Ada Flash Sale',
                          style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppTheme.textPrimary),
                        ),
                        const SizedBox(height: 4),
                        const Text(
                          'Buat event flash sale promosi diskon kilat pertama Anda.',
                          style: TextStyle(fontSize: 12, color: AppTheme.textSecondary),
                        ),
                      ],
                    ),
                  ),
                )
              : RefreshIndicator(
                  onRefresh: _loadFlashSalesData,
                  child: ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: _flashSales.length,
                    itemBuilder: (context, index) {
                      final sale = _flashSales[index];
                      final isActive = sale['is_active'] == true;
                      final items = sale['items'] as List<dynamic>? ?? [];

                      return Container(
                        margin: const EdgeInsets.only(bottom: 16),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(color: Colors.grey.shade200),
                        ),
                        child: ExpansionTile(
                          leading: Container(
                            padding: const EdgeInsets.all(8),
                            decoration: BoxDecoration(
                              color: Colors.red.shade50,
                              shape: BoxShape.circle,
                            ),
                            child: const Icon(Icons.bolt_rounded, color: Colors.red, size: 22),
                          ),
                          title: Text(
                            sale['title'] ?? 'Event Flash Sale',
                            style: const TextStyle(fontSize: 13.5, fontWeight: FontWeight.bold, color: AppTheme.textPrimary),
                          ),
                          subtitle: Text(
                            '${_formatDate(sale['start_time'])} - ${_formatDate(sale['end_time'])}',
                            style: const TextStyle(fontSize: 10, color: AppTheme.textSecondary),
                          ),
                          trailing: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Switch.adaptive(
                                value: isActive,
                                activeColor: AppTheme.success,
                                onChanged: (_) => _handleToggle(sale['id'], index),
                              ),
                            ],
                          ),
                          children: [
                            const Divider(height: 1),
                            // Items header
                            Padding(
                              padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
                              child: Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Text(
                                    'Daftar Produk (${items.length})',
                                    style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppTheme.textPrimary),
                                  ),
                                  TextButton.icon(
                                    icon: const Icon(Icons.add_rounded, size: 14),
                                    label: const Text('Tambah', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                                    style: TextButton.styleFrom(padding: EdgeInsets.zero, minimumSize: Size.zero),
                                    onPressed: () => _showAddItemDialog(sale['id']),
                                  )
                                ],
                              ),
                            ),

                            if (items.isEmpty)
                              const Padding(
                                padding: EdgeInsets.symmetric(vertical: 16),
                                child: Text('Belum ada produk yang ikut flash sale.', style: TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                              )
                            else
                              ListView.builder(
                                shrinkWrap: true,
                                physics: const NeverScrollableScrollPhysics(),
                                itemCount: items.length,
                                itemBuilder: (context, itemIdx) {
                                  final item = items[itemIdx];
                                  final product = item['product'] ?? {};
                                  final storeName = product['store'] != null ? product['store']['name'] : 'Toko';

                                  return ListTile(
                                    contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 0),
                                    dense: true,
                                    title: Text(
                                      product['name'] ?? 'Produk Promo',
                                      style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
                                    ),
                                    subtitle: Text(
                                      'Rp ${item['flash_sale_price']} (${item['discount_percentage']}% Off)  |  Stok: ${item['stock_allocated']}',
                                      style: const TextStyle(fontSize: 10.5, color: AppTheme.accentOrange, fontWeight: FontWeight.w600),
                                    ),
                                    trailing: IconButton(
                                      icon: const Icon(Icons.remove_circle_outline_rounded, color: Colors.red, size: 18),
                                      onPressed: () => _handleRemoveItem(sale['id'], item['id']),
                                    ),
                                  );
                                },
                              )
                          ],
                        ),
                      );
                    },
                  ),
                ),
    );
  }
}
