import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../providers/auth_provider.dart';
import '../auth/login_screen.dart';

class ProfileScreen extends StatelessWidget {
  const ProfileScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);
    final user = authProvider.user;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Akun Saya'),
        actions: [
          IconButton(
            icon: const Icon(Icons.settings_outlined),
            onPressed: () {},
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            // User Header Card
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: AppTheme.border),
              ),
              child: authProvider.isAuthenticated && user != null
                  ? Row(
                      children: [
                        CircleAvatar(
                          radius: 28,
                          backgroundColor: AppTheme.primaryLight,
                          backgroundImage: user.avatarUrl != null ? NetworkImage(user.avatarUrl!) : null,
                          child: user.avatarUrl == null
                              ? Text(
                                  user.name.isNotEmpty ? user.name[0].toUpperCase() : 'U',
                                  style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w800, color: AppTheme.primaryDark),
                                )
                              : null,
                        ),
                        const SizedBox(width: 14),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(user.name, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w800)),
                              const SizedBox(height: 2),
                              Text(user.email, style: const TextStyle(fontSize: 12, color: AppTheme.textMuted)),
                              const SizedBox(height: 6),
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                decoration: BoxDecoration(
                                  color: AppTheme.primaryLight,
                                  borderRadius: BorderRadius.circular(4),
                                ),
                                child: Text(
                                  user.role.toUpperCase(),
                                  style: const TextStyle(fontSize: 9, fontWeight: FontWeight.w800, color: AppTheme.primaryDark),
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
                          radius: 26,
                          backgroundColor: AppTheme.primaryLight,
                          child: Icon(Icons.person_outline, color: AppTheme.primary, size: 28),
                        ),
                        const SizedBox(width: 14),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text('Belum Masuk Akun', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w800)),
                              const SizedBox(height: 2),
                              const Text('Masuk untuk pengalaman belanja lebih hemat', style: TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                            ],
                          ),
                        ),
                        ElevatedButton(
                          onPressed: () {
                            Navigator.push(context, MaterialPageRoute(builder: (context) => const LoginScreen()));
                          },
                          style: ElevatedButton.styleFrom(
                            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                          ),
                          child: const Text('Masuk', style: TextStyle(fontSize: 12)),
                        ),
                      ],
                    ),
            ),
            const SizedBox(height: 16),

            // Quick Stats Card
            if (authProvider.isAuthenticated && user != null) ...[
              Container(
                padding: const EdgeInsets.symmetric(vertical: 14),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: AppTheme.border),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceAround,
                  children: [
                    _buildStatItem('Pesanan', user.ordersCount.toString(), Icons.receipt_long_outlined),
                    _buildStatDivider(),
                    _buildStatItem('Wishlist', user.wishlistCount.toString(), Icons.favorite_border_rounded),
                    _buildStatDivider(),
                    _buildStatItem('Keranjang', user.cartCount.toString(), Icons.shopping_cart_outlined),
                  ],
                ),
              ),
              const SizedBox(height: 16),
            ],

            // Menu Options
            Container(
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: AppTheme.border),
              ),
              child: Column(
                children: [
                  _buildMenuItem(Icons.account_balance_wallet_outlined, 'Dompet NitipPay & Saldo', 'Rp 0'),
                  _buildMenuDivider(),
                  _buildMenuItem(Icons.confirmation_number_outlined, 'Voucher & Kupon Saya', '3 Kupon'),
                  _buildMenuDivider(),
                  _buildMenuItem(Icons.location_on_outlined, 'Daftar Alamat Pengiriman', null),
                  _buildMenuDivider(),
                  _buildMenuItem(Icons.storefront_outlined, 'Buka Toko Gratis di NitipDong', 'Mulai Jual'),
                  _buildMenuDivider(),
                  _buildMenuItem(Icons.headset_mic_outlined, 'Pusat Bantuan & CS 24/7', null),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // Logout Button
            if (authProvider.isAuthenticated)
              SizedBox(
                width: double.infinity,
                child: OutlinedButton.icon(
                  icon: const Icon(Icons.logout, color: Colors.red, size: 18),
                  label: const Text('Keluar dari Akun', style: TextStyle(color: Colors.red, fontWeight: FontWeight.w700)),
                  style: OutlinedButton.styleFrom(
                    side: const BorderSide(color: Colors.red),
                    padding: const EdgeInsets.symmetric(vertical: 12),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  onPressed: () async {
                    await authProvider.logout();
                  },
                ),
              ),
            const SizedBox(height: 40),
          ],
        ),
      ),
    );
  }

  Widget _buildStatItem(String label, String value, IconData icon) {
    return Column(
      children: [
        Icon(icon, color: AppTheme.primary, size: 20),
        const SizedBox(height: 4),
        Text(value, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w900)),
        Text(label, style: const TextStyle(fontSize: 10, color: AppTheme.textMuted)),
      ],
    );
  }

  Widget _buildStatDivider() {
    return Container(width: 1, height: 30, color: AppTheme.border);
  }

  Widget _buildMenuItem(IconData icon, String title, String? badge) {
    return ListTile(
      leading: Icon(icon, color: AppTheme.textSecondary, size: 20),
      title: Text(title, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600)),
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
      onTap: () {},
    );
  }

  Widget _buildMenuDivider() {
    return const Divider(height: 1, indent: 16, endIndent: 16, color: AppTheme.border);
  }
}
