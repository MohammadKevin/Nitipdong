import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:intl/intl.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../models/order_model.dart';
import '../../services/api_service.dart';
import '../../providers/auth_provider.dart';
import '../auth/login_screen.dart';

class OrdersScreen extends StatefulWidget {
  const OrdersScreen({Key? key}) : super(key: key);

  @override
  State<OrdersScreen> createState() => _OrdersScreenState();
}

class _OrdersScreenState extends State<OrdersScreen> {
  List<OrderModel> _orders = [];
  bool _isLoading = true;
  String _selectedStatus = 'all';

  final List<Map<String, String>> _statusTabs = [
    {'label': 'Semua', 'value': 'all'},
    {'label': 'Belum Bayar', 'value': 'pending'},
    {'label': 'Diproses', 'value': 'processing'},
    {'label': 'Dikirim', 'value': 'shipped'},
    {'label': 'Selesai', 'value': 'completed'},
    {'label': 'Dibatalkan', 'value': 'cancelled'},
  ];

  @override
  void initState() {
    super.initState();
    _fetchOrders();
  }

  Future<void> _fetchOrders() async {
    setState(() => _isLoading = true);
    final orders = await ApiService.getOrders(status: _selectedStatus);
    if (mounted) {
      setState(() {
        _orders = orders;
        _isLoading = false;
      });
    }
  }

  String _formatCurrency(double amount) {
    final formatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    return formatter.format(amount);
  }

