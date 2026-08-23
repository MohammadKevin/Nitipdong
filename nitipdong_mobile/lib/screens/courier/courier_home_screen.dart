import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../providers/auth_provider.dart';
import '../../services/api_service.dart';
import '../auth/login_screen.dart';
import '../main_nav_screen.dart';
import 'courier_delivery_detail_screen.dart';

class CourierHomeScreen extends StatefulWidget {
  const CourierHomeScreen({Key? key}) : super(key: key);

  @override
  State<CourierHomeScreen> createState() => _CourierHomeScreenState();
}

class _CourierHomeScreenState extends State<CourierHomeScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  bool _isLoading = true;
  bool _isOnline = true;

  Map<String, dynamic> _stats = {};
  List<Map<String, dynamic>> _activeDeliveries = [];
  List<Map<String, dynamic>> _availableDeliveries = [];
  List<Map<String, dynamic>> _completedDeliveries = [];

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    _loadCourierData();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadCourierData() async {
    setState(() => _isLoading = true);
    try {
      final results = await Future.wait([
        ApiService.getCourierStatistics(),
        ApiService.getCourierDeliveries(type: 'active'),
        ApiService.getCourierDeliveries(type: 'available'),
        ApiService.getCourierDeliveries(type: 'completed'),
      ]);

      if (mounted) {
        setState(() {
          _stats = results[0] as Map<String, dynamic>;
          _activeDeliveries = results[1] as List<Map<String, dynamic>>;
          _availableDeliveries = results[2] as List<Map<String, dynamic>>;
          _completedDeliveries = results[3] as List<Map<String, dynamic>>;
          _isLoading = false;
        });
      }
    } catch (_) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  String _formatCurrency(num amount) {
    final formatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    return formatter.format(amount);
  }

  @override
  Widget build(BuildContext context) {
    final user = Provider.of<AuthProvider>(context).user;

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
                border: Border.all(color: Colors.cyanAccent, width: 1.5),
              ),
              child: const CircleAvatar(
                radius: 18,
                backgroundColor: AppTheme.primary,
                child: Icon(Icons.delivery_dining_rounded, color: Colors.white, size: 22),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Text(
                        user?.name ?? 'Kurir Mitra',
                        style: const TextStyle(color: Colors.white, fontSize: 14, fontWeight: FontWeight.w800),
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(width: 6),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                        decoration: BoxDecoration(
                          color: _isOnline ? Colors.green.withOpacity(0.2) : Colors.red.withOpacity(0.2),
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: _isOnline ? Colors.green : Colors.red, width: 0.8),
                        ),
                        child: Text(
                          _isOnline ? 'ONLINE' : 'OFFLINE',
                          style: TextStyle(
                            color: _isOnline ? Colors.greenAccent : Colors.redAccent,
                            fontSize: 9,
                            fontWeight: FontWeight.w900,
                          ),
                        ),
                      ),
                    ],
                  ),
                  const Text('Driver & Ekspedisi NitipDong', style: TextStyle(color: Colors.white60, fontSize: 11)),
                ],
              ),
            ),
            // Switch to Marketplace / Shopping Mode
            Container(
              margin: const EdgeInsets.only(right: 6),
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
                      Icon(Icons.shopping_bag_outlined, color: Colors.amberAccent, size: 14),
                      SizedBox(width: 4),
                      Text('Belanja 🛍️', style: TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.w700)),
                    ],
                  ),
                ),
              ),
            ),
            IconButton(
              icon: const Icon(Icons.logout_rounded, color: Colors.white70, size: 20),
              tooltip: 'Keluar Akun',
              onPressed: () async {
                final confirm = await showDialog<bool>(
                  context: context,
                  builder: (ctx) => AlertDialog(
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    title: const Text('Keluar dari Akun Kurir?'),
                    content: const Text('Anda akan mengakhiri sesi kurir dan beralih ke halaman login.'),
                    actions: [
                      TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Batal')),
                      ElevatedButton(onPressed: () => Navigator.pop(ctx, true), child: const Text('Keluar')),
                    ],
                  ),
                );
                if (confirm == true && mounted) {
                  await Provider.of<AuthProvider>(context, listen: false).logout();
                  Navigator.pushAndRemoveUntil(
                    context,
                    MaterialPageRoute(builder: (_) => const LoginScreen()),
                    (route) => false,
                  );
                }
              },
            ),
          ],
        ),
      ),
      body: RefreshIndicator(
        onRefresh: _loadCourierData,
        color: AppTheme.primary,
        child: Column(
          children: [
            // Header Stats Card
            Container(
              width: double.infinity,
              padding: const EdgeInsets.fromLTRB(16, 10, 16, 18),
              decoration: const BoxDecoration(
                color: AppTheme.accentNavy,
                borderRadius: BorderRadius.vertical(bottom: Radius.circular(24)),
              ),
              child: Column(
                children: [
                  // Stat cards row
                  Row(
                    children: [
                      _buildStatBox(
                        'Antaran Hari Ini',
                        '${_stats['completed_today'] ?? 0} Paket',
                        Icons.check_circle_outline_rounded,
                        Colors.cyanAccent,
                      ),
                      const SizedBox(width: 10),
                      _buildStatBox(
                        'Estimasi Komisi',
                        _formatCurrency(_stats['earnings_today'] ?? 0),
                        Icons.monetization_on_outlined,
                        Colors.amberAccent,
                      ),
                      const SizedBox(width: 10),
                      _buildStatBox(
                        'Rating Performa',
                        '⭐ ${_stats['rating'] ?? '4.95'}',
                        Icons.stars_rounded,
                        Colors.orangeAccent,
                      ),
                    ],
                  ),
                ],
              ),
            ),

            // Tab Bar Selector
            Container(
              color: Colors.white,
              child: TabBar(
                controller: _tabController,
                indicatorColor: AppTheme.primary,
                indicatorWeight: 3,
                labelColor: AppTheme.primaryDark,
                unselectedLabelColor: AppTheme.textMuted,
                labelStyle: const TextStyle(fontSize: 12.5, fontWeight: FontWeight.w800),
                tabs: [
                  Tab(text: 'Tugas Aktif (${_activeDeliveries.length})'),
                  Tab(text: 'Siap Jemput (${_availableDeliveries.length})'),
                  Tab(text: 'Riwayat (${_completedDeliveries.length})'),
                ],
              ),
            ),

            // Tab Bar Views
            Expanded(
              child: _isLoading
                  ? const Center(child: CircularProgressIndicator(color: AppTheme.primary))
                  : TabBarView(
                      controller: _tabController,
                      children: [
                        // Tab 1: Tugas Aktif
                        _buildDeliveryList(_activeDeliveries, isAvailable: false, isCompleted: false),
                        // Tab 2: Siap Jemput
                        _buildDeliveryList(_availableDeliveries, isAvailable: true, isCompleted: false),
                        // Tab 3: Riwayat
                        _buildDeliveryList(_completedDeliveries, isAvailable: false, isCompleted: true),
                      ],
                    ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatBox(String title, String value, IconData icon, Color color) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 10),
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.08),
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: Colors.white.withOpacity(0.12)),
        ),
        child: Column(
          children: [
            Icon(icon, color: color, size: 20),
            const SizedBox(height: 6),
            Text(value, style: const TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.w800)),
            const SizedBox(height: 2),
            Text(title, style: const TextStyle(color: Colors.white60, fontSize: 9.5), textAlign: TextAlign.center),
          ],
        ),
      ),
    );
  }

  Widget _buildDeliveryList(List<Map<String, dynamic>> items, {required bool isAvailable, required bool isCompleted}) {
    if (items.isEmpty) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(32),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(
                isCompleted ? Icons.history_rounded : (isAvailable ? Icons.inbox_rounded : Icons.check_circle_outline_rounded),
                size: 56,
                color: Colors.grey.shade400,
              ),
              const SizedBox(height: 12),
              Text(
                isCompleted ? 'Belum ada riwayat antaran selesai' : (isAvailable ? 'Tidak ada paket siap jemput saat ini' : 'Tidak ada tugas aktif'),
                style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: AppTheme.textPrimary),
              ),
              const SizedBox(height: 4),
              Text(
                isAvailable ? 'Tarik ke bawah untuk memuat ulang daftar antaran baru.' : 'Tugas yang Anda ambil akan muncul di sini.',
                textAlign: TextAlign.center,
                style: const TextStyle(fontSize: 11, color: AppTheme.textMuted),
              ),
            ],
          ),
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: items.length,
      itemBuilder: (context, index) {
        final item = items[index];
        return _buildDeliveryCard(item, isAvailable: isAvailable, isCompleted: isCompleted);
      },
    );
  }

  Widget _buildDeliveryCard(Map<String, dynamic> item, {required bool isAvailable, required bool isCompleted}) {
    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppTheme.border),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 10, offset: const Offset(0, 4)),
        ],
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header: Invoice & Status Badge
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  item['invoice_number'] ?? '#INV',
                  style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w800, color: AppTheme.textPrimary),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                  decoration: BoxDecoration(
                    color: isCompleted ? Colors.green.shade50 : (isAvailable ? Colors.amber.shade50 : AppTheme.primaryLight),
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(
                      color: isCompleted ? Colors.green.shade300 : (isAvailable ? Colors.amber.shade400 : AppTheme.primary.withOpacity(0.4)),
                    ),
                  ),
                  child: Text(
                    isCompleted ? 'Selesai Terkirim' : (isAvailable ? 'Siap Dijemput' : 'Sedang Diantar 🚚'),
                    style: TextStyle(
                      fontSize: 10.5,
                      fontWeight: FontWeight.w800,
                      color: isCompleted ? Colors.green.shade800 : (isAvailable ? Colors.amber.shade900 : AppTheme.primaryDark),
                    ),
                  ),
                ),
              ],
            ),
            const Divider(height: 20),

            // Pickup: Store Info
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Icon(Icons.storefront_rounded, color: AppTheme.primary, size: 20),
                const SizedBox(width: 10),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text('Alamat Penjemputan (Toko):', style: TextStyle(fontSize: 10.5, color: AppTheme.textMuted, fontWeight: FontWeight.w600)),
                      Text(item['store_name'] ?? 'Toko', style: const TextStyle(fontSize: 12.5, fontWeight: FontWeight.w800)),
                      Text(item['store_address'] ?? '-', style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary)),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),

            // Dropoff: Customer Info
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Icon(Icons.location_on_rounded, color: Colors.redAccent, size: 20),
                const SizedBox(width: 10),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text('Alamat Pengantaran (Pembeli):', style: TextStyle(fontSize: 10.5, color: AppTheme.textMuted, fontWeight: FontWeight.w600)),
                      Text('${item['recipient_name']} (${item['recipient_phone']})', style: const TextStyle(fontSize: 12.5, fontWeight: FontWeight.w800)),
                      Text(item['shipping_address'] ?? '-', style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary)),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),

            // Action Buttons
            if (isAvailable) ...[
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  icon: const Icon(Icons.touch_app_rounded, size: 18),
                  label: const Text('Ambil / Klaim Tugas Ini 🚀', style: TextStyle(fontWeight: FontWeight.w800)),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.primary,
                    padding: const EdgeInsets.symmetric(vertical: 12),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  onPressed: () async {
                    final res = await ApiService.acceptCourierTask(item['id']);
                    if (res['success'] == true && mounted) {
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(
                          content: Text('Tugas berhasil diambil! Silakan mulai perjalanan.'),
                          backgroundColor: AppTheme.success,
                          behavior: SnackBarBehavior.floating,
                        ),
                      );
                      _loadCourierData();
                    }
                  },
                ),
              ),
            ] else if (!isCompleted) ...[
              Row(
                children: [
                  Expanded(
                    child: ElevatedButton.icon(
                      icon: const Icon(Icons.navigation_rounded, size: 18),
                      label: const Text('Buka Navigasi Rute & GPS', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w800)),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppTheme.accentNavy,
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                      onPressed: () async {
                        await Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => CourierDeliveryDetailScreen(orderData: item),
                          ),
                        );
                        _loadCourierData();
                      },
                    ),
                  ),
                ],
              ),
            ] else ...[
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text('Status Pengantaran:', style: TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                  Text(
                    'Diterima Pembeli ✅',
                    style: TextStyle(fontSize: 12, fontWeight: FontWeight.w800, color: Colors.green.shade700),
                  ),
                ],
              ),
            ],
          ],
        ),
      ),
    );
  }
}
