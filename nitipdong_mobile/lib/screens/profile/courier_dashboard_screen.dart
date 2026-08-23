import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../theme/app_theme.dart';
import '../../services/api_service.dart';

class CourierDashboardScreen extends StatefulWidget {
  const CourierDashboardScreen({Key? key}) : super(key: key);

  @override
  State<CourierDashboardScreen> createState() => _CourierDashboardScreenState();
}

class _CourierDashboardScreenState extends State<CourierDashboardScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  List<Map<String, dynamic>> _orders = [];
  bool _isLoading = true;
  String _searchQuery = '';

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    _fetchOrders();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _fetchOrders() async {
    setState(() => _isLoading = true);
    final data = await ApiService.getCourierOrders();
    if (mounted) {
      setState(() {
        _orders = data;
        _isLoading = false;
      });
    }
  }

  String _formatCurrency(double amount) {
    final formatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    return formatter.format(amount);
  }

  List<Map<String, dynamic>> _getFilteredOrders(String status) {
    return _orders.where((o) {
      final matchesStatus = o['status'] == status;
      final matchesSearch = o['invoice_number'].toString().toLowerCase().contains(_searchQuery.toLowerCase()) ||
          o['recipient_name'].toString().toLowerCase().contains(_searchQuery.toLowerCase());
      return matchesStatus && matchesSearch;
    }).toList();
  }

  Future<void> _handlePickup(dynamic orderId, String invoiceNumber) async {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Ambil Paket?'),
        content: Text('Apakah Anda yakin ingin mempickup paket untuk pesanan $invoiceNumber? Status pesanan akan berubah menjadi "Sedang Dikirim".'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () async {
              Navigator.pop(ctx);
              setState(() => _isLoading = true);
              final res = await ApiService.pickupCourierOrder(orderId);
              if (res['success'] == true && mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text('Pesanan $invoiceNumber berhasil diambil! No Resi: ${res['tracking_number']}'),
                    backgroundColor: Colors.green,
                  ),
                );
                _fetchOrders();
              } else if (mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text(res['message'] ?? 'Gagal mengambil paket.'),
                    backgroundColor: Colors.redAccent,
                  ),
                );
                setState(() => _isLoading = false);
              }
            },
            child: const Text('Ambil'),
          ),
        ],
      ),
    );
  }

  Future<void> _handleDeliver(dynamic orderId, String invoiceNumber) async {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Selesaikan Pengiriman?'),
        content: Text('Apakah Anda yakin paket untuk pesanan $invoiceNumber telah sampai di tujuan dan diterima dengan baik oleh penerima?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () async {
              Navigator.pop(ctx);
              setState(() => _isLoading = true);
              final res = await ApiService.deliverCourierOrder(orderId);
              if (res['success'] == true && mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text('Pengiriman pesanan $invoiceNumber telah selesai! 🎉'),
                    backgroundColor: Colors.green,
                  ),
                );
                _fetchOrders();
              } else if (mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text(res['message'] ?? 'Gagal menyelesaikan pengiriman.'),
                    backgroundColor: Colors.redAccent,
                  ),
                );
                setState(() => _isLoading = false);
              }
            },
            child: const Text('Selesai'),
          ),
        ],
      ),
    );
  }

  void _contactRecipient(String name, String phone) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (ctx) => SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 16),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Hubungi $name',
                style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppTheme.accentNavy),
              ),
              const SizedBox(height: 12),
              ListTile(
                leading: Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(color: Colors.green.shade50, shape: BoxShape.circle),
                  child: const Icon(Icons.phone_rounded, color: Colors.green),
                ),
                title: const Text('Telepon Langsung', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13.5)),
                subtitle: Text(phone, style: const TextStyle(fontSize: 12)),
                onTap: () {
                  Navigator.pop(ctx);
                  // Action dial
                },
              ),
              const SizedBox(height: 8),
              ListTile(
                leading: Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(color: Colors.blue.shade50, shape: BoxShape.circle),
                  child: const Icon(Icons.chat_bubble_rounded, color: Colors.blue),
                ),
                title: const Text('Kirim WhatsApp', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13.5)),
                subtitle: const Text('Hubungi via chat messenger', style: TextStyle(fontSize: 12)),
                onTap: () {
                  Navigator.pop(ctx);
                  // Action wa
                },
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _showAddressMapSim(String address) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Row(
          children: const [
            Icon(Icons.map_rounded, color: AppTheme.primary),
            SizedBox(width: 8),
            Text('Peta Pengiriman', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
          ],
        ),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Alamat Tujuan:',
              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: AppTheme.textSecondary),
            ),
            const SizedBox(height: 4),
            Text(
              address,
              style: const TextStyle(fontSize: 12),
            ),
            const SizedBox(height: 16),
            Container(
              height: 140,
              decoration: BoxDecoration(
                color: Colors.grey.shade100,
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: Colors.grey.shade300),
              ),
              child: const Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.location_on_rounded, color: Colors.redAccent, size: 36),
                    SizedBox(height: 8),
                    Text('Simulasi Peta Rute Pengantaran', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: AppTheme.textMuted)),
                  ],
                ),
              ),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('Tutup'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Dashboard Kurir 🚚'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _fetchOrders,
          ),
        ],
        bottom: TabBar(
          controller: _tabController,
          labelColor: AppTheme.primary,
          unselectedLabelColor: AppTheme.textMuted,
          indicatorColor: AppTheme.primary,
          labelStyle: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12),
          tabs: const [
            Tab(text: 'Perlu Dikirim'),
            Tab(text: 'Sedang Dikirim'),
            Tab(text: 'Selesai'),
          ],
        ),
      ),
      body: Column(
        children: [
          // Search Input
          Padding(
            padding: const EdgeInsets.all(12),
            child: TextField(
              onChanged: (val) => setState(() => _searchQuery = val),
              decoration: InputDecoration(
                hintText: 'Cari No. Invoice atau Penerima...',
                prefixIcon: const Icon(Icons.search, size: 20),
                contentPadding: const EdgeInsets.symmetric(vertical: 8),
                fillColor: Colors.white,
              ),
            ),
          ),

          // Tab Bar View content
          Expanded(
            child: TabBarView(
              controller: _tabController,
              children: [
                _buildOrderList('processing'),
                _buildOrderList('shipped'),
                _buildOrderList('completed'),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildOrderList(String status) {
    final list = _getFilteredOrders(status);

    if (_isLoading) {
      return const Center(child: CircularProgressIndicator(color: AppTheme.primary));
    }

    if (list.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.local_shipping_outlined, size: 48, color: Colors.grey.shade300),
            const SizedBox(height: 8),
            Text(
              'Tidak ada pesanan untuk status ini',
              style: TextStyle(fontSize: 12, color: Colors.grey.shade500),
            ),
          ],
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: _fetchOrders,
      child: ListView.builder(
        padding: const EdgeInsets.all(12),
        itemCount: list.length,
        itemBuilder: (context, index) {
          final item = list[index];
          final id = item['id'];
          final invoice = item['invoice_number'] ?? 'INV-ORD';
          final name = item['recipient_name'] ?? 'Pelanggan';
          final phone = item['recipient_phone'] ?? '08123456789';
          final address = item['shipping_address'] ?? 'No Address';
          final courier = item['courier'] ?? 'J&T Express';
          final trk = item['tracking_number'] ?? '-';
          final date = item['created_at'] ?? '';
          final total = (item['total_amount'] as num?)?.toDouble() ?? 0.0;
          final prod = item['first_product'];

          return Container(
            margin: const EdgeInsets.only(bottom: 12),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(14),
              border: Border.all(color: AppTheme.border),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Header (Invoice & Date)
                Padding(
                  padding: const EdgeInsets.all(12),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            invoice,
                            style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 13, color: AppTheme.accentNavy),
                          ),
                          Text(
                            date,
                            style: const TextStyle(fontSize: 10, color: AppTheme.textMuted),
                          ),
                        ],
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                        decoration: BoxDecoration(
                          color: status == 'processing'
                              ? Colors.orange.shade50
                              : status == 'shipped'
                                  ? Colors.blue.shade50
                                  : Colors.green.shade50,
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: Text(
                          status == 'processing'
                              ? 'Perlu Pickup'
                              : status == 'shipped'
                                  ? 'Mengantar'
                                  : 'Selesai',
                          style: TextStyle(
                            fontSize: 9,
                            fontWeight: FontWeight.bold,
                            color: status == 'processing'
                                ? Colors.orange.shade800
                                : status == 'shipped'
                                    ? Colors.blue.shade800
                                    : Colors.green.shade800,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                const Divider(height: 1),

                // Recipient & Address Details
                Padding(
                  padding: const EdgeInsets.all(12),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          const Icon(Icons.person_outline_rounded, size: 16, color: AppTheme.textSecondary),
                          const SizedBox(width: 6),
                          Text(
                            'Penerima: $name',
                            style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: AppTheme.textPrimary),
                          ),
                        ],
                      ),
                      const SizedBox(height: 4),
                      Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Icon(Icons.location_on_outlined, size: 16, color: Colors.redAccent),
                          const SizedBox(width: 6),
                          Expanded(
                            child: Text(
                              address,
                              style: const TextStyle(fontSize: 11.5, color: AppTheme.textSecondary),
                            ),
                          ),
                        ],
                      ),
                      if (status != 'processing' && trk != '-') ...[
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            const Icon(Icons.tag_rounded, size: 15, color: AppTheme.textMuted),
                            const SizedBox(width: 6),
                            Text(
                              'Resi: $trk',
                              style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppTheme.primaryDark),
                            ),
                          ],
                        ),
                      ],
                    ],
                  ),
                ),
                const Divider(height: 1),

                // Product Preview
                if (prod != null)
                  Padding(
                    padding: const EdgeInsets.all(12),
                    child: Row(
                      children: [
                        Container(
                          width: 44,
                          height: 44,
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(color: AppTheme.border),
                            image: DecorationImage(
                              image: NetworkImage(prod['image_url']),
                              fit: BoxFit.cover,
                            ),
                          ),
                        ),
                        const SizedBox(width: 10),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                prod['name'],
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                                style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
                              ),
                              Text(
                                '${prod['quantity']} barang x ${_formatCurrency(prod['price'])}',
                                style: const TextStyle(fontSize: 10.5, color: AppTheme.textSecondary),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                if (prod != null) const Divider(height: 1),

                // Footer (Total Amount & Actions)
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text('Total Tagihan', style: TextStyle(fontSize: 9, color: AppTheme.textMuted)),
                          Text(
                            _formatCurrency(total),
                            style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w900, color: AppTheme.accentNavy),
                          ),
                        ],
                      ),
                      Row(
                        children: [
                          IconButton(
                            icon: const Icon(Icons.phone_outlined, size: 20, color: AppTheme.primary),
                            tooltip: 'Hubungi Penerima',
                            onPressed: () => _contactRecipient(name, phone),
                          ),
                          IconButton(
                            icon: const Icon(Icons.map_outlined, size: 20, color: AppTheme.primary),
                            tooltip: 'Peta Rute',
                            onPressed: () => _showAddressMapSim(address),
                          ),
                          const SizedBox(width: 4),
                          if (status == 'processing')
                            ElevatedButton(
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.green,
                                foregroundColor: Colors.white,
                                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                                minimumSize: Size.zero,
                                tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                              ),
                              onPressed: () => _handlePickup(id, invoice),
                              child: const Text('Pickup', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                            ),
                          if (status == 'shipped')
                            ElevatedButton(
                              style: ElevatedButton.styleFrom(
                                backgroundColor: AppTheme.primary,
                                foregroundColor: Colors.white,
                                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                                minimumSize: Size.zero,
                                tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                              ),
                              onPressed: () => _handleDeliver(id, invoice),
                              child: const Text('Selesai', style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                            ),
                        ],
                      ),
                    ],
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}
