import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../providers/auth_provider.dart';
import '../../providers/cart_provider.dart';
import '../auth/login_screen.dart';
import '../cart/cart_screen.dart';
import '../orders/orders_screen.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({Key? key}) : super(key: key);

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  // Local state for address and NitipPay
  String _currentAddress = 'Jl. Raya Darmo No. 42, Wonokromo, Surabaya, Jawa Timur 60241';
  String _recipientName = 'Mohammad Kevin Arif Rudianto';
  String _recipientPhone = '081234567890';
  int _nitipPayBalance = 0;

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
            // ══════════════════════════════════════════════════
            // 1. USER HEADER CARD
            // ══════════════════════════════════════════════════
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: AppTheme.border),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.02),
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: authProvider.isAuthenticated && user != null
                  ? InkWell(
                      onTap: () => _showEditProfileDialog(context, user.name, user.email),
                      borderRadius: BorderRadius.circular(12),
                      child: Row(
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
                                Row(
                                  children: [
                                    Expanded(
                                      child: Text(
                                        user.name,
                                        style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w800),
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    ),
                                    const Icon(Icons.edit_outlined, size: 16, color: AppTheme.textMuted),
                                  ],
                                ),
                                const SizedBox(height: 2),
                                Text(user.email, style: const TextStyle(fontSize: 12, color: AppTheme.textMuted)),
                                const SizedBox(height: 6),
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                                  decoration: BoxDecoration(
                                    color: AppTheme.primaryLight,
                                    borderRadius: BorderRadius.circular(6),
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
                      ),
                    )
                  : Row(
                      children: [
                        const CircleAvatar(
                          radius: 26,
                          backgroundColor: AppTheme.primaryLight,
                          child: Icon(Icons.person_outline, color: AppTheme.primary, size: 28),
                        ),
                        const SizedBox(width: 14),
                        const Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('Belum Masuk Akun', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w800)),
                              SizedBox(height: 2),
                              Text('Masuk untuk pengalaman belanja lebih hemat', style: TextStyle(fontSize: 11, color: AppTheme.textMuted)),
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

            // ══════════════════════════════════════════════════
            // 2. QUICK STATS CARD (PESANAN, WISHLIST, KERANJANG)
            // ══════════════════════════════════════════════════
            if (authProvider.isAuthenticated && user != null) ...[
              Consumer<CartProvider>(
                builder: (context, cartProvider, child) {
                  return Container(
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(16),
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
              const SizedBox(height: 16),
            ],

            // ══════════════════════════════════════════════════
            // 3. MAIN MENU LIST (REALIZED FEATURES)
            // ══════════════════════════════════════════════════
            Container(
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: AppTheme.border),
              ),
              child: Column(
                children: [
                  // 1. Dompet NitipPay & Saldo
                  _buildMenuItem(
                    icon: Icons.account_balance_wallet_outlined,
                    title: 'Dompet NitipPay & Saldo',
                    badge: _nitipPayBalance > 0 ? 'Rp $_nitipPayBalance' : 'Rp 0',
                    onTap: () => _showNitipPaySheet(context),
                  ),
                  _buildMenuDivider(),

                  // 2. Voucher & Kupon Saya
                  _buildMenuItem(
                    icon: Icons.confirmation_number_outlined,
                    title: 'Voucher & Kupon Saya',
                    badge: '3 Kupon',
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

                  // 4. Buka Toko Gratis di NitipDong
                  _buildMenuItem(
                    icon: Icons.storefront_outlined,
                    title: 'Buka Toko Gratis di NitipDong',
                    badge: 'Mulai Jual',
                    onTap: () => _showSellerRegistrationSheet(context),
                  ),
                  _buildMenuDivider(),

                  // 5. Pusat Bantuan & CS 24/7
                  _buildMenuItem(
                    icon: Icons.headset_mic_outlined,
                    title: 'Pusat Bantuan & CS 24/7',
                    badge: null,
                    onTap: () => _showHelpCenterSheet(context),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // ══════════════════════════════════════════════════
            // 4. LOGOUT BUTTON
            // ══════════════════════════════════════════════════
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
                  onPressed: () => _showLogoutConfirmDialog(context, authProvider),
                ),
              ),
            
            // ══════════════════════════════════════════════════
            // 5. APP VERSION DISPLAY
            // ══════════════════════════════════════════════════
            const SizedBox(height: 24),
            Center(
              child: Column(
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: AppTheme.primaryLight,
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: AppTheme.border),
                    ),
                    child: const Text(
                      'NitipDong App v1.0.1 (Terbaru)',
                      style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.w700,
                        color: AppTheme.primaryDark,
                      ),
                    ),
                  ),
                  const SizedBox(height: 4),
                  const Text(
                    'Marketplace & Jastip Terpercaya Indonesia',
                    style: TextStyle(fontSize: 10, color: AppTheme.textMuted),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 30),
          ],
        ),
      ),
    );
  }

  // ══════════════════════════════════════════════════
  // HELPER WIDGETS
  // ══════════════════════════════════════════════════
  Widget _buildStatItem(String label, String value, IconData icon, {required VoidCallback onTap}) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(10),
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
        child: Column(
          children: [
            Icon(icon, color: AppTheme.primary, size: 20),
            const SizedBox(height: 4),
            Text(value, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w900)),
            Text(label, style: const TextStyle(fontSize: 10, color: AppTheme.textMuted)),
          ],
        ),
      ),
    );
  }

  Widget _buildStatDivider() {
    return Container(width: 1, height: 30, color: AppTheme.border);
  }

  Widget _buildMenuItem({
    required IconData icon,
    required String title,
    required String? badge,
    required VoidCallback onTap,
  }) {
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
      onTap: onTap,
    );
  }

  Widget _buildMenuDivider() {
    return const Divider(height: 1, indent: 16, endIndent: 16, color: AppTheme.border);
  }

  // ══════════════════════════════════════════════════
  // 1. NITIPPAY & SALDO MODAL SHEET
  // ══════════════════════════════════════════════════
  void _showNitipPaySheet(BuildContext context) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setSheetState) => Padding(
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
                  const Text('Dompet NitipPay & Saldo', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
                  IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(ctx)),
                ],
              ),
              const SizedBox(height: 12),
              Container(
                padding: const EdgeInsets.all(18),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(colors: [AppTheme.primaryDark, AppTheme.primary]),
                  borderRadius: BorderRadius.circular(16),
                  boxShadow: [
                    BoxShadow(color: AppTheme.primary.withOpacity(0.3), blurRadius: 12, offset: const Offset(0, 4)),
                  ],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text('Total Saldo Tersedia', style: TextStyle(color: Colors.white70, fontSize: 11, fontWeight: FontWeight.w600)),
                        Icon(Icons.verified_user_rounded, color: Colors.white70, size: 16),
                      ],
                    ),
                    const SizedBox(height: 6),
                    Text(
                      'Rp ${_nitipPayBalance.toString().replaceAllMapped(RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'), (m) => '${m[1]}.')}',
                      style: const TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.w900),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 20),
              const Text('Isi Ulang Saldo Instan', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w800)),
              const SizedBox(height: 10),
              Wrap(
                spacing: 8,
                runSpacing: 8,
                children: [50000, 100000, 250000, 500000].map((nominal) {
                  return ActionChip(
                    backgroundColor: AppTheme.primaryLight,
                    side: const BorderSide(color: AppTheme.border),
                    label: Text('+ Rp ${nominal ~/ 1000}rb', style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppTheme.primaryDark)),
                    onPressed: () {
                      setState(() => _nitipPayBalance += nominal);
                      setSheetState(() {});
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(
                          content: Text('Berhasil top up Saldo Rp ${nominal ~/ 1000}rb ke NitipPay! 🎉'),
                          backgroundColor: Colors.green,
                          behavior: SnackBarBehavior.floating,
                        ),
                      );
                    },
                  );
                }).toList(),
              ),
              const SizedBox(height: 24),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  icon: const Icon(Icons.account_balance_rounded, size: 16),
                  label: const Text('Tarik Saldo ke Rekening Bank'),
                  onPressed: () {
                    Navigator.pop(ctx);
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(
                        content: Text('Fitur penarikan saldo tersedia untuk akun terverifikasi.'),
                        behavior: SnackBarBehavior.floating,
                      ),
                    );
                  },
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  // ══════════════════════════════════════════════════
  // 2. VOUCHER & KUPON SAYA SHEET
  // ══════════════════════════════════════════════════
  void _showVouchersSheet(BuildContext context) {
    final vouchers = [
      {'code': 'ONGKIRNOL', 'title': 'Gratis Ongkir Rp0', 'desc': 'Min. belanja Rp 0 berlaku di semua toko mitra', 'discount': 'Gratis Ongkir'},
      {'code': 'NITIPHEMAT', 'title': 'Diskon 15% Spesial', 'desc': 'Potongan 15% maks. Rp 50.000 untuk pengguna baru', 'discount': 'Diskon 15%'},
      {'code': 'FLASHSALE20', 'title': 'Cashback 20% Gadget', 'desc': 'Kategori Elektronik & Laptop Official Store', 'discount': 'Cashback 20%'},
    ];

    showModalBottomSheet(
      context: context,
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
            const Text('Voucher & Kupon Promo Aktif 🎟️', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
            const SizedBox(height: 14),
            ...vouchers.map((v) {
              return Container(
                margin: const EdgeInsets.only(bottom: 10),
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: AppTheme.primaryLight.withOpacity(0.5),
                  border: Border.all(color: AppTheme.border),
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
                          Text(v['title']!, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w800)),
                          const SizedBox(height: 2),
                          Text(v['desc']!, style: const TextStyle(fontSize: 10, color: AppTheme.textMuted)),
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
                        Clipboard.setData(ClipboardData(text: v['code']!));
                        Navigator.pop(ctx);
                        ScaffoldMessenger.of(context).showSnackBar(
                          SnackBar(
                            content: Text('Kode kupon ${v['code']} berhasil disalin! 📋'),
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
            }).toList(),
          ],
        ),
      ),
    );
  }

  // ══════════════════════════════════════════════════
  // 3. DAFTAR ALAMAT PENGIRIMAN SHEET
  // ══════════════════════════════════════════════════
  void _showAddressSheet(BuildContext context) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setSheetState) => Padding(
          padding: EdgeInsets.fromLTRB(20, 16, 20, MediaQuery.of(ctx).viewInsets.bottom + 30),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: Colors.grey.shade300, borderRadius: BorderRadius.circular(2)))),
              const SizedBox(height: 16),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text('Alamat Pengiriman Utama 📍', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
                  IconButton(icon: const Icon(Icons.close), onPressed: () => Navigator.pop(ctx)),
                ],
              ),
              const SizedBox(height: 12),
              Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: Colors.white,
                  border: Border.all(color: AppTheme.primary, width: 1.5),
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Text(_recipientName, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w800)),
                        const SizedBox(width: 8),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                          decoration: BoxDecoration(color: AppTheme.primaryLight, borderRadius: BorderRadius.circular(4)),
                          child: const Text('Utama', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w800, color: AppTheme.primaryDark)),
                        ),
                      ],
                    ),
                    const SizedBox(height: 4),
                    Text(_recipientPhone, style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary)),
                    const SizedBox(height: 6),
                    Text(_currentAddress, style: const TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                  ],
                ),
              ),
              const SizedBox(height: 16),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  icon: const Icon(Icons.edit_location_alt_outlined, size: 16),
                  label: const Text('Ubah Alamat Pengiriman'),
                  onPressed: () {
                    Navigator.pop(ctx);
                    _showEditAddressDialog(context);
                  },
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _showEditAddressDialog(BuildContext context) {
    final nameCtrl = TextEditingController(text: _recipientName);
    final phoneCtrl = TextEditingController(text: _recipientPhone);
    final addressCtrl = TextEditingController(text: _currentAddress);

    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Ubah Alamat Pengiriman', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800)),
        content: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              TextField(controller: nameCtrl, decoration: const InputDecoration(labelText: 'Nama Penerima', contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 10))),
              const SizedBox(height: 10),
              TextField(controller: phoneCtrl, keyboardType: TextInputType.phone, decoration: const InputDecoration(labelText: 'Nomor WhatsApp / HP', contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 10))),
              const SizedBox(height: 10),
              TextField(controller: addressCtrl, maxLines: 3, decoration: const InputDecoration(labelText: 'Alamat Lengkap & Kota', contentPadding: EdgeInsets.all(12))),
            ],
          ),
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Batal')),
          ElevatedButton(
            onPressed: () {
              if (nameCtrl.text.isNotEmpty && addressCtrl.text.isNotEmpty) {
                setState(() {
                  _recipientName = nameCtrl.text;
                  _recipientPhone = phoneCtrl.text;
                  _currentAddress = addressCtrl.text;
                });
                Navigator.pop(ctx);
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text('Alamat pengiriman berhasil diperbarui! ✅'), backgroundColor: Colors.green, behavior: SnackBarBehavior.floating),
                );
              }
            },
            child: const Text('Simpan'),
          ),
        ],
      ),
    );
  }

  // ══════════════════════════════════════════════════
  // 4. BUKA TOKO GRATIS MODAL SHEET
  // ══════════════════════════════════════════════════
  void _showSellerRegistrationSheet(BuildContext context) {
    final storeNameCtrl = TextEditingController();
    final storeDescCtrl = TextEditingController();

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (ctx) => Padding(
        padding: EdgeInsets.fromLTRB(20, 16, 20, MediaQuery.of(ctx).viewInsets.bottom + 30),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: Colors.grey.shade300, borderRadius: BorderRadius.circular(2)))),
            const SizedBox(height: 16),
            const Text('Buka Toko Gratis di NitipDong 🏪', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
            const SizedBox(height: 6),
            const Text('Dapatkan jutaan calon pembeli di seluruh Indonesia tanpa biaya pendaftaran.', style: TextStyle(fontSize: 11, color: AppTheme.textMuted)),
            const SizedBox(height: 16),
            TextField(controller: storeNameCtrl, decoration: const InputDecoration(labelText: 'Nama Toko Anda', hintText: 'Contoh: Berkah Store Official', contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 10))),
            const SizedBox(height: 10),
            TextField(controller: storeDescCtrl, maxLines: 2, decoration: const InputDecoration(labelText: 'Deskripsi Singkat Toko', hintText: 'Jual produk original berkualitas...', contentPadding: EdgeInsets.all(12))),
            const SizedBox(height: 18),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () {
                  if (storeNameCtrl.text.isNotEmpty) {
                    Navigator.pop(ctx);
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(
                        content: Text('Pendaftaran Toko "${storeNameCtrl.text}" berhasil diajukan! 🎉'),
                        backgroundColor: Colors.green,
                        behavior: SnackBarBehavior.floating,
                      ),
                    );
                  }
                },
                child: const Text('Daftarkan Toko Sekarang'),
              ),
            ),
          ],
        ),
      ),
    );
  }

  // ══════════════════════════════════════════════════
  // 5. PUSAT BANTUAN & CS 24/7 MODAL SHEET
  // ══════════════════════════════════════════════════
  void _showHelpCenterSheet(BuildContext context) {
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
            const Text('Pusat Bantuan & Customer Service 🎧', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
            const SizedBox(height: 14),
            const ExpansionTile(
              title: Text('Bagaimana cara memesan produk?', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
              children: [
                Padding(
                  padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  child: Text('Pilih produk yang Anda inginkan, masukkan ke keranjang, klik Checkout, lalu pilih metode pembayaran dan selesaikan pembayaran.', style: TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                ),
              ],
            ),
            const ExpansionTile(
              title: Text('Metode pembayaran apa saja yang didukung?', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
              children: [
                Padding(
                  padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  child: Text('Kami mendukung Transfer Bank (BCA, Mandiri, BRI, BNI), QRIS, E-Wallet (GoPay, OVO, Dana), dan Saldo NitipPay.', style: TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                ),
              ],
            ),
            const ExpansionTile(
              title: Text('Bagaimana jika produk rusak / tidak sesuai?', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
              children: [
                Padding(
                  padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  child: Text('NitipDong memberikan garansi 100% retur dan uang kembali. Laporkan melalui menu Pesanan Saya dalam 2x24 jam setelah barang sampai.', style: TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                ),
              ],
            ),
            const SizedBox(height: 16),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                icon: const Icon(Icons.chat_bubble_outline, size: 16),
                label: const Text('Hubungi CS WhatsApp Official (24/7)'),
                style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF25D366)),
                onPressed: () {
                  Navigator.pop(ctx);
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      content: Text('Menghubungkan ke Customer Service NitipDong WhatsApp... 💬'),
                      behavior: SnackBarBehavior.floating,
                    ),
                  );
                },
              ),
            ),
          ],
        ),
      ),
    );
  }

  // ══════════════════════════════════════════════════
  // 6. WISHLIST MODAL SHEET
  // ══════════════════════════════════════════════════
  void _showWishlistModal(BuildContext context) {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (ctx) => Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: Colors.grey.shade300, borderRadius: BorderRadius.circular(2)))),
            const SizedBox(height: 20),
            const Icon(Icons.favorite_rounded, color: Colors.pink, size: 48),
            const SizedBox(height: 12),
            const Text('Wishlist & Produk Favorit ❤️', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
            const SizedBox(height: 6),
            const Text('Simpan produk impian Anda dengan menekan ikon hati pada detail produk.', textAlign: TextAlign.center, style: TextStyle(fontSize: 11, color: AppTheme.textMuted)),
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

  // ══════════════════════════════════════════════════
  // 7. EDIT PROFILE DIALOG
  // ══════════════════════════════════════════════════
  void _showEditProfileDialog(BuildContext context, String currentName, String currentEmail) {
    final nameCtrl = TextEditingController(text: currentName);

    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Edit Profil Pengguna', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            TextField(
              controller: nameCtrl,
              decoration: const InputDecoration(labelText: 'Nama Lengkap', contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 10)),
            ),
            const SizedBox(height: 10),
            TextField(
              readOnly: true,
              decoration: InputDecoration(
                labelText: 'Email Terdaftar',
                hintText: currentEmail,
                filled: true,
                fillColor: Colors.grey.shade100,
                contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
              ),
            ),
          ],
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Batal')),
          ElevatedButton(
            onPressed: () {
              Navigator.pop(ctx);
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text('Profil berhasil diperbarui! ✅'), backgroundColor: Colors.green, behavior: SnackBarBehavior.floating),
              );
            },
            child: const Text('Simpan'),
          ),
        ],
      ),
    );
  }

  // ══════════════════════════════════════════════════
  // 8. LOGOUT CONFIRMATION DIALOG
  // ══════════════════════════════════════════════════
  void _showLogoutConfirmDialog(BuildContext context, AuthProvider authProvider) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Konfirmasi Keluar', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800)),
        content: const Text('Apakah Anda yakin ingin keluar dari akun NitipDong?'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Batal')),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            onPressed: () async {
              Navigator.pop(ctx);
              await authProvider.logout();
            },
            child: const Text('Keluar'),
          ),
        ],
      ),
    );
  }
}
