import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:intl/intl.dart';
import 'package:local_auth/local_auth.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../theme/app_theme.dart';
import '../../providers/auth_provider.dart';
import '../../providers/cart_provider.dart';
import '../../services/api_service.dart';
import '../auth/login_screen.dart';
import '../cart/cart_screen.dart';
import '../orders/orders_screen.dart';
import 'ai_support_screen.dart';
import '../update/app_update_progress_screen.dart';
import '../courier/courier_home_screen.dart';
import '../courier/courier_registration_screen.dart';
import '../seller/seller_dashboard_screen.dart';
import '../seller/seller_registration_screen.dart';
import '../admin/admin_dashboard_screen.dart';
import '../admin/store_approval_screen.dart';
import '../admin/product_moderation_screen.dart';
import '../admin/flash_sale_management_screen.dart';
import '../admin/category_management_screen.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({Key? key}) : super(key: key);

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  String _currentAddress = 'Jl. Raya Darmo No. 42, Wonokromo, Surabaya, Jawa Timur 60241';
  String _recipientName = 'Mohammad Kevin Arif Rudianto';
  String _recipientPhone = '081234567890';
  List<Map<String, dynamic>> _availableVouchers = [];

  @override
  void initState() {
    super.initState();
    _loadInitialData();
  }

  Future<void> _loadInitialData() async {
    await Future.wait([
      _loadSavedAddress(),
      _loadVouchers(),
    ]);
  }

  Future<void> _loadSavedAddress() async {
    final data = await ApiService.getSavedAddress();
    if (mounted) {
      setState(() {
        if (data['full_address'] != null && data['full_address']!.isNotEmpty) {
          _currentAddress = data['full_address']!;
        }
        if (data['recipient_name'] != null && data['recipient_name']!.isNotEmpty) {
          _recipientName = data['recipient_name']!;
        }
        if (data['phone'] != null && data['phone']!.isNotEmpty) {
          _recipientPhone = data['phone']!;
        }
      });
    }
  }



  Future<void> _loadVouchers() async {
    final vouchers = await ApiService.getAvailableVouchers();
    if (mounted) {
      setState(() {
        _availableVouchers = vouchers;
      });
    }
  }

  String _formatCurrency(double amount) {
    final formatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    return formatter.format(amount);
  }

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);
    final user = authProvider.user;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Akun Saya'),
        automaticallyImplyLeading: false,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            // 1. USER HEADER CARD (Max radius 14px, fixed clean layout)
            Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: AppTheme.border),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.02),
                    blurRadius: 6,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: authProvider.isAuthenticated && user != null
                  ? Row(
                      children: [
                        CircleAvatar(
                          radius: 26,
                          backgroundColor: AppTheme.primaryLight,
                          backgroundImage: user.avatarUrl != null ? NetworkImage(user.avatarUrl!) : null,
                          child: user.avatarUrl == null
                              ? Text(
                                  user.name.isNotEmpty ? user.name[0].toUpperCase() : 'U',
                                  style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: AppTheme.primaryDark),
                                )
                              : null,
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                user.name,
                                style: const TextStyle(fontSize: 14.5, fontWeight: FontWeight.w800),
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                              ),
                              const SizedBox(height: 2),
                              Text(user.email, style: const TextStyle(fontSize: 11.5, color: AppTheme.textMuted)),
                              const SizedBox(height: 4),
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 1.5),
                                decoration: BoxDecoration(
                                  color: AppTheme.primaryLight,
                                  borderRadius: BorderRadius.circular(4),
                                ),
                                child: Text(
                                  user.role.toUpperCase(),
                                  style: const TextStyle(fontSize: 8.5, fontWeight: FontWeight.w800, color: AppTheme.primaryDark),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    )
                  : Row(
                      children: [
                        const CircleAvatar(
                          radius: 24,
                          backgroundColor: AppTheme.primaryLight,
                          child: Icon(Icons.person_outline, color: AppTheme.primary, size: 24),
                        ),
                        const SizedBox(width: 12),
                        const Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('Belum Masuk Akun', style: TextStyle(fontSize: 13.5, fontWeight: FontWeight.w800)),
                              SizedBox(height: 2),
                              Text('Masuk untuk nikmati jastip & promo menarik', style: TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                            ],
                          ),
                        ),
                        ElevatedButton(
                          onPressed: () {
                            Navigator.push(context, MaterialPageRoute(builder: (context) => const LoginScreen()));
                          },
                          style: ElevatedButton.styleFrom(
                            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                          ),
                          child: const Text('Masuk', style: TextStyle(fontSize: 12)),
                        ),
                      ],
                    ),
            ),
            const SizedBox(height: 12),

            // 2. QUICK STATS CARD (Orders, Wishlist, Cart)
            if (authProvider.isAuthenticated && user != null) ...[
              Consumer<CartProvider>(
                builder: (context, cartProvider, child) {
                  return Container(
                    padding: const EdgeInsets.symmetric(vertical: 12),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(14),
                      border: Border.all(color: AppTheme.border),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceAround,
                      children: [
                        _buildStatItem(
                          'Pesanan',
                          user.ordersCount.toString(),
                          Icons.receipt_long_outlined,
                          onTap: () {
                            Navigator.push(context, MaterialPageRoute(builder: (context) => const OrdersScreen()));
                          },
                        ),
                        _buildStatDivider(),
                        _buildStatItem(
                          'Wishlist',
                          user.wishlistCount.toString(),
                          Icons.favorite_border_rounded,
                          onTap: () => _showWishlistModal(context),
                        ),
                        _buildStatDivider(),
                        _buildStatItem(
                          'Keranjang',
                          cartProvider.itemCount.toString(),
                          Icons.shopping_cart_outlined,
                          onTap: () {
                            Navigator.push(context, MaterialPageRoute(builder: (context) => const CartScreen()));
                          },
                        ),
                      ],
                    ),
                  );
                },
              ),
              const SizedBox(height: 12),
            ],

            // 3. MAIN MENU LIST (Kupon, Alamat, Bantuan AI, Toko)
            Container(
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: AppTheme.border),
              ),
              child: Column(
                children: [
                  // 1. Voucher & Kupon Saya
                  _buildMenuItem(
                    icon: Icons.confirmation_number_outlined,
                    title: 'Voucher & Kupon Saya',
                    badge: '${_availableVouchers.isNotEmpty ? _availableVouchers.length : 3} Kupon Aktif',
                    onTap: () => _showVouchersSheet(context),
                  ),
                  _buildMenuDivider(),

                  // 3. Daftar Alamat Pengiriman
                  _buildMenuItem(
                    icon: Icons.location_on_outlined,
                    title: 'Daftar Alamat Pengiriman',
                    badge: null,
                    onTap: () => _showAddressSheet(context),
                  ),
                  _buildMenuDivider(),

                  // 4. Pusat Bantuan & CS (AI Customer Assistant)
                  _buildMenuItem(
                    icon: Icons.smart_toy_outlined,
                    title: 'Pusat Bantuan (Asisten AI)',
                    badge: 'Gemini AI 🤖',
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (context) => const AiSupportScreen()),
                      );
                    },
                  ),
                  _buildMenuDivider(),

                  // 5. SUPER ADMIN SECTION (Khusus Role super_admin)
                  if (authProvider.isAuthenticated && user != null && user.role?.toLowerCase() == 'super_admin') ...[
                    _buildMenuDivider(),
                    _buildMenuItem(
                      icon: Icons.admin_panel_settings_rounded,
                      title: 'Panel Kontrol Super Admin',
                      badge: 'Super Admin 👑',
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(builder: (context) => const AdminDashboardScreen()),
                        );
                      },
                    ),
                    _buildMenuDivider(),
                    _buildMenuItem(
                      icon: Icons.verified_user_rounded,
                      title: 'Persetujuan Toko Mitra',
                      badge: 'Verifikasi 🏪',
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(builder: (context) => const StoreApprovalScreen()),
                        );
                      },
                    ),
                    _buildMenuDivider(),
                    _buildMenuItem(
                      icon: Icons.inventory_2_rounded,
                      title: 'Moderasi & Validasi Produk',
                      badge: 'Katalog 📦',
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(builder: (context) => const ProductModerationScreen()),
                        );
                      },
                    ),
                    _buildMenuDivider(),
                    _buildMenuItem(
                      icon: Icons.bolt_rounded,
                      title: 'Manajemen Flash Sale & Promo',
                      badge: 'Flash Sale ⚡',
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(builder: (context) => const FlashSaleManagementScreen()),
                        );
                      },
                    ),
                    _buildMenuDivider(),
                    _buildMenuItem(
                      icon: Icons.category_rounded,
                      title: 'Kelola Kategori Marketplace',
                      badge: 'Kategori 🏷️',
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(builder: (context) => const CategoryManagementScreen()),
                        );
                      },
                    ),
                    _buildMenuDivider(),
                    _buildMenuItem(
                      icon: Icons.storefront_rounded,
                      title: 'Seller Center (Toko Jualan)',
                      badge: 'Akses Penuh 🛍️',
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(builder: (context) => const SellerDashboardScreen()),
                        );
                      },
                    ),
                    _buildMenuDivider(),
                    _buildMenuItem(
                      icon: Icons.delivery_dining_rounded,
                      title: 'Dashboard Mitra Kurir (Mode Driver)',
                      badge: 'Akses Penuh 🛵',
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(builder: (context) => const CourierHomeScreen()),
                        );
                      },
                    ),
                  ]
                  // 6. ADMIN SECTION (Khusus Role admin biasa)
                  else if (authProvider.isAuthenticated && user != null && user.role?.toLowerCase() == 'admin') ...[
                    _buildMenuDivider(),
                    _buildMenuItem(
                      icon: Icons.admin_panel_settings_rounded,
                      title: 'Panel Kontrol Admin Platform',
                      badge: 'Admin 🛡️',
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(builder: (context) => const AdminDashboardScreen()),
                        );
                      },
                    ),
                    _buildMenuDivider(),
                    _buildMenuItem(
                      icon: Icons.verified_user_rounded,
                      title: 'Persetujuan Toko Mitra',
                      badge: 'Verifikasi 🏪',
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(builder: (context) => const StoreApprovalScreen()),
                        );
                      },
                    ),
                    _buildMenuDivider(),
                    _buildMenuItem(
                      icon: Icons.inventory_2_rounded,
                      title: 'Moderasi & Validasi Produk',
                      badge: 'Katalog 📦',
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(builder: (context) => const ProductModerationScreen()),
                        );
                      },
                    ),
                    _buildMenuDivider(),
                    _buildMenuItem(
                      icon: Icons.bolt_rounded,
                      title: 'Manajemen Flash Sale & Promo',
                      badge: 'Flash Sale ⚡',
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(builder: (context) => const FlashSaleManagementScreen()),
                        );
                      },
                    ),
                    _buildMenuDivider(),
                    _buildMenuItem(
                      icon: Icons.category_rounded,
                      title: 'Kelola Kategori Marketplace',
                      badge: 'Kategori 🏷️',
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(builder: (context) => const CategoryManagementScreen()),
                        );
                      },
                    ),
                  ]
                  // 7. SELLER & COURIER & BUYER
                  else ...[
                    _buildMenuDivider(),
                    // Buka Toko / Seller Center
                    if (user?.role?.toLowerCase() == 'seller')
                      _buildMenuItem(
                        icon: Icons.storefront_rounded,
                        title: 'Seller Center (Toko Saya)',
                        badge: 'Toko Aktif 🛍️',
                        onTap: () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(builder: (context) => const SellerDashboardScreen()),
                          );
                        },
                      )
                    else
                      _buildMenuItem(
                        icon: Icons.storefront_outlined,
                        title: 'Buka Toko Jualan (Seller Center)',
                        badge: 'Gratis 🛍️',
                        onTap: () {
                          if (!authProvider.isAuthenticated) {
                            Navigator.push(context, MaterialPageRoute(builder: (context) => const LoginScreen()));
                            return;
                          }
                          Navigator.push(
                            context,
                            MaterialPageRoute(builder: (context) => const SellerRegistrationScreen()),
                          );
                        },
                      ),
                    _buildMenuDivider(),

                    // Mitra Kurir NitipDong (Mode Driver / Pendaftaran)
                    if (user?.role?.toLowerCase() == 'courier' || user?.role?.toLowerCase() == 'kurir')
                      _buildMenuItem(
                        icon: Icons.delivery_dining_rounded,
                        title: 'Dashboard Mitra Kurir (Mode Driver)',
                        badge: 'Aktif 🛵',
                        onTap: () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(builder: (context) => const CourierHomeScreen()),
                          );
                        },
                      )
                    else
                      _buildMenuItem(
                        icon: Icons.two_wheeler_rounded,
                        title: 'Gabung Jadi Mitra Kurir',
                        badge: 'Daftar 🛵',
                        onTap: () {
                          if (!authProvider.isAuthenticated) {
                            Navigator.push(context, MaterialPageRoute(builder: (context) => const LoginScreen()));
                            return;
                          }
                          Navigator.push(
                            context,
                            MaterialPageRoute(builder: (context) => const CourierRegistrationScreen()),
                          );
                        },
                      ),
                  ],

                ],
              ),
            ),
            const SizedBox(height: 12),

            // 3.1 KEAMANAN: PILIHAN SIDIK JARI & SCAN WAJAH (BIOMETRIC LOCK)
            if (authProvider.isAuthenticated && user != null) ...[
              Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: AppTheme.border),
                ),
                child: Material(
                  color: Colors.transparent,
                  child: InkWell(
                    borderRadius: BorderRadius.circular(14),
                    onTap: () => _showBiometricSecuritySheet(context, authProvider, user),
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                      child: Row(
                        children: [
                          Container(
                            width: 38,
                            height: 38,
                            decoration: BoxDecoration(
                              color: user.biometricEnabled
                                  ? (user.biometricType == 'face'
                                      ? const Color(0xFF8B5CF6).withOpacity(0.12)
                                      : const Color(0xFF06B6D4).withOpacity(0.12))
                                  : const Color(0xFFF1F5F9),
                              borderRadius: BorderRadius.circular(10),
                            ),
                            child: Icon(
                              user.biometricType == 'face'
                                  ? Icons.face_unlock_rounded
                                  : Icons.fingerprint_rounded,
                              color: user.biometricEnabled
                                  ? (user.biometricType == 'face' ? const Color(0xFF7C3AED) : const Color(0xFF0891B2))
                                  : const Color(0xFF64748B),
                              size: 22,
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    const Text(
                                      'Kunci Aplikasi (Biometrik)',
                                      style: TextStyle(
                                        fontSize: 12.5,
                                        fontWeight: FontWeight.w700,
                                        color: Color(0xFF0F172A),
                                      ),
                                    ),
                                    const SizedBox(width: 6),
                                    if (user.biometricEnabled)
                                      Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 1.5),
                                        decoration: BoxDecoration(
                                          color: user.biometricType == 'face'
                                              ? const Color(0xFF8B5CF6).withOpacity(0.15)
                                              : const Color(0xFF06B6D4).withOpacity(0.15),
                                          borderRadius: BorderRadius.circular(6),
                                        ),
                                        child: Text(
                                          user.biometricType == 'face' ? 'Face Unlock' : 'Fingerprint',
                                          style: TextStyle(
                                            fontSize: 9.5,
                                            fontWeight: FontWeight.w800,
                                            color: user.biometricType == 'face' ? const Color(0xFF7C3AED) : const Color(0xFF0891B2),
                                          ),
                                        ),
                                      ),
                                  ],
                                ),
                                const SizedBox(height: 2),
                                Text(
                                  user.biometricEnabled
                                      ? (user.biometricType == 'face'
                                          ? 'Aktif • Pindai Wajah sebelum masuk'
                                          : 'Aktif • Sidik jari sebelum masuk')
                                      : 'Nonaktif • Ketuk untuk memilih Sidik Jari / Scan Wajah',
                                  style: TextStyle(
                                    fontSize: 10.5,
                                    color: user.biometricEnabled ? const Color(0xFF059669) : const Color(0xFF64748B),
                                    fontWeight: user.biometricEnabled ? FontWeight.w600 : FontWeight.w400,
                                  ),
                                ),
                              ],
                            ),
                          ),
                          const Icon(Icons.chevron_right_rounded, size: 20, color: Color(0xFF94A3B8)),
                        ],
                      ),
                    ),
                  ),
                ),
              ),
              const SizedBox(height: 12),
            ],

            // 4. APP VERSION DISPLAY & CHECK
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: AppTheme.border),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: [
                      const Icon(Icons.info_outline_rounded, size: 18, color: AppTheme.textMuted),
                      const SizedBox(width: 8),
                      Text(
                        'NitipDong App v${ApiService.currentAppVersion} (Terbaru)',
                        style: const TextStyle(fontSize: 11.5, color: AppTheme.textSecondary, fontWeight: FontWeight.w600),
                      ),
                    ],
                  ),
                  TextButton(
                    onPressed: () => _checkAppUpdate(context),
                    child: const Text('Cek Update', style: TextStyle(fontSize: 11.5, fontWeight: FontWeight.w700)),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 12),

            // 5. LOGOUT BUTTON
            if (authProvider.isAuthenticated) ...[
              SizedBox(
                width: double.infinity,
                child: OutlinedButton.icon(
                  icon: const Icon(Icons.logout_rounded, size: 16, color: Colors.red),
                  label: const Text('Keluar dari Akun', style: TextStyle(color: Colors.red, fontSize: 12.5, fontWeight: FontWeight.w700)),
                  style: OutlinedButton.styleFrom(
                    side: BorderSide(color: Colors.red.shade200),
                    padding: const EdgeInsets.symmetric(vertical: 12),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  onPressed: () async {
                    await authProvider.logout();
                    if (mounted) {
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(content: Text('Anda telah berhasil keluar.'), behavior: SnackBarBehavior.floating),
                      );
                    }
                  },
                ),
              ),
              const SizedBox(height: 20),
            ],
          ],
        ),
      ),
    );
  }

  Widget _buildStatItem(String label, String value, IconData icon, {required VoidCallback onTap}) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(10),
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
        child: Column(
          children: [
            Icon(icon, color: AppTheme.primary, size: 20),
            const SizedBox(height: 4),
            Text(value, style: const TextStyle(fontSize: 13.5, fontWeight: FontWeight.w900)),
            Text(label, style: const TextStyle(fontSize: 10, color: AppTheme.textMuted)),
          ],
        ),
      ),
    );
  }

  Widget _buildStatDivider() {
    return Container(width: 1, height: 26, color: AppTheme.border);
  }

  Widget _buildMenuItem({
    required IconData icon,
    required String title,
    required String? badge,
    required VoidCallback onTap,
  }) {
    return ListTile(
      leading: Icon(icon, color: AppTheme.textSecondary, size: 20),
      title: Text(title, style: const TextStyle(fontSize: 12.5, fontWeight: FontWeight.w600)),
      trailing: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          if (badge != null)
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
              decoration: BoxDecoration(
                color: AppTheme.primaryLight,
                borderRadius: BorderRadius.circular(6),
              ),
              child: Text(
                badge,
                style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w700, color: AppTheme.primaryDark),
              ),
            ),
          const SizedBox(width: 4),
          const Icon(Icons.chevron_right, color: AppTheme.textMuted, size: 18),
        ],
      ),
      onTap: onTap,
    );
  }

  Widget _buildMenuDivider() {
    return const Divider(height: 1, indent: 16, endIndent: 16, color: AppTheme.border);
  }

  // ══════════════════════════════════════════════════
  // 1. VOUCHER & KUPON SAYA SHEET (REALIZED WITH EXPIRY)
  // ══════════════════════════════════════════════════
  void _showVouchersSheet(BuildContext context) {
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
                const Text('Voucher & Kupon Promo Aktif 🎟️', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
                IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(ctx)),
              ],
            ),
            const SizedBox(height: 10),

            _availableVouchers.isEmpty
                ? const Padding(
                    padding: EdgeInsets.all(20),
                    child: Center(child: CircularProgressIndicator()),
                  )
                : ListView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    itemCount: _availableVouchers.length,
                    itemBuilder: (context, vIdx) {
                      final v = _availableVouchers[vIdx];
                      return Container(
                        margin: const EdgeInsets.only(bottom: 10),
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: AppTheme.primaryLight.withOpacity(0.5),
                          border: Border.all(color: AppTheme.primary.withOpacity(0.3)),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Row(
                          children: [
                            Container(
                              padding: const EdgeInsets.all(10),
                              decoration: BoxDecoration(color: AppTheme.primary, borderRadius: BorderRadius.circular(10)),
                              child: const Icon(Icons.confirmation_number_outlined, color: Colors.white, size: 20),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(v['name'] ?? v['code'] ?? 'Kupon Promo', style: const TextStyle(fontSize: 12.5, fontWeight: FontWeight.w800)),
                                  const SizedBox(height: 2),
                                  Text(v['description'] ?? '', style: const TextStyle(fontSize: 10, color: AppTheme.textSecondary)),
                                  const SizedBox(height: 4),
                                  Text(
                                    '⏳ Berlaku s/d: ${v['expires_at'] ?? 'Berlaku Selamanya'}',
                                    style: const TextStyle(fontSize: 9.5, color: AppTheme.accentOrange, fontWeight: FontWeight.w700),
                                  ),
                                ],
                              ),
                            ),
                            OutlinedButton(
                              style: OutlinedButton.styleFrom(
                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                                minimumSize: Size.zero,
                                tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                              ),
                              onPressed: () {
                                Clipboard.setData(ClipboardData(text: v['code'] ?? 'NITIPHEMAT20'));
                                Navigator.pop(ctx);
                                ScaffoldMessenger.of(context).showSnackBar(
                                  SnackBar(
                                    content: Text('Kode kupon "${v['code']}" berhasil disalin! 📋'),
                                    backgroundColor: AppTheme.primaryDark,
                                    behavior: SnackBarBehavior.floating,
                                  ),
                                );
                              },
                              child: const Text('Salin', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700)),
                            ),
                          ],
                        ),
                      );
                    },
                  ),
          ],
        ),
      ),
    );
  }

  // ══════════════════════════════════════════════════
  // 3. DAFTAR ALAMAT SHEET
  // ══════════════════════════════════════════════════
  void _showAddressSheet(BuildContext context) {
    final addressController = TextEditingController(text: _currentAddress);
    final nameController = TextEditingController(text: _recipientName);
    final phoneController = TextEditingController(text: _recipientPhone);

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(15))),
      builder: (ctx) => Padding(
        padding: EdgeInsets.fromLTRB(20, 16, 20, MediaQuery.of(ctx).viewInsets.bottom + 30),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(child: Container(width: 36, height: 4, decoration: BoxDecoration(color: Colors.grey.shade300, borderRadius: BorderRadius.circular(2)))),
            const SizedBox(height: 16),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text('Alamat Pengiriman Utama 📍', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
                IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(ctx)),
              ],
            ),
            const SizedBox(height: 12),
            TextField(
              controller: nameController,
              decoration: const InputDecoration(labelText: 'Nama Penerima', contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 10)),
              style: const TextStyle(fontSize: 12),
            ),
            const SizedBox(height: 10),
            TextField(
              controller: phoneController,
              decoration: const InputDecoration(labelText: 'Nomor WhatsApp / HP', contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 10)),
              style: const TextStyle(fontSize: 12),
            ),
            const SizedBox(height: 10),
            TextField(
              controller: addressController,
              maxLines: 2,
              decoration: const InputDecoration(labelText: 'Alamat Lengkap (Jalan, RT/RW, Kota)', contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 10)),
              style: const TextStyle(fontSize: 12),
            ),
            const SizedBox(height: 20),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () async {
                  await ApiService.saveAddressLocally(
                    fullAddress: addressController.text.trim(),
                    recipientName: nameController.text.trim(),
                    phone: phoneController.text.trim(),
                  );
                  setState(() {
                    _currentAddress = addressController.text.trim();
                    _recipientName = nameController.text.trim();
                    _recipientPhone = phoneController.text.trim();
                  });
                  Navigator.pop(ctx);
                  if (mounted) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(content: Text('Alamat pengiriman berhasil diperbarui! 📍'), backgroundColor: AppTheme.success, behavior: SnackBarBehavior.floating),
                    );
                  }
                },
                child: const Text('Simpan Alamat'),
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _showSellerCenterDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
        title: const Text('Buka Toko Jualan 🏪', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
        content: const Text(
          'Daftarkan toko jastip & produk Anda di NitipDong! Dapatkan akses ke ribuan pembeli, manajemen pesanan, dan penarikan dana langsung.',
          style: TextStyle(fontSize: 12.5, color: AppTheme.textSecondary),
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Tutup')),
          ElevatedButton(
            onPressed: () async {
              Navigator.pop(ctx);
              final uri = Uri.parse('https://budayakita.com/customer/store/register');
              if (await canLaunchUrl(uri)) {
                await launchUrl(uri, mode: LaunchMode.externalApplication);
              }
            },
            child: const Text('Buka Seller Center'),
          ),
        ],
      ),
    );
  }

  void _showWishlistModal(BuildContext context) {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(15))),
      builder: (ctx) => Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Icon(Icons.favorite_rounded, color: AppTheme.accentOrange, size: 48),
            const SizedBox(height: 12),
            const Text('Wishlist Favorit Saya', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
            const SizedBox(height: 6),
            const Text('Produk yang Anda sukai akan tersimpan rapi di sini.', textAlign: TextAlign.center, style: TextStyle(fontSize: 12, color: AppTheme.textMuted)),
            const SizedBox(height: 20),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(onPressed: () => Navigator.pop(ctx), child: const Text('Tutup')),
            ),
          ],
        ),
      ),
    );
  }

  Future<void> _checkAppUpdate(BuildContext context) async {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (ctx) => const Center(child: CircularProgressIndicator(color: AppTheme.primary)),
    );

    final status = await ApiService.checkSystemStatus();
    if (!mounted) return;
    Navigator.pop(context);

    final latestVer = status['latest_version']?.toString() ?? ApiService.currentAppVersion;
    final currentVer = ApiService.currentAppVersion;
    final hasUpdate = latestVer.isNotEmpty && latestVer != currentVer;
    final updateUrl = status['update_url']?.toString() ?? 'https://budayakita.com/download/app';

    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (ctx) => Padding(
        padding: const EdgeInsets.fromLTRB(24, 20, 24, 30),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(width: 40, height: 4, decoration: BoxDecoration(color: Colors.grey.shade300, borderRadius: BorderRadius.circular(2))),
            const SizedBox(height: 16),
            Container(
              width: 56,
              height: 56,
              decoration: BoxDecoration(
                color: hasUpdate ? AppTheme.primaryLight : Colors.green.shade50,
                borderRadius: BorderRadius.circular(16),
              ),
              child: Icon(
                hasUpdate ? Icons.system_update_rounded : Icons.check_circle_rounded,
                color: hasUpdate ? AppTheme.primary : Colors.green,
                size: 28,
              ),
            ),
            const SizedBox(height: 12),
            Text(
              hasUpdate ? 'Pembaruan Tersedia (v$latestVer) 🚀' : 'Versi Aplikasi Terbaru (v$currentVer) ✅',
              style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w800),
            ),
            const SizedBox(height: 8),
            Text(
              hasUpdate
                  ? 'Versi v$latestVer telah siap dipasang. Termasuk pembaruan performa sistem, pembersihan menu, dan peningkatan stabilitas pembayaran.'
                  : 'Aplikasi NitipDong Anda sudah menggunakan versi paling mutakhir (v$currentVer). Semua fitur berjalan lancar dan optimal.',
              textAlign: TextAlign.center,
              style: const TextStyle(fontSize: 12, color: AppTheme.textSecondary, height: 1.4),
            ),
            const SizedBox(height: 20),
            if (hasUpdate) ...[
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  icon: const Icon(Icons.download_rounded, size: 18),
                  label: const Text('Pasang Pembaruan Sekarang', style: TextStyle(fontWeight: FontWeight.w800)),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.primary,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  onPressed: () {
                    Navigator.pop(ctx);
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => AppUpdateProgressScreen(
                          newVersion: latestVer,
                          downloadUrl: updateUrl,
                        ),
                      ),
                    );
                  },
                ),
              ),
              const SizedBox(height: 10),
              SizedBox(
                width: double.infinity,
                child: TextButton(
                  onPressed: () => Navigator.pop(ctx),
                  child: const Text('Nanti Saja', style: TextStyle(color: AppTheme.textMuted)),
                ),
              ),
            ] else ...[
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: () => Navigator.pop(ctx),
                  style: ElevatedButton.styleFrom(
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    padding: const EdgeInsets.symmetric(vertical: 14),
                  ),
                  child: const Text('Tutup'),
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }

  void _showBiometricSecuritySheet(BuildContext context, AuthProvider authProvider, dynamic user) {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (sheetCtx) => StatefulBuilder(
        builder: (ctx, setModalState) => Padding(
          padding: const EdgeInsets.fromLTRB(20, 16, 20, 28),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Center(
                child: Container(
                  width: 40,
                  height: 4,
                  decoration: BoxDecoration(color: Colors.grey.shade300, borderRadius: BorderRadius.circular(2)),
                ),
              ),
              const SizedBox(height: 16),
              Row(
                children: [
                  Container(
                    width: 42,
                    height: 42,
                    decoration: BoxDecoration(
                      color: const Color(0xFF06B6D4).withOpacity(0.12),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const Icon(Icons.security_rounded, color: Color(0xFF0891B2), size: 24),
                  ),
                  const SizedBox(width: 12),
                  const Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Pilih Metode Kunci Aplikasi',
                          style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800, color: Color(0xFF0F172A)),
                        ),
                        SizedBox(height: 2),
                        Text(
                          'Verifikasi identitas sebelum membuka NitipDong',
                          style: TextStyle(fontSize: 11.5, color: Color(0xFF64748B)),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 20),

              // Option 1: Sidik Jari (Fingerprint)
              _buildBiometricOption(
                title: 'Sidik Jari (Fingerprint)',
                subtitle: 'Sentuh sensor sidik jari HP untuk membuka',
                icon: Icons.fingerprint_rounded,
                iconColor: const Color(0xFF0891B2),
                isSelected: user.biometricEnabled && user.biometricType == 'fingerprint',
                onTap: () async {
                  Navigator.pop(sheetCtx);
                  await _handleToggleBiometric(context, authProvider, true, type: 'fingerprint');
                },
              ),

              const SizedBox(height: 10),

              // Option 2: Scan Wajah (Face Unlock)
              _buildBiometricOption(
                title: 'Pindai Wajah (Face Unlock)',
                subtitle: 'Gunakan kamera depan untuk pengenalan wajah',
                icon: Icons.face_unlock_rounded,
                iconColor: const Color(0xFF7C3AED),
                isSelected: user.biometricEnabled && user.biometricType == 'face',
                onTap: () async {
                  Navigator.pop(sheetCtx);
                  await _handleToggleBiometric(context, authProvider, true, type: 'face');
                },
              ),

              const SizedBox(height: 10),

              // Option 3: Nonaktifkan Kunci
              _buildBiometricOption(
                title: 'Nonaktifkan Kunci Biometrik',
                subtitle: 'Aplikasi langsung terbuka tanpa meminta sidik jari / wajah',
                icon: Icons.lock_open_rounded,
                iconColor: const Color(0xFF64748B),
                isSelected: !user.biometricEnabled,
                onTap: () async {
                  Navigator.pop(sheetCtx);
                  await _handleToggleBiometric(context, authProvider, false);
                },
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildBiometricOption({
    required String title,
    required String subtitle,
    required IconData icon,
    required Color iconColor,
    required bool isSelected,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(14),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
        decoration: BoxDecoration(
          color: isSelected ? iconColor.withOpacity(0.06) : const Color(0xFFF8FAFC),
          borderRadius: BorderRadius.circular(14),
          border: Border.all(
            color: isSelected ? iconColor : const Color(0xFFE2E8F0),
            width: isSelected ? 1.5 : 1,
          ),
        ),
        child: Row(
          children: [
            Container(
              width: 36,
              height: 36,
              decoration: BoxDecoration(
                color: iconColor.withOpacity(0.12),
                borderRadius: BorderRadius.circular(10),
              ),
              child: Icon(icon, color: iconColor, size: 20),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: TextStyle(
                      fontSize: 13,
                      fontWeight: isSelected ? FontWeight.w800 : FontWeight.w700,
                      color: const Color(0xFF0F172A),
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    subtitle,
                    style: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                  ),
                ],
              ),
            ),
            if (isSelected)
              Icon(Icons.check_circle_rounded, color: iconColor, size: 22)
            else
              const Icon(Icons.radio_button_unchecked_rounded, color: Color(0xFFCBD5E1), size: 22),
          ],
        ),
      ),
    );
  }

  Future<void> _handleToggleBiometric(
    BuildContext context,
    AuthProvider authProvider,
    bool newVal, {
    String type = 'fingerprint',
  }) async {
    final localAuth = LocalAuthentication();

    if (newVal) {
      // 1. Verifikasi ketersediaan hardware biometrik pada perangkat
      try {
        final bool canCheck = await localAuth.canCheckBiometrics;
        final bool isSupported = await localAuth.isDeviceSupported();

        if (!canCheck && !isSupported) {
          if (!mounted) return;
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Perangkat Anda belum mendukung sensor sidik jari atau Face ID.'),
              backgroundColor: Colors.orange,
              behavior: SnackBarBehavior.floating,
            ),
          );
          return;
        }

        final String reason = type == 'face'
            ? 'Pindai wajah Anda untuk mengonfirmasi pengaktifan Face Unlock'
            : 'Pindai sidik jari Anda untuk mengonfirmasi pengaktifan Fingerprint';

        // 2. Minta verifikasi biometrik terlebih dahulu sebelum mengaktifkan
        final bool authenticated = await localAuth.authenticate(
          localizedReason: reason,
          options: const AuthenticationOptions(
            biometricOnly: false,
            stickyAuth: true,
          ),
        );

        if (!authenticated) {
          if (!mounted) return;
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Verifikasi biometrik dibatalkan. Kunci belum diaktifkan.'),
              behavior: SnackBarBehavior.floating,
            ),
          );
          return;
        }
      } catch (e) {
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Kendala sensor biometrik: $e'),
            backgroundColor: Colors.red,
            behavior: SnackBarBehavior.floating,
          ),
        );
        return;
      }
    }

    // 3. Simpan perubahan ke database akun dan cache lokal
    final success = await authProvider.updateBiometric(newVal, type: type);
    if (!mounted) return;

    if (success) {
      final label = type == 'face' ? 'Pindai Wajah (Face Unlock)' : 'Sidik Jari (Fingerprint)';
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            newVal ? 'Kunci $label Berhasil Diaktifkan! 🔐' : 'Kunci Biometrik Telah Dinonaktifkan.',
          ),
          backgroundColor: newVal ? const Color(0xFF059669) : const Color(0xFF475569),
          behavior: SnackBarBehavior.floating,
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Gagal memperbarui pengaturan biometrik ke server. Silakan coba lagi.'),
          backgroundColor: Colors.red,
          behavior: SnackBarBehavior.floating,
        ),
      );
    }
  }
}
