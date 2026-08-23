import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:intl/intl.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../theme/app_theme.dart';
import '../../models/order_model.dart';
import '../../services/api_service.dart';
import '../../providers/auth_provider.dart';
import '../auth/login_screen.dart';
import 'live_map_tracking_screen.dart';

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
                Container(
                  padding: const EdgeInsets.all(24),
                  decoration: BoxDecoration(
                    color: AppTheme.primaryLight,
                    borderRadius: BorderRadius.circular(15),
                  ),
                  child: const Icon(Icons.receipt_long_outlined, size: 56, color: AppTheme.primary),
                ),
                const SizedBox(height: 20),
                const Text('Masuk untuk Melihat Pesanan', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
                const SizedBox(height: 8),
                const Text(
                  'Lihat riwayat pembelian, lacak paket pengiriman, dan kelola pesanan Anda.',
                  textAlign: TextAlign.center,
                  style: TextStyle(fontSize: 12.5, color: AppTheme.textSecondary, height: 1.4),
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
          // 1. FILTER STATUS TABS
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
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                  ),
                );
              },
            ),
          ),

          // 2. ORDERS LIST
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
                                  Container(
                                    padding: const EdgeInsets.all(24),
                                    decoration: BoxDecoration(
                                      color: Colors.grey.shade100,
                                      borderRadius: BorderRadius.circular(15),
                                    ),
                                    child: Icon(Icons.inbox_outlined, size: 56, color: Colors.grey.shade400),
                                  ),
                                  const SizedBox(height: 16),
                                  const Text(
                                    'Belum Ada Pesanan',
                                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800),
                                  ),
                                  const SizedBox(height: 6),
                                  const Text(
                                    'Pesanan Anda pada status ini akan muncul di sini.',
                                    textAlign: TextAlign.center,
                                    style: TextStyle(fontSize: 12, color: AppTheme.textMuted),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        )
                      : ListView.builder(
                          padding: const EdgeInsets.fromLTRB(16, 12, 16, 80),
                          itemCount: _orders.length,
                          itemBuilder: (context, index) {
                            final order = _orders[index];
                            final statusColor = _getStatusColor(order.status);
                            final st = order.status.toLowerCase();

                            return Container(
                              margin: const EdgeInsets.only(bottom: 12),
                              padding: const EdgeInsets.all(14),
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(14),
                                border: Border.all(color: AppTheme.border),
                                boxShadow: [
                                  BoxShadow(
                                    color: Colors.black.withOpacity(0.02),
                                    blurRadius: 8,
                                    offset: const Offset(0, 2),
                                  ),
                                ],
                              ),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  // Header: Invoice & Status Pill
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    children: [
                                      Row(
                                        children: [
                                          const Icon(Icons.shopping_bag_outlined, size: 16, color: AppTheme.primaryDark),
                                          const SizedBox(width: 6),
                                          Text(
                                            order.orderNumber,
                                            style: const TextStyle(fontSize: 11.5, fontWeight: FontWeight.w800, color: AppTheme.textPrimary),
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
                                          style: TextStyle(
                                            color: statusColor,
                                            fontSize: 10,
                                            fontWeight: FontWeight.w800,
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                  const Divider(height: 18, thickness: 0.8),

                                  // Product Info Preview
                                  if (order.firstProduct != null)
                                    Row(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        ClipRRect(
                                          borderRadius: BorderRadius.circular(8),
                                          child: CachedNetworkImage(
                                            imageUrl: order.firstProduct!.imageUrl,
                                            width: 56,
                                            height: 56,
                                            fit: BoxFit.cover,
                                            placeholder: (context, url) => Container(color: Colors.grey.shade100),
                                            errorWidget: (context, url, error) => Container(
                                              color: Colors.grey.shade100,
                                              child: const Icon(Icons.broken_image, color: Colors.grey),
                                            ),
                                          ),
                                        ),
                                        const SizedBox(width: 12),
                                        Expanded(
                                          child: Column(
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              Text(
                                                order.firstProduct!.name,
                                                maxLines: 1,
                                                overflow: TextOverflow.ellipsis,
                                                style: const TextStyle(fontSize: 12.5, fontWeight: FontWeight.w700),
                                              ),
                                              const SizedBox(height: 4),
                                              Text(
                                                '${order.firstProduct!.quantity}x  ${_formatCurrency(order.firstProduct!.price)}',
                                                style: const TextStyle(fontSize: 11, color: AppTheme.textMuted),
                                              ),
                                              if (order.itemsCount > 1) ...[
                                                const SizedBox(height: 2),
                                                Text(
                                                  '+${order.itemsCount - 1} produk lainnya',
                                                  style: const TextStyle(fontSize: 10, color: AppTheme.primary, fontWeight: FontWeight.w600),
                                                ),
                                              ],
                                            ],
                                          ),
                                        ),
                                      ],
                                    ),

                                  const Divider(height: 18, thickness: 0.8),

                                  // Footer: Total & Contextual Action Buttons
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    children: [
                                      Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          const Text('Total Belanja', style: TextStyle(fontSize: 10, color: AppTheme.textMuted)),
                                          Text(
                                            _formatCurrency(order.totalAmount),
                                            style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w900, color: AppTheme.primaryDark),
                                          ),
                                        ],
                                      ),

                                      // Contextual Action Buttons
                                      Wrap(
                                        spacing: 6,
                                        children: [
                                          // 1. Pending Actions
                                          if (st == 'pending') ...[
                                            OutlinedButton(
                                              onPressed: () => _confirmCancelOrder(order),
                                              style: OutlinedButton.styleFrom(
                                                foregroundColor: Colors.red,
                                                side: BorderSide(color: Colors.red.shade200),
                                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                                                minimumSize: Size.zero,
                                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                                              ),
                                              child: const Text('Batalkan', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700)),
                                            ),
                                            ElevatedButton(
                                              onPressed: () => _payOrder(order),
                                              style: ElevatedButton.styleFrom(
                                                backgroundColor: AppTheme.accentOrange,
                                                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                                                minimumSize: Size.zero,
                                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                                              ),
                                              child: const Text('Bayar Sekarang', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w800, color: Colors.white)),
                                            ),
                                          ],

                                          // 2. Processing Actions
                                          if (st == 'processing') ...[
                                            OutlinedButton(
                                              onPressed: () => _confirmCancelOrder(order),
                                              style: OutlinedButton.styleFrom(
                                                foregroundColor: Colors.red,
                                                side: BorderSide(color: Colors.red.shade200),
                                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                                                minimumSize: Size.zero,
                                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                                              ),
                                              child: const Text('Batalkan', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700)),
                                            ),
                                            OutlinedButton(
                                              onPressed: () => _showTrackingSheet(order),
                                              style: OutlinedButton.styleFrom(
                                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                                                minimumSize: Size.zero,
                                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                                              ),
                                              child: const Text('Lacak', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700)),
                                            ),
                                          ],

                                          // 3. Shipped Actions
                                          if (st == 'shipped') ...[
                                            ElevatedButton.icon(
                                              icon: const Icon(Icons.map_rounded, size: 13, color: Colors.cyanAccent),
                                              label: const Text('Live Tracking 🗺️', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w800, color: Colors.white)),
                                              style: ElevatedButton.styleFrom(
                                                backgroundColor: AppTheme.accentNavy,
                                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                                                minimumSize: Size.zero,
                                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                                              ),
                                              onPressed: () {
                                                Navigator.push(
                                                  context,
                                                  MaterialPageRoute(
                                                    builder: (_) => LiveMapTrackingScreen(
                                                      orderId: order.id,
                                                      invoiceNumber: order.invoiceNumber,
                                                    ),
                                                  ),
                                                );
                                              },
                                            ),
                                            ElevatedButton(
                                              onPressed: () => _confirmReceiveOrder(order),
                                              style: ElevatedButton.styleFrom(
                                                backgroundColor: AppTheme.success,
                                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                                                minimumSize: Size.zero,
                                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                                              ),
                                              child: const Text('Terima Pesanan', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w800, color: Colors.white)),
                                            ),
                                          ],

                                          // 4. Completed Actions
                                          if (st == 'completed') ...[
                                            ElevatedButton.icon(
                                              icon: const Icon(Icons.star_rounded, size: 14, color: Colors.amber),
                                              label: const Text('Beri Ulasan', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w800, color: Colors.white)),
                                              style: ElevatedButton.styleFrom(
                                                backgroundColor: AppTheme.primary,
                                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                                                minimumSize: Size.zero,
                                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                                              ),
                                              onPressed: () => _showReviewSheet(order),
                                            ),
                                          ],

                                          // Detail Button Always Available
                                          OutlinedButton(
                                            onPressed: () => _showOrderDetailSheet(context, order),
                                            style: OutlinedButton.styleFrom(
                                              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                                              minimumSize: Size.zero,
                                              side: const BorderSide(color: AppTheme.border),
                                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                                            ),
                                            child: const Text('Detail', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: AppTheme.textSecondary)),
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
  // ACTIONS: PAY, CANCEL, TRACK, CONFIRM, REVIEW
  // ══════════════════════════════════════════════════

  Future<void> _payOrder(OrderModel order) async {
    setState(() => _isLoading = true);
    final result = await ApiService.payOrder(order.id);
    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['success'] == true ? 'Pembayaran berhasil dikonfirmasi! Pesanan sedang diproses toko. 🎉' : (result['message'] ?? 'Gagal memproses pembayaran.')),
          backgroundColor: result['success'] == true ? AppTheme.success : Colors.red,
          behavior: SnackBarBehavior.floating,
        ),
      );
      _fetchOrders();
    }
  }

  void _confirmCancelOrder(OrderModel order) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
        title: const Text('Batalkan Pesanan?', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
        content: Text('Apakah Anda yakin ingin membatalkan pesanan #${order.orderNumber}? Stok barang dan kuota voucher akan otomatis dikembalikan.'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Kembali')),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            onPressed: () async {
              Navigator.pop(ctx);
              setState(() => _isLoading = true);
              final res = await ApiService.cancelOrder(order.id);
              if (mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text(res['message'] ?? 'Pesanan berhasil dibatalkan.'),
                    backgroundColor: res['success'] == true ? AppTheme.success : Colors.red,
                    behavior: SnackBarBehavior.floating,
                  ),
                );
                _fetchOrders();
              }
            },
            child: const Text('Ya, Batalkan'),
          ),
        ],
      ),
    );
  }

  void _confirmReceiveOrder(OrderModel order) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
        title: const Text('Konfirmasi Terima Pesanan', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
        content: Text('Pastikan paket untuk pesanan #${order.orderNumber} telah Anda terima dengan lengkap dan dalam kondisi baik.'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Batal')),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.success),
            onPressed: () async {
              Navigator.pop(ctx);
              setState(() => _isLoading = true);
              final res = await ApiService.confirmOrderReceived(order.id);
              if (mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text(res['message'] ?? 'Pesanan selesai! Silakan berikan ulasan Anda.'),
                    backgroundColor: AppTheme.success,
                    behavior: SnackBarBehavior.floating,
                  ),
                );
                await _fetchOrders();
                _showReviewSheet(order);
              }
            },
            child: const Text('Konfirmasi Selesai'),
          ),
        ],
      ),
    );
  }

  void _showTrackingSheet(OrderModel order) async {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(15))),
      builder: (ctx) {
        return FutureBuilder<Map<String, dynamic>?>(
          future: ApiService.getOrderTracking(order.id),
          builder: (context, snapshot) {
            final data = snapshot.data;
            final timeline = (data != null && data['timeline'] is List) ? data['timeline'] as List : [];

            return Padding(
              padding: const EdgeInsets.fromLTRB(20, 16, 20, 30),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Center(child: Container(width: 36, height: 4, decoration: BoxDecoration(color: Colors.grey.shade300, borderRadius: BorderRadius.circular(2)))),
                  const SizedBox(height: 16),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Row(
                        children: [
                          const Icon(Icons.local_shipping_outlined, color: AppTheme.primary),
                          const SizedBox(width: 8),
                          const Text('Lacak Pengiriman', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
                        ],
                      ),
                      IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(ctx)),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: AppTheme.primaryLight,
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(data?['courier'] ?? 'J&T Express Regular', style: const TextStyle(fontSize: 12.5, fontWeight: FontWeight.w800, color: AppTheme.primaryDark)),
                            const SizedBox(height: 2),
                            Text('No. Resi: ${data?['tracking_number'] ?? 'NTD-' + order.orderNumber}', style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary)),
                          ],
                        ),
                        IconButton(
                          icon: const Icon(Icons.copy, size: 18, color: AppTheme.primaryDark),
                          onPressed: () {
                            Clipboard.setData(ClipboardData(text: data?['tracking_number'] ?? order.orderNumber));
                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(content: Text('Nomor resi berhasil disalin!'), behavior: SnackBarBehavior.floating),
                            );
                          },
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 16),

                  if (snapshot.connectionState == ConnectionState.waiting)
                    const Center(child: Padding(padding: EdgeInsets.all(20), child: CircularProgressIndicator()))
                  else if (timeline.isEmpty)
                    const Padding(padding: EdgeInsets.all(20), child: Center(child: Text('Data tracking belum tersedia.')))
                  else
                    ListView.builder(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      itemCount: timeline.length,
                      itemBuilder: (context, tIdx) {
                        final step = timeline[tIdx];
                        final isDone = step['is_completed'] == true;
                        final isLast = tIdx == timeline.length - 1;

                        return Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Column(
                              children: [
                                Icon(
                                  isDone ? Icons.check_circle : Icons.radio_button_unchecked,
                                  color: isDone ? AppTheme.primary : Colors.grey.shade300,
                                  size: 20,
                                ),
                                if (!isLast)
                                  Container(
                                    width: 2,
                                    height: 36,
                                    color: isDone ? AppTheme.primary.withOpacity(0.5) : Colors.grey.shade200,
                                  ),
                              ],
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Padding(
                                padding: const EdgeInsets.only(bottom: 12),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      step['title'] ?? '',
                                      style: TextStyle(
                                        fontSize: 12.5,
                                        fontWeight: isDone ? FontWeight.w800 : FontWeight.w500,
                                        color: isDone ? AppTheme.textPrimary : AppTheme.textMuted,
                                      ),
                                    ),
                                    const SizedBox(height: 2),
                                    Text(
                                      step['description'] ?? '',
                                      style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary),
                                    ),
                                    const SizedBox(height: 2),
                                    Text(
                                      step['time'] ?? '',
                                      style: const TextStyle(fontSize: 9.5, color: AppTheme.textMuted),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ],
                        );
                      },
                    ),
                  const SizedBox(height: 10),
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      onPressed: () => Navigator.pop(ctx),
                      child: const Text('Tutup'),
                    ),
                  ),
                ],
              ),
            );
          },
        );
      },
    );
  }

  void _showReviewSheet(OrderModel order) {
    int selectedStars = 5;
    final commentController = TextEditingController();
    bool isSubmitting = false;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(15))),
      builder: (ctx) {
        return StatefulBuilder(
          builder: (context, setModalState) {
            return Padding(
              padding: EdgeInsets.fromLTRB(20, 16, 20, MediaQuery.of(context).viewInsets.bottom + 20),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Center(child: Container(width: 36, height: 4, decoration: BoxDecoration(color: Colors.grey.shade300, borderRadius: BorderRadius.circular(2)))),
                  const SizedBox(height: 16),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('Beri Ulasan Produk ⭐', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
                      IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(ctx)),
                    ],
                  ),
                  const SizedBox(height: 6),
                  Text('Bagikan kepuasan belanja Anda untuk pesanan #${order.orderNumber}', style: const TextStyle(fontSize: 11.5, color: AppTheme.textSecondary)),
                  const SizedBox(height: 16),

                  // Star Selector
                  Center(
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: List.generate(5, (starIndex) {
                        final starValue = starIndex + 1;
                        return IconButton(
                          icon: Icon(
                            starValue <= selectedStars ? Icons.star_rounded : Icons.star_outline_rounded,
                            color: Colors.amber,
                            size: 36,
                          ),
                          onPressed: () => setModalState(() => selectedStars = starValue),
                        );
                      }),
                    ),
                  ),
                  Center(
                    child: Text(
                      selectedStars == 5 ? 'Sangat Puas! 😍' : (selectedStars >= 4 ? 'Puas 👍' : (selectedStars >= 3 ? 'Cukup 🙂' : 'Kurang Puas 😞')),
                      style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 12, color: AppTheme.primaryDark),
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Comment Input
                  TextField(
                    controller: commentController,
                    maxLines: 3,
                    decoration: InputDecoration(
                      hintText: 'Tuliskan ulasan mengenai kualitas produk, respon penjual, dan kecepatan pengiriman...',
                      hintStyle: const TextStyle(fontSize: 12),
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
                    ),
                  ),
                  const SizedBox(height: 20),

                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                      onPressed: isSubmitting
                          ? null
                          : () async {
                              setModalState(() => isSubmitting = true);
                              final prodId = order.firstProduct != null ? 1 : 1;
                              final res = await ApiService.submitOrderReview(order.id, prodId, selectedStars, commentController.text.trim());
                              Navigator.pop(ctx);
                              if (mounted) {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  SnackBar(
                                    content: Text(res['message'] ?? 'Ulasan Anda berhasil dikirim!'),
                                    backgroundColor: AppTheme.success,
                                    behavior: SnackBarBehavior.floating,
                                  ),
                                );
                              }
                            },
                      child: isSubmitting
                          ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                          : const Text('Kirim Ulasan', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w800)),
                    ),
                  ),
                ],
              ),
            );
          },
        );
      },
    );
  }

  void _showOrderDetailSheet(BuildContext context, OrderModel order) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(15))),
      builder: (ctx) => Padding(
        padding: const EdgeInsets.fromLTRB(20, 16, 20, 30),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(child: Container(width: 36, height: 4, decoration: BoxDecoration(color: Colors.grey.shade300, borderRadius: BorderRadius.circular(2)))),
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
                children: [
                  const Text('Kurir: J&T Express Regular', style: TextStyle(fontSize: 11.5, fontWeight: FontWeight.w700)),
                  const SizedBox(height: 4),
                  Text('No. Resi: NTD-${order.orderNumber}', style: const TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                  const SizedBox(height: 4),
                  Text(
                    order.status.toLowerCase() == 'completed' ? 'Status: Paket telah diterima.' : 'Status: Paket dalam penanganan kurir.',
                    style: TextStyle(fontSize: 11, color: _getStatusColor(order.status), fontWeight: FontWeight.w600),
                  ),
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
