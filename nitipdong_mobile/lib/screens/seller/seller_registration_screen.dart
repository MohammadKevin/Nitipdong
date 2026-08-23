import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../providers/auth_provider.dart';
import '../../services/api_service.dart';
import 'seller_dashboard_screen.dart';

class SellerRegistrationScreen extends StatefulWidget {
  const SellerRegistrationScreen({Key? key}) : super(key: key);

  @override
  State<SellerRegistrationScreen> createState() => _SellerRegistrationScreenState();
}

class _SellerRegistrationScreenState extends State<SellerRegistrationScreen> {
  final _formKey = GlobalKey<FormState>();

  final TextEditingController _storeNameController = TextEditingController();
  final TextEditingController _descController = TextEditingController();
  final TextEditingController _addressController = TextEditingController();
  final TextEditingController _cityController = TextEditingController(text: 'Surabaya');
  late TextEditingController _phoneController;

  bool _agreedToTerms = true;
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    final user = Provider.of<AuthProvider>(context, listen: false).user;
    _phoneController = TextEditingController(text: user?.phone ?? '');
  }

  @override
  void dispose() {
    _storeNameController.dispose();
    _descController.dispose();
    _addressController.dispose();
    _cityController.dispose();
    _phoneController.dispose();
    super.dispose();
  }

  Future<void> _submitStoreRegistration() async {
    if (!_formKey.currentState!.validate()) return;
    if (!_agreedToTerms) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Harap setujui Syarat & Ketentuan Penjual.')),
      );
      return;
    }

    setState(() => _isLoading = true);

    final res = await ApiService.registerSellerStore(
      name: _storeNameController.text.trim(),
      address: _addressController.text.trim(),
      city: _cityController.text.trim(),
      phone: _phoneController.text.trim(),
      description: _descController.text.trim(),
    );

    setState(() => _isLoading = false);

    if (res['success'] == true && mounted) {
      // Refresh AuthProvider profile
      await Provider.of<AuthProvider>(context, listen: false).checkAuth();

      // Show congratulations dialog
      showDialog(
        context: context,
        barrierDismissible: false,
        builder: (ctx) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                width: 64,
                height: 64,
                decoration: BoxDecoration(
                  color: Colors.amber.shade50,
                  shape: BoxShape.circle,
                  border: Border.all(color: Colors.amber.shade300, width: 2),
                ),
                child: const Icon(Icons.storefront_rounded, color: Colors.amber, size: 36),
              ),
              const SizedBox(height: 16),
              const Text(
                'Toko Resmi Dibuka! 🛍️',
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900),
              ),
              const SizedBox(height: 6),
              Text(
                'Selamat! Toko "${_storeNameController.text}" kini aktif. Anda siap menambahkan produk dan menerima pesanan jastip.',
                textAlign: TextAlign.center,
                style: const TextStyle(fontSize: 12.5, color: AppTheme.textSecondary, height: 1.4),
              ),
              const SizedBox(height: 20),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.primary,
                    padding: const EdgeInsets.symmetric(vertical: 12),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  onPressed: () {
                    Navigator.pop(ctx);
                    Navigator.pushReplacement(
                      context,
                      MaterialPageRoute(builder: (_) => const SellerDashboardScreen()),
                    );
                  },
                  child: const Text('Buka Seller Center 🏪', style: TextStyle(fontWeight: FontWeight.w800)),
                ),
              ),
            ],
          ),
        ),
      );
    } else {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(res['message'] ?? 'Gagal membuka toko.'),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('Buka Toko Jualan 🏪', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800)),
        backgroundColor: AppTheme.accentNavy,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            // Top Hero Banner
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(20),
              decoration: const BoxDecoration(
                color: AppTheme.accentNavy,
                borderRadius: BorderRadius.only(
                  bottomLeft: Radius.circular(24),
                  bottomRight: Radius.circular(24),
                ),
              ),
              child: Column(
                children: [
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.1),
                      shape: BoxShape.circle,
                      border: Border.all(color: Colors.amberAccent.withOpacity(0.5)),
                    ),
                    child: const Icon(Icons.store_rounded, color: Colors.amberAccent, size: 36),
                  ),
                  const SizedBox(height: 12),
                  const Text(
                    'Mulai Jualan & Terima Pesanan Jastip',
                    textAlign: TextAlign.center,
                    style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.w900),
                  ),
                  const SizedBox(height: 4),
                  const Text(
                    'Dapatkan jutaan calon pembeli di seluruh Indonesia dengan sistem pengiriman kurir otomatis dan pembayaran aman Midtrans.',
                    textAlign: TextAlign.center,
                    style: TextStyle(color: Colors.white70, fontSize: 11.5, height: 1.4),
                  ),
                ],
              ),
            ),

            // Form Container
            Padding(
              padding: const EdgeInsets.all(20),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Informasi Toko', style: TextStyle(fontSize: 13.5, fontWeight: FontWeight.w800, color: AppTheme.textPrimary)),
                    const SizedBox(height: 12),

                    // Nama Toko
                    _buildTextField(
                      controller: _storeNameController,
                      label: 'Nama Toko / Brand Anda',
                      icon: Icons.storefront_outlined,
                      hint: 'Contoh: Kevin Store Official',
                      validator: (val) => val == null || val.isEmpty ? 'Nama toko wajib diisi' : null,
                    ),
                    const SizedBox(height: 12),

                    // Deskripsi Toko
                    _buildTextField(
                      controller: _descController,
                      label: 'Deskripsi Singkat Toko',
                      icon: Icons.description_outlined,
                      hint: 'Contoh: Jual gadget, fashion & kuliner original terpercaya.',
                      maxLines: 2,
                    ),
                    const SizedBox(height: 20),

                    const Text('Lokasi & Kontak Toko', style: TextStyle(fontSize: 13.5, fontWeight: FontWeight.w800, color: AppTheme.textPrimary)),
                    const SizedBox(height: 12),

                    // Alamat Lengkap Toko
                    _buildTextField(
                      controller: _addressController,
                      label: 'Alamat Lengkap Toko (Titik Pickup Kurir)',
                      icon: Icons.location_on_outlined,
                      hint: 'Contoh: Jl. Basuki Rahmat No. 88',
                      validator: (val) => val == null || val.isEmpty ? 'Alamat wajib diisi' : null,
                    ),
                    const SizedBox(height: 12),

                    // Kota Toko
                    _buildTextField(
                      controller: _cityController,
                      label: 'Kota / Kabupaten',
                      icon: Icons.location_city_rounded,
                      hint: 'Contoh: Surabaya, Jakarta, Bandung',
                      validator: (val) => val == null || val.isEmpty ? 'Kota wajib diisi' : null,
                    ),
                    const SizedBox(height: 12),

                    // No WhatsApp Toko
                    _buildTextField(
                      controller: _phoneController,
                      label: 'Nomor WhatsApp Toko',
                      icon: Icons.phone_android_rounded,
                      keyboardType: TextInputType.phone,
                      validator: (val) => val == null || val.isEmpty ? 'Nomor HP wajib diisi' : null,
                    ),
                    const SizedBox(height: 16),

                    // Syarat & Ketentuan Checkbox
                    Row(
                      children: [
                        Checkbox(
                          value: _agreedToTerms,
                          activeColor: AppTheme.primary,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(4)),
                          onChanged: (val) => setState(() => _agreedToTerms = val ?? false),
                        ),
                        const Expanded(
                          child: Text(
                            'Saya menyetujui Syarat & Ketentuan Toko Penjual NitipDong.',
                            style: TextStyle(fontSize: 11.5, color: AppTheme.textSecondary),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 20),

                    // Submit Button
                    SizedBox(
                      width: double.infinity,
                      height: 50,
                      child: ElevatedButton(
                        style: ElevatedButton.styleFrom(
                          backgroundColor: AppTheme.primary,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                          elevation: 3,
                        ),
                        onPressed: _isLoading ? null : _submitStoreRegistration,
                        child: _isLoading
                            ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
                            : const Text(
                                'Buka Toko Sekarang Gratis 🚀',
                                style: TextStyle(fontSize: 13.5, fontWeight: FontWeight.w800, color: Colors.white),
                              ),
                      ),
                    ),
                    const SizedBox(height: 30),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildTextField({
    required TextEditingController controller,
    required String label,
    required IconData icon,
    String? hint,
    int maxLines = 1,
    TextInputType keyboardType = TextInputType.text,
    String? Function(String?)? validator,
  }) {
    return TextFormField(
      controller: controller,
      keyboardType: keyboardType,
      maxLines: maxLines,
      validator: validator,
      style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
      decoration: InputDecoration(
        labelText: label,
        hintText: hint,
        prefixIcon: Icon(icon, color: AppTheme.primary, size: 20),
        filled: true,
        fillColor: Colors.white,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppTheme.border),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppTheme.border),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppTheme.primary, width: 1.5),
        ),
      ),
    );
  }
}
