import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../providers/auth_provider.dart';
import '../../services/api_service.dart';
import '../main_nav_screen.dart';
import 'seller_add_product_screen.dart';

class SellerDashboardScreen extends StatefulWidget {
  const SellerDashboardScreen({Key? key}) : super(key: key);

  @override
  State<SellerDashboardScreen> createState() => _SellerDashboardScreenState();
}

class _SellerDashboardScreenState extends State<SellerDashboardScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  bool _isLoading = true;

  Map<String, dynamic>? _dashboardData;
  List<Map<String, dynamic>> _products = [];
  List<Map<String, dynamic>> _orders = [];

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    _loadSellerData();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadSellerData() async {
    setState(() => _isLoading = true);
    try {
      final results = await Future.wait([
        ApiService.getSellerDashboard(),
        ApiService.getSellerProducts(),
        ApiService.getSellerOrders(),
      ]);

      if (mounted) {
        setState(() {
          _dashboardData = results[0] as Map<String, dynamic>?;
          _products = results[1] as List<Map<String, dynamic>>;
          _orders = results[2] as List<Map<String, dynamic>>;
          _isLoading = false;
        });
      }
    } catch (_) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  String _formatRupiah(num val) {
    return NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0).format(val);
  }

  Future<void> _updateOrderStatus(int orderId, String newStatus) async {
    final success = await ApiService.updateSellerOrderStatus(orderId, newStatus);
    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Status pesanan diubah ke: $newStatus'), backgroundColor: Colors.green),
      );
      _loadSellerData();
    }
  }

  @override
  Widget build(BuildContext context) {
    final store = _dashboardData?['store'];

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: AppTheme.accentNavy,
        elevation: 0,
        automaticallyImplyLeading: false,
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(2),
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                border: Border.all(color: Colors.amberAccent, width: 1.5),
              ),
              child: const CircleAvatar(
                radius: 17,
                backgroundColor: Colors.amber,
                child: Icon(Icons.storefront_rounded, color: Colors.white, size: 20),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    store?['name'] ?? 'Toko Saya',
                    style: const TextStyle(color: Colors.white, fontSize: 13.5, fontWeight: FontWeight.w800),
                    overflow: TextOverflow.ellipsis,
                  ),
                  const Text('Seller Center NitipDong', style: TextStyle(color: Colors.white60, fontSize: 10.5)),
                ],
              ),
            ),
          ],
        ),
        actions: [
          // Switch to Marketplace Shopping
          Container(
            margin: const EdgeInsets.only(right: 12),
            child: InkWell(
              onTap: () {
                Navigator.pushReplacement(
                  context,
                  MaterialPageRoute(builder: (_) => const MainNavScreen()),
                );
              },
              borderRadius: BorderRadius.circular(20),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.12),
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: Colors.white24),
                ),
                child: const Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(Icons.shopping_bag_outlined, color: Colors.cyanAccent, size: 14),
                    SizedBox(width: 4),
                    Text('Belanja 🛍️', style: TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.w700)),
                  ],
                ),
              ),
            ),
          ),
        ],
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: Colors.amberAccent,
          indicatorWeight: 3,
          labelColor: Colors.amberAccent,
          unselectedLabelColor: Colors.white60,
          labelStyle: const TextStyle(fontSize: 12, fontWeight: FontWeight.w800),
          tabs: const [
            Tab(icon: Icon(Icons.dashboard_rounded, size: 18), text: 'Ringkasan'),
            Tab(icon: Icon(Icons.inventory_2_rounded, size: 18), text: 'Produk Toko'),
            Tab(icon: Icon(Icons.receipt_long_rounded, size: 18), text: 'Pesanan'),
          ],
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: AppTheme.primary))
          : RefreshIndicator(
              onRefresh: _loadSellerData,
              child: TabBarView(
                controller: _tabController,
                children: [
                  _buildSummaryTab(),
                  _buildProductsTab(),
                  _buildOrdersTab(),
                ],
              ),
            ),
      floatingActionButton: _tabController.index == 1
          ? FloatingActionButton.extended(
              backgroundColor: AppTheme.primary,
              icon: const Icon(Icons.add_rounded, color: Colors.white),
              label: const Text('Tambah Produk', style: TextStyle(fontWeight: FontWeight.w800, color: Colors.white)),
              onPressed: () async {
                final added = await Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => const SellerAddProductScreen()),
                );
                if (added == true) _loadSellerData();
              },
            )
          : null,
    );
  }

  // TAB 1: RINGKASAN TOKO
  Widget _buildSummaryTab() {
    final totalSales = _dashboardData?['total_sales'] ?? 0;
    final walletBalance = _dashboardData?['wallet_balance'] ?? 0;
    final totalProducts = _dashboardData?['total_products'] ?? 0;
    final pendingOrders = _dashboardData?['pending_orders'] ?? 0;

    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Revenue Card
          Container(
            padding: const EdgeInsets.all(18),
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                colors: [Color(0xFF0B1528), Color(0xFF1E293B)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(18),
              boxShadow: [
                BoxShadow(color: Colors.black.withOpacity(0.1), blurRadius: 10, offset: const Offset(0, 4)),
              ],
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text('Saldo Penghasilan Toko', style: TextStyle(color: Colors.white70, fontSize: 12, fontWeight: FontWeight.w600)),
                    Icon(Icons.account_balance_wallet_rounded, color: Colors.amberAccent, size: 20),
                  ],
                ),
                const SizedBox(height: 8),
                Text(
                  _formatRupiah(walletBalance),
                  style: const TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.w900),
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                      decoration: BoxDecoration(color: Colors.white10, borderRadius: BorderRadius.circular(8)),
                      child: Text('Total Omset: ${_formatRupiah(totalSales)}', style: const TextStyle(color: Colors.amberAccent, fontSize: 11, fontWeight: FontWeight.bold)),
                    ),
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),

          // 2x2 Grid Stats
          GridView.count(
            crossAxisCount: 2,
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            crossAxisSpacing: 12,
            mainAxisSpacing: 12,
            childAspectRatio: 1.4,
            children: [
              _buildStatCard('Pesanan Perlu Diproses', '$pendingOrders', Icons.hourglass_top_rounded, Colors.orange),
              _buildStatCard('Total Produk Aktif', '$totalProducts', Icons.inventory_2_outlined, Colors.blue),
              _buildStatCard('Pesanan Selesai', '${_dashboardData?['completed_orders'] ?? 0}', Icons.check_circle_outline_rounded, Colors.green),
              _buildStatCard('Rating Toko', '5.0 ⭐', Icons.star_outline_rounded, Colors.amber),
            ],
          ),
          const SizedBox(height: 20),

          // Quick Action Banner
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: AppTheme.border),
            ),
            child: Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(color: AppTheme.primaryLight, borderRadius: BorderRadius.circular(12)),
                  child: const Icon(Icons.add_business_rounded, color: AppTheme.primary, size: 24),
                ),
                const SizedBox(width: 12),
                const Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('Tambah Produk Jualan', style: TextStyle(fontSize: 13.5, fontWeight: FontWeight.w800)),
                      Text('Perbanyak katalog untuk raih lebih banyak pembeli', style: TextStyle(fontSize: 11, color: AppTheme.textSecondary)),
                    ],
                  ),
                ),
                ElevatedButton(
                  onPressed: () async {
                    final added = await Navigator.push(
                      context,
                      MaterialPageRoute(builder: (_) => const SellerAddProductScreen()),
                    );
                    if (added == true) _loadSellerData();
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.primary,
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                  ),
                  child: const Text('Tambah', style: TextStyle(fontSize: 11.5, fontWeight: FontWeight.w800)),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatCard(String label, String value, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppTheme.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(label, style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary, fontWeight: FontWeight.w600)),
              Icon(icon, color: color, size: 18),
            ],
          ),
          Text(value, style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: AppTheme.textPrimary)),
        ],
      ),
    );
  }

  // TAB 2: PRODUK TOKO
  Widget _buildProductsTab() {
    if (_products.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.inventory_2_outlined, size: 50, color: Colors.grey),
            const SizedBox(height: 12),
            const Text('Belum Ada Produk di Toko Anda', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
            const SizedBox(height: 4),
            const Text('Tekan tombol di bawah untuk menambah produk pertama.', style: TextStyle(fontSize: 12, color: AppTheme.textMuted)),
            const SizedBox(height: 16),
            ElevatedButton.icon(
              icon: const Icon(Icons.add, size: 18),
              label: const Text('Tambah Produk Sekarang'),
              style: ElevatedButton.styleFrom(backgroundColor: AppTheme.primary),
              onPressed: () async {
                final added = await Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => const SellerAddProductScreen()),
                );
                if (added == true) _loadSellerData();
              },
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _products.length,
      itemBuilder: (ctx, idx) {
        final p = _products[idx];
        return Container(
          margin: const EdgeInsets.only(bottom: 12),
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: AppTheme.border),
          ),
          child: Row(
            children: [
              ClipRRect(
                borderRadius: BorderRadius.circular(10),
                child: Image.network(
                  p['image_url'] ?? '',
                  width: 60,
                  height: 60,
                  fit: BoxFit.cover,
                  errorBuilder: (_, __, ___) => Container(width: 60, height: 60, color: Colors.grey.shade200, child: const Icon(Icons.image)),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(p['name'] ?? '', style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w800), maxLines: 1, overflow: TextOverflow.ellipsis),
                    const SizedBox(height: 3),
                    Text(_formatRupiah(p['price'] ?? 0), style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w900, color: AppTheme.primary)),
                    const SizedBox(height: 2),
                    Text('Stok: ${p['stock']} unit', style: const TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                  ],
                ),
              ),
              IconButton(
                icon: const Icon(Icons.delete_outline_rounded, color: Colors.redAccent, size: 20),
                onPressed: () async {
                  final confirm = await showDialog<bool>(
                    context: context,
                    builder: (dCtx) => AlertDialog(
                      title: const Text('Hapus Produk?'),
                      content: Text('Yakin ingin menghapus "${p['name']}"?'),
                      actions: [
                        TextButton(onPressed: () => Navigator.pop(dCtx, false), child: const Text('Batal')),
                        ElevatedButton(
                          style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
                          onPressed: () => Navigator.pop(dCtx, true),
                          child: const Text('Hapus'),
                        ),
                      ],
                    ),
                  );
                  if (confirm == true) {
                    await ApiService.deleteSellerProduct(p['id']);
                    _loadSellerData();
                  }
                },
              ),
            ],
          ),
        );
      },
    );
  }

  // TAB 3: PESANAN MASUK
  Widget _buildOrdersTab() {
    if (_orders.isEmpty) {
      return const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.receipt_long_outlined, size: 50, color: Colors.grey),
            SizedBox(height: 12),
            Text('Belum Ada Pesanan Masuk', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
            Text('Pesanan baru dari pembeli akan muncul di sini.', style: TextStyle(fontSize: 12, color: AppTheme.textMuted)),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _orders.length,
      itemBuilder: (ctx, idx) {
        final o = _orders[idx];
        final status = o['status'] ?? 'pending';

        return Container(
          margin: const EdgeInsets.only(bottom: 12),
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: AppTheme.border),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(o['invoice_number'] ?? '', style: const TextStyle(fontSize: 12.5, fontWeight: FontWeight.w800)),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                    decoration: BoxDecoration(
                      color: status == 'completed' ? Colors.green.shade50 : (status == 'shipped' ? Colors.blue.shade50 : Colors.orange.shade50),
                      borderRadius: BorderRadius.circular(6),
                    ),
                    child: Text(
                      status.toUpperCase(),
                      style: TextStyle(
                        fontSize: 10,
                        fontWeight: FontWeight.w900,
                        color: status == 'completed' ? Colors.green : (status == 'shipped' ? Colors.blue : Colors.orange),
                      ),
                    ),
                  ),
                ],
              ),
              const Divider(height: 16),
              Text('Pembeli: ${o['recipient_name']} (${o['recipient_phone']})', style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
              Text('Alamat: ${o['shipping_address']}', style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary), maxLines: 1, overflow: TextOverflow.ellipsis),
              const SizedBox(height: 6),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text('Total: ${_formatRupiah(o['total_amount'] ?? 0)}', style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w900, color: AppTheme.primary)),
                  Text(o['created_at'] ?? '', style: const TextStyle(fontSize: 10.5, color: AppTheme.textMuted)),
                ],
              ),
              if (status == 'pending' || status == 'paid') ...[
                const SizedBox(height: 10),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    style: ElevatedButton.styleFrom(backgroundColor: AppTheme.primary),
                    onPressed: () => _updateOrderStatus(o['id'], 'processing'),
                    child: const Text('Proses & Siapkan Barang 📦', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w800)),
                  ),
                ),
              ] else if (status == 'processing') ...[
                const SizedBox(height: 10),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF0E7490)),
                    onPressed: () => _updateOrderStatus(o['id'], 'shipped'),
                    child: const Text('Siap Diambil Kurir / Kirim 🚚', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w800)),
                  ),
                ),
              ],
            ],
          ),
        );
      },
    );
  }
}
