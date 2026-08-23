import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../theme/app_theme.dart';
import '../../services/api_service.dart';
import '../main_nav_screen.dart';

class AdminDashboardScreen extends StatefulWidget {
  const AdminDashboardScreen({Key? key}) : super(key: key);

  @override
  State<AdminDashboardScreen> createState() => _AdminDashboardScreenState();
}

class _AdminDashboardScreenState extends State<AdminDashboardScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  bool _isLoading = true;

  Map<String, dynamic>? _dashboardData;
  List<Map<String, dynamic>> _users = [];
  List<Map<String, dynamic>> _stores = [];
  bool _isMaintenance = false;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 4, vsync: this);
    _loadAdminData();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadAdminData() async {
    setState(() => _isLoading = true);
    try {
      final results = await Future.wait([
        ApiService.getAdminDashboard(),
        ApiService.getAdminUsers(),
        ApiService.getAdminStores(),
      ]);

      if (mounted) {
        setState(() {
          _dashboardData = results[0] as Map<String, dynamic>?;
          _users = results[1] as List<Map<String, dynamic>>;
          _stores = results[2] as List<Map<String, dynamic>>;
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

  Future<void> _toggleStore(int storeId) async {
    final success = await ApiService.toggleAdminStoreStatus(storeId);
    if (success && mounted) {
      _loadAdminData();
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Status toko berhasil diperbarui!'), backgroundColor: Colors.green),
      );
    }
  }

  Future<void> _toggleMaintenance(bool val) async {
    final success = await ApiService.toggleAdminMaintenance(val);
    if (success && mounted) {
      setState(() => _isMaintenance = val);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(val ? 'Mode Maintenance diaktifkan! 🛠️' : 'Sistem kembali normal (Live)! 🟢'),
          backgroundColor: val ? Colors.orange : Colors.green,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: const Color(0xFF0F172A),
        elevation: 0,
        automaticallyImplyLeading: false,
        title: const Row(
          children: [
            CircleAvatar(
              radius: 16,
              backgroundColor: Colors.purpleAccent,
              child: Icon(Icons.admin_panel_settings_rounded, color: Colors.white, size: 20),
            ),
            SizedBox(width: 10),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Admin Panel Platform 👑', style: TextStyle(color: Colors.white, fontSize: 14, fontWeight: FontWeight.w800)),
                Text('Super Admin Control Center', style: TextStyle(color: Colors.white60, fontSize: 10.5)),
              ],
            ),
          ],
        ),
        actions: [
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
          indicatorColor: Colors.purpleAccent,
          indicatorWeight: 3,
          labelColor: Colors.purpleAccent,
          unselectedLabelColor: Colors.white60,
          labelStyle: const TextStyle(fontSize: 11, fontWeight: FontWeight.w800),
          tabs: const [
            Tab(icon: Icon(Icons.analytics_rounded, size: 16), text: 'Metrik'),
            Tab(icon: Icon(Icons.store_rounded, size: 16), text: 'Toko'),
            Tab(icon: Icon(Icons.people_alt_rounded, size: 16), text: 'Pengguna'),
            Tab(icon: Icon(Icons.settings_suggest_rounded, size: 16), text: 'Sistem'),
          ],
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Colors.purpleAccent))
          : RefreshIndicator(
              onRefresh: _loadAdminData,
              child: TabBarView(
                controller: _tabController,
                children: [
                  _buildMetricsTab(),
                  _buildStoresTab(),
                  _buildUsersTab(),
                  _buildSystemTab(),
                ],
              ),
            ),
    );
  }

  // TAB 1: METRIK PLATFORM
  Widget _buildMetricsTab() {
    final gmv = _dashboardData?['total_gmv'] ?? 0;
    final usersCount = _dashboardData?['total_users'] ?? 0;
    final storesCount = _dashboardData?['total_stores'] ?? 0;
    final couriersCount = _dashboardData?['total_couriers'] ?? 0;
    final ordersCount = _dashboardData?['total_orders'] ?? 0;
    final recentOrders = (_dashboardData?['recent_orders'] as List?) ?? [];

    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // GMV Card
          Container(
            padding: const EdgeInsets.all(18),
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                colors: [Color(0xFF3B0764), Color(0xFF1E1B4B)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(18),
              boxShadow: [
                BoxShadow(color: Colors.purple.withOpacity(0.2), blurRadius: 12, offset: const Offset(0, 4)),
              ],
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text('Total Transaksi Platform (GMV)', style: TextStyle(color: Colors.white70, fontSize: 12, fontWeight: FontWeight.w600)),
                    Icon(Icons.monetization_on_rounded, color: Colors.amberAccent, size: 22),
                  ],
                ),
                const SizedBox(height: 8),
                Text(
                  _formatRupiah(gmv),
                  style: const TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.w900),
                ),
                const SizedBox(height: 10),
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                      decoration: BoxDecoration(color: Colors.white10, borderRadius: BorderRadius.circular(8)),
                      child: Text('Total $ordersCount Pesanan Diproses', style: const TextStyle(color: Colors.cyanAccent, fontSize: 11, fontWeight: FontWeight.bold)),
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
              _buildStatCard('Total Pengguna', '$usersCount Akun', Icons.people_outline_rounded, Colors.blue),
              _buildStatCard('Toko Mitra Aktif', '$storesCount Toko', Icons.storefront_rounded, Colors.amber),
              _buildStatCard('Mitra Driver Kurir', '$couriersCount Kurir', Icons.delivery_dining_rounded, Colors.green),
              _buildStatCard('Total Pesanan', '$ordersCount Transaksi', Icons.receipt_long_rounded, Colors.purple),
            ],
          ),
          const SizedBox(height: 20),

          // Recent Orders Header
          const Text('Transaksi Terbaru Platform ⏱️', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w800)),
          const SizedBox(height: 10),

          ListView.builder(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: recentOrders.length,
            itemBuilder: (ctx, idx) {
              final o = recentOrders[idx];
              return Container(
                margin: const EdgeInsets.only(bottom: 8),
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: AppTheme.border),
                ),
                child: Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(color: Colors.purple.shade50, borderRadius: BorderRadius.circular(8)),
                      child: const Icon(Icons.receipt_outlined, color: Colors.purple, size: 18),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(o['invoice_number'] ?? '', style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w800)),
                          Text('${o['customer_name']} • Toko ${o['store_name']}', style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary)),
                        ],
                      ),
                    ),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        Text(_formatRupiah(o['total_amount'] ?? 0), style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w800, color: AppTheme.primary)),
                        Text(o['status'] ?? '', style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w700, color: Colors.green)),
                      ],
                    ),
                  ],
                ),
              );
            },
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
          Text(value, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900, color: AppTheme.textPrimary)),
        ],
      ),
    );
  }

  // TAB 2: MANAJEMEN TOKO
  Widget _buildStoresTab() {
    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _stores.length,
      itemBuilder: (ctx, idx) {
        final s = _stores[idx];
        final isActive = s['is_active'] == true;

        return Container(
          margin: const EdgeInsets.only(bottom: 10),
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: AppTheme.border),
          ),
          child: Row(
            children: [
              CircleAvatar(
                radius: 20,
                backgroundColor: isActive ? Colors.amber.shade50 : Colors.red.shade50,
                child: Icon(Icons.storefront_rounded, color: isActive ? Colors.amber : Colors.red, size: 22),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(s['name'] ?? '', style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w800)),
                    Text('Pemilik: ${s['owner_name']} • ${s['city']}', style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary)),
                    Text('Telepon: ${s['phone']}', style: const TextStyle(fontSize: 10.5, color: AppTheme.textMuted)),
                  ],
                ),
              ),
              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: isActive ? Colors.red.shade50 : Colors.green.shade50,
                  foregroundColor: isActive ? Colors.red : Colors.green,
                  elevation: 0,
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                ),
                onPressed: () => _toggleStore(s['id']),
                child: Text(isActive ? 'Suspend' : 'Aktifkan', style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w800)),
              ),
            ],
          ),
        );
      },
    );
  }

  // TAB 3: MANAJEMEN PENGGUNA
  Widget _buildUsersTab() {
    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _users.length,
      itemBuilder: (ctx, idx) {
        final u = _users[idx];
        final role = u['role'] ?? 'customer';

        Color roleColor = Colors.blue;
        if (role == 'super_admin' || role == 'admin') roleColor = Colors.purple;
        if (role == 'courier') roleColor = Colors.green;
        if (role == 'seller') roleColor = Colors.amber;

        return Container(
          margin: const EdgeInsets.only(bottom: 8),
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: AppTheme.border),
          ),
          child: Row(
            children: [
              CircleAvatar(
                radius: 18,
                backgroundColor: roleColor.withOpacity(0.1),
                child: Icon(Icons.person_outline_rounded, color: roleColor, size: 20),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(u['name'] ?? '', style: const TextStyle(fontSize: 12.5, fontWeight: FontWeight.w800)),
                    Text(u['email'] ?? '', style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary)),
                  ],
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(color: roleColor.withOpacity(0.1), borderRadius: BorderRadius.circular(6)),
                child: Text(
                  role.toString().toUpperCase(),
                  style: TextStyle(color: roleColor, fontSize: 9.5, fontWeight: FontWeight.w900),
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  // TAB 4: KONTROL SISTEM
  Widget _buildSystemTab() {
    return Padding(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: AppTheme.border),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Row(
                  children: [
                    Icon(Icons.build_circle_outlined, color: Colors.orange, size: 24),
                    SizedBox(width: 12),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('Mode Pemeliharaan (Maintenance)', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w800)),
                        Text('Kunci aplikasi saat sedang migrasi server', style: TextStyle(fontSize: 11, color: AppTheme.textSecondary)),
                      ],
                    ),
                  ],
                ),
                Switch(
                  value: _isMaintenance,
                  activeColor: Colors.orange,
                  onChanged: _toggleMaintenance,
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),

          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: AppTheme.border),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text('Informasi Engine Server', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w800)),
                const SizedBox(height: 8),
                _buildInfoRow('Backend Endpoint', 'https://budayakita.com/api/v1'),
                _buildInfoRow('Versi Rilis Mobile', 'v1.1.7+15'),
                _buildInfoRow('Midtrans Status', 'Production Core API Active 🟢'),
                _buildInfoRow('Gudang Regional Hub', '10 Kota Utama Terdaftar 🏢'),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary)),
          Text(value, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppTheme.textPrimary)),
        ],
      ),
    );
  }
}
