import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../services/api_service.dart';
import 'store_approval_screen.dart';
import 'product_moderation_screen.dart';
import 'category_management_screen.dart';
import 'flash_sale_management_screen.dart';

class AdminDashboardScreen extends StatefulWidget {
  const AdminDashboardScreen({Key? key}) : super(key: key);

  @override
  State<AdminDashboardScreen> createState() => _AdminDashboardScreenState();
}

class _AdminDashboardScreenState extends State<AdminDashboardScreen> {
  bool _isLoading = true;
  Map<String, dynamic> _stats = {
    'pending_stores': 0,
    'total_products': 0,
    'total_categories': 0,
    'active_flash_sales': 0,
  };

  @override
  void initState() {
    super.initState();
    _loadDashboardStats();
  }

  Future<void> _loadDashboardStats() async {
    setState(() => _isLoading = true);
    final res = await ApiService.getAdminStats();
    if (mounted) {
      setState(() {
        if (res['success'] == true) {
          _stats = res['data'] ?? _stats;
        }
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: const Text('Panel Admin Operasional'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh_rounded),
            onPressed: _loadDashboardStats,
          ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadDashboardStats,
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Header Banner card
                    Container(
                      padding: const EdgeInsets.all(20),
                      width: double.infinity,
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(
                          colors: [AppTheme.primary, AppTheme.primaryDark],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                        borderRadius: BorderRadius.circular(16),
                        boxShadow: [
                          BoxShadow(
                            color: AppTheme.primary.withOpacity(0.3),
                            blurRadius: 10,
                            offset: const Offset(0, 4),
                          )
                        ],
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Selamat Datang, Admin!',
                            style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            'Kelola persetujuan toko, moderasi produk, kategori, dan flash sale hari ini.',
                            style: TextStyle(color: Colors.white.withOpacity(0.85), fontSize: 12),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 24),

                    const Text(
                      'Statistik Sistem',
                      style: TextStyle(fontSize: 14, fontWeight: FontWeight.w800, color: AppTheme.textPrimary),
                    ),
                    const SizedBox(height: 12),

                    // Grid stats
                    GridView.count(
                      crossAxisCount: 2,
                      crossAxisSpacing: 12,
                      mainAxisSpacing: 12,
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      children: [
                        _buildStatCard(
                          context,
                          title: 'Toko Pending',
                          value: _stats['pending_stores'].toString(),
                          icon: Icons.storefront_rounded,
                          color: Colors.orange,
                          onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const StoreApprovalScreen())),
                        ),
                        _buildStatCard(
                          context,
                          title: 'Total Produk',
                          value: _stats['total_products'].toString(),
                          icon: Icons.inventory_2_rounded,
                          color: Colors.blue,
                          onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const ProductModerationScreen())),
                        ),
                        _buildStatCard(
                          context,
                          title: 'Total Kategori',
                          value: _stats['total_categories'].toString(),
                          icon: Icons.category_rounded,
                          color: Colors.purple,
                          onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const CategoryManagementScreen())),
                        ),
                        _buildStatCard(
                          context,
                          title: 'Flash Sale Aktif',
                          value: _stats['active_flash_sales'].toString(),
                          icon: Icons.flash_on_rounded,
                          color: Colors.red,
                          onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const FlashSaleManagementScreen())),
                        ),
                      ],
                    ),
                    const SizedBox(height: 24),

                    const Text(
                      'Menu Cepat Manajemen',
                      style: TextStyle(fontSize: 14, fontWeight: FontWeight.w800, color: AppTheme.textPrimary),
                    ),
                    const SizedBox(height: 12),

                    _buildMenuTile(
                      context,
                      title: 'Persetujuan Registrasi Toko',
                      subtitle: 'Setujui atau tolak pengajuan toko baru',
                      icon: Icons.how_to_reg_rounded,
                      color: Colors.orange,
                      badge: _stats['pending_stores'] > 0 ? _stats['pending_stores'].toString() : null,
                      onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const StoreApprovalScreen())),
                    ),
                    _buildMenuTile(
                      context,
                      title: 'Moderasi Katalog Produk',
                      subtitle: 'Aktifkan atau takedown produk melanggar',
                      icon: Icons.gavel_rounded,
                      color: Colors.blue,
                      onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const ProductModerationScreen())),
                    ),
                    _buildMenuTile(
                      context,
                      title: 'Manajemen Kategori Belanja',
                      subtitle: 'Tambah, edit, dan hapus kategori induk',
                      icon: Icons.grid_view_rounded,
                      color: Colors.purple,
                      onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const CategoryManagementScreen())),
                    ),
                    _buildMenuTile(
                      context,
                      title: 'Event Flash Sale Promosi',
                      subtitle: 'Kelola jadwal dan produk diskon kilat',
                      icon: Icons.bolt_rounded,
                      color: Colors.red,
                      onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const FlashSaleManagementScreen())),
                    ),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildStatCard(
    BuildContext context, {
    required String title,
    required String value,
    required IconData icon,
    required Color color,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: Colors.grey.shade200),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.02),
              blurRadius: 6,
              offset: const Offset(0, 3),
            )
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: color.withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, color: color, size: 24),
            ),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  value,
                  style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: AppTheme.textPrimary),
                ),
                const SizedBox(height: 2),
                Text(
                  title,
                  style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary, fontWeight: FontWeight.w600),
                ),
              ],
            )
          ],
        ),
      ),
    );
  }

  Widget _buildMenuTile(
    BuildContext context, {
    required String title,
    required String subtitle,
    required IconData icon,
    required Color color,
    String? badge,
    required VoidCallback onTap,
  }) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: ListTile(
        onTap: onTap,
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
        leading: Container(
          padding: const EdgeInsets.all(10),
          decoration: BoxDecoration(
            color: color.withOpacity(0.1),
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(icon, color: color, size: 22),
        ),
        title: Text(
          title,
          style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: AppTheme.textPrimary),
        ),
        subtitle: Text(
          subtitle,
          style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary),
        ),
        trailing: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            if (badge != null)
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                decoration: BoxDecoration(
                  color: Colors.redAccent,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Text(
                  badge,
                  style: const TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold),
                ),
              ),
            const SizedBox(width: 4),
            const Icon(Icons.arrow_forward_ios_rounded, size: 14, color: AppTheme.textMuted),
          ],
        ),
      ),
    );
  }
}