  Color _getStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'completed':
        return AppTheme.success;
      case 'shipped':
        return Colors.blue;
      case 'processing':
        return Colors.indigo;
      case 'pending':
        return AppTheme.accentOrange;
      case 'cancelled':
        return Colors.red;
      default:
        return AppTheme.primary;
    }
  }

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);

    if (!authProvider.isAuthenticated) {
      return Scaffold(
        appBar: AppBar(title: const Text('Daftar Transaksi')),
        body: Center(
          child: Padding(
            padding: const EdgeInsets.all(30),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(Icons.receipt_long_outlined, size: 70, color: Colors.grey.shade300),
                const SizedBox(height: 16),
                const Text('Masuk untuk Melihat Pesanan', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
                const SizedBox(height: 6),
                const Text(
                  'Lihat riwayat pembelian, lacak paket, dan kelola pesanan Anda.',
                  textAlign: TextAlign.center,
                  style: TextStyle(fontSize: 12, color: AppTheme.textSecondary),
                ),
                const SizedBox(height: 24),
                ElevatedButton(
                  onPressed: () {
                    Navigator.push(context, MaterialPageRoute(builder: (context) => const LoginScreen()));
                  },
                  child: const Text('Masuk Sekarang'),
                ),
              ],
            ),
          ),
        ),
      );
    }

    return Scaffold(
      appBar: AppBar(
        title: const Text('Daftar Pesanan Saya'),
      ),
      body: Column(
        children: [
          // ══════════════════════════════════════════════════
          // 1. FILTER STATUS TABS
          // ══════════════════════════════════════════════════
          Container(
            height: 48,
            decoration: const BoxDecoration(
              color: Colors.white,
              border: Border(bottom: BorderSide(color: AppTheme.border)),
            ),
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              itemCount: _statusTabs.length,
              itemBuilder: (context, index) {
                final tab = _statusTabs[index];
                final isSelected = _selectedStatus == tab['value'];

                return Padding(
                  padding: const EdgeInsets.only(right: 8),
                  child: FilterChip(
                    label: Text(
                      tab['label']!,
                      style: TextStyle(
                        fontSize: 11,
                        fontWeight: isSelected ? FontWeight.w800 : FontWeight.w500,
                        color: isSelected ? AppTheme.primaryDark : AppTheme.textSecondary,
                      ),
                    ),
                    selected: isSelected,
                    onSelected: (_) {
                      setState(() => _selectedStatus = tab['value']!);
                      _fetchOrders();
                    },
                    backgroundColor: Colors.grey.shade50,
                    selectedColor: AppTheme.primaryLight,
                    side: BorderSide(
                      color: isSelected ? AppTheme.primary : Colors.grey.shade200,
                    ),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                    showCheckmark: false,
                    padding: const EdgeInsets.symmetric(horizontal: 4),
                  ),
                );
              },
            ),
          ),

          // ══════════════════════════════════════════════════
          // 2. ORDERS LIST
          // ══════════════════════════════════════════════════
          Expanded(
            child: RefreshIndicator(
              color: AppTheme.primary,
              onRefresh: _fetchOrders,
              child: _isLoading
                  ? const Center(child: CircularProgressIndicator(color: AppTheme.primary))
                  : _orders.isEmpty
                      ? Center(
                          child: SingleChildScrollView(
                            physics: const AlwaysScrollableScrollPhysics(),
                            child: Padding(
                              padding: const EdgeInsets.all(40),
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Icon(Icons.inventory_2_outlined, size: 60, color: Colors.grey.shade300),
                                  const SizedBox(height: 14),
                                  const Text('Belum Ada Pesanan', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
                                  const SizedBox(height: 6),
                                  const Text(
                                    'Pesanan Anda akan tercatat di sini setelah melakukan checkout pembelian.',
                                    textAlign: TextAlign.center,
                                    style: TextStyle(fontSize: 12, color: AppTheme.textMuted),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        )
                      : ListView.builder(
                          padding: const EdgeInsets.all(16),
                          itemCount: _orders.length,
                          itemBuilder: (context, index) {
                            final order = _orders[index];
                            final statusColor = _getStatusColor(order.status);

                            return Container(
                              margin: const EdgeInsets.only(bottom: 14),
                              padding: const EdgeInsets.all(14),
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(16),
                                border: Border.all(color: AppTheme.border),
                                boxShadow: [
                                  BoxShadow(
                                    color: Colors.black.withOpacity(0.02),
                                    blurRadius: 6,
                                    offset: const Offset(0, 2),
                                  ),
                                ],
                              ),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  // Order Header: Number & Status Badge
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    children: [
                                      Row(
                                        children: [
                                          const Icon(Icons.receipt_outlined, size: 16, color: AppTheme.primary),
                                          const SizedBox(width: 6),
                                          Text(
                                            order.orderNumber,
                                            style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w800, color: AppTheme.textPrimary),
                                          ),
                                        ],
                                      ),
                                      Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                                        decoration: BoxDecoration(
                                          color: statusColor.withOpacity(0.12),
                                          borderRadius: BorderRadius.circular(6),
                                        ),
                                        child: Text(
                                          order.statusLabel,
                                          style: TextStyle(color: statusColor, fontSize: 10, fontWeight: FontWeight.w800),
                                        ),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 10),
                                  const Divider(height: 1),
                                  const SizedBox(height: 10),

                                  // First Product Preview
                                  if (order.firstProduct != null)
                                    Row(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        ClipRRect(
                                          borderRadius: BorderRadius.circular(8),
                                          child: CachedNetworkImage(
                                            imageUrl: order.firstProduct!.imageUrl,
                                            width: 50,
                                            height: 50,
                                            fit: BoxFit.cover,
                                            placeholder: (context, url) => Container(color: Colors.grey.shade100),
                                            errorWidget: (context, url, error) => Container(
                                              color: Colors.grey.shade100,
                                              child: const Icon(Icons.shopping_bag_outlined, color: Colors.grey),
                                            ),
                                          ),
                                        ),
                                        const SizedBox(width: 10),
                                        Expanded(
                                          child: Column(
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              Text(
                                                order.firstProduct!.name,
                                                maxLines: 1,
                                                overflow: TextOverflow.ellipsis,
                                                style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700),
                                              ),
                                              const SizedBox(height: 2),
                                              Text(
                                                '${order.firstProduct!.quantity}x · ${_formatCurrency(order.firstProduct!.price)}',
                                                style: const TextStyle(fontSize: 11, color: AppTheme.textMuted),
                                              ),
                                              if (order.itemsCount > 1)
                                                Text(
                                                  '+${order.itemsCount - 1} produk lainnya',
                                                  style: const TextStyle(fontSize: 10, color: AppTheme.primary, fontWeight: FontWeight.w600),
                                                ),
                                            ],
                                          ),
                                        ),
                                      ],
                                    ),
                                  const SizedBox(height: 12),
                                  const Divider(height: 1),
                                  const SizedBox(height: 10),

                                  // Total & Action Buttons
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    children: [
                                      Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          const Text('Total Pembayaran', style: TextStyle(fontSize: 10, color: AppTheme.textMuted)),
                                          Text(
                                            _formatCurrency(order.totalAmount),
                                            style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w900, color: AppTheme.primaryDark),
                                          ),
                                        ],
                                      ),
                                      Row(
                                        children: [
                                          if (order.status.toLowerCase() == 'pending') ...[
                                            ElevatedButton(
                                              onPressed: () => _payOrder(order),
                                              style: ElevatedButton.styleFrom(
                                                backgroundColor: AppTheme.accentOrange,
                                                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                                              ),
                                              child: const Text('Bayar Sekarang', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w800, color: Colors.white)),
                                            ),
                                            const SizedBox(width: 8),
                                          ],
                                          OutlinedButton(
                                            onPressed: () => _showOrderDetailSheet(context, order),
                                            style: OutlinedButton.styleFrom(
                                              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                                              side: const BorderSide(color: AppTheme.primary),
                                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                                            ),
                                            child: const Text('Detail', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: AppTheme.primary)),
                                          ),
                                        ],
                                      ),
                                    ],
                                  ),
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

  // ══════════════════════════════════════════════════
  // PAY ORDER ACTION
  // ══════════════════════════════════════════════════
  Future<void> _payOrder(OrderModel order) async {
    setState(() => _isLoading = true);
    final result = await ApiService.payOrder(order.id);
    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['success'] == true ? 'Pembayaran berhasil dikonfirmasi! Pesanan sedang diproses toko. 🎉' : (result['message'] ?? 'Gagal memproses pembayaran.')),
          backgroundColor: result['success'] == true ? Colors.green : Colors.red,
          behavior: SnackBarBehavior.floating,
        ),
      );
      _fetchOrders();
    }
  }

  // ══════════════════════════════════════════════════
  // ORDER DETAIL BOTTOM SHEET
  // ══════════════════════════════════════════════════
  void _showOrderDetailSheet(BuildContext context, OrderModel order) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (ctx) => Padding(
        padding: const EdgeInsets.fromLTRB(20, 16, 20, 30),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: Colors.grey.shade300, borderRadius: BorderRadius.circular(2)))),
            const SizedBox(height: 16),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text('Rincian Pesanan 📦', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
                IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(ctx)),
              ],
            ),
            const SizedBox(height: 12),
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(12), border: Border.all(color: AppTheme.border)),
              child: Column(
                children: [
                  _buildDetailRow('Nomor Pesanan', order.orderNumber),
                  const SizedBox(height: 6),
                  _buildDetailRow('Status Pesanan', order.statusLabel),
                  const SizedBox(height: 6),
                  _buildDetailRow('Status Pembayaran', order.paymentStatus.toUpperCase()),
                  const SizedBox(height: 6),
                  _buildDetailRow('Waktu Transaksi', order.createdAt),
                ],
              ),
            ),
            const SizedBox(height: 16),
            const Text('Kurir & Pengiriman', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w800)),
            const SizedBox(height: 8),
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(color: Colors.grey.shade50, borderRadius: BorderRadius.circular(12), border: Border.all(color: AppTheme.border)),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: const [
                  Text('Kurir: J&T Express (Gratis Ongkir Rp0)', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700)),
                  SizedBox(height: 4),
                  Text('No. Resi: NTD-EXP-202688910', style: TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                  SizedBox(height: 4),
                  Text('Status: Paket sedang dalam perjalanan ke alamat tujuan.', style: TextStyle(fontSize: 11, color: Colors.blue, fontWeight: FontWeight.w600)),
                ],
              ),
            ),
            const SizedBox(height: 20),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () => Navigator.pop(ctx),
                child: const Text('Tutup'),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildDetailRow(String label, String value) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: const TextStyle(fontSize: 11, color: AppTheme.textMuted)),
        Text(value, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w700)),
      ],
    );
  }
}
