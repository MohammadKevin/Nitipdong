import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../providers/auth_provider.dart';
import '../../services/api_service.dart';
import 'courier_home_screen.dart';

class CourierRegistrationScreen extends StatefulWidget {
  const CourierRegistrationScreen({Key? key}) : super(key: key);

  @override
  State<CourierRegistrationScreen> createState() => _CourierRegistrationScreenState();
}

class _CourierRegistrationScreenState extends State<CourierRegistrationScreen> {
  final _formKey = GlobalKey<FormState>();

  late TextEditingController _nameController;
  late TextEditingController _phoneController;
  final TextEditingController _nikController = TextEditingController();
  final TextEditingController _simController = TextEditingController();
  final TextEditingController _plateController = TextEditingController();
  final TextEditingController _cityController = TextEditingController(text: 'Surabaya');

  String _selectedVehicle = 'Sepeda Motor';
  bool _agreedToTerms = true;
  bool _isLoading = false;

  final List<String> _vehicleTypes = [
    'Sepeda Motor',
    'Mobil Van / Blind Van',
    'Mobil Pick-Up',
    'Mobil Penumpang (City Car)',
  ];

  @override
  void initState() {
    super.initState();
    final user = Provider.of<AuthProvider>(context, listen: false).user;
    _nameController = TextEditingController(text: user?.name ?? '');
    _phoneController = TextEditingController(text: user?.phone ?? '');
  }

  @override
  void dispose() {
    _nameController.dispose();
    _phoneController.dispose();
    _nikController.dispose();
    _simController.dispose();
    _plateController.dispose();
    _cityController.dispose();
    super.dispose();
  }

  Future<void> _submitRegistration() async {
    if (!_formKey.currentState!.validate()) return;
    if (!_agreedToTerms) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Harap setujui Syarat & Ketentuan Kemitraan Kurir.')),
      );
      return;
    }

    setState(() => _isLoading = true);

    final res = await ApiService.registerCourier(
      nik: _nikController.text.trim(),
      simNumber: _simController.text.trim(),
      vehicleType: _selectedVehicle,
      plateNumber: _plateController.text.trim(),
      phone: _phoneController.text.trim(),
      city: _cityController.text.trim(),
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
                  color: Colors.green.shade50,
                  shape: BoxShape.circle,
                  border: Border.all(color: Colors.green.shade200, width: 2),
                ),
                child: const Icon(Icons.check_circle_rounded, color: Colors.green, size: 40),
              ),
              const SizedBox(height: 16),
              const Text(
                'Selamat! 🎉',
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900),
              ),
              const SizedBox(height: 6),
              const Text(
                'Pendaftaran Anda telah aktif! Anda kini resmi menjadi Mitra Kurir NitipDong.',
                textAlign: TextAlign.center,
                style: TextStyle(fontSize: 12.5, color: AppTheme.textSecondary, height: 1.4),
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
                      MaterialPageRoute(builder: (_) => const CourierHomeScreen()),
                    );
                  },
                  child: const Text('Buka Dashboard Kurir 🛵', style: TextStyle(fontWeight: FontWeight.w800)),
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
            content: Text(res['message'] ?? 'Gagal mendaftar kurir.'),
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
        title: const Text('Pendaftaran Mitra Kurir 🛵', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800)),
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
                      border: Border.all(color: Colors.cyanAccent.withOpacity(0.5)),
                    ),
                    child: const Icon(Icons.delivery_dining_rounded, color: Colors.cyanAccent, size: 36),
                  ),
                  const SizedBox(height: 12),
                  const Text(
                    'Gabung Jadi Mitra Pengantar NitipDong',
                    textAlign: TextAlign.center,
                    style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.w900),
                  ),
                  const SizedBox(height: 4),
                  const Text(
                    'Dapatkan penghasilan harian fleksibel, kelola tugas pickup dari toko, dan lacak rute GPS otomatis.',
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
                    const Text('Informasi Pribadi & Kontak', style: TextStyle(fontSize: 13.5, fontWeight: FontWeight.w800, color: AppTheme.textPrimary)),
                    const SizedBox(height: 12),

                    // Nama
                    _buildTextField(
                      controller: _nameController,
                      label: 'Nama Lengkap',
                      icon: Icons.person_outline_rounded,
                      validator: (val) => val == null || val.isEmpty ? 'Nama wajib diisi' : null,
                    ),
                    const SizedBox(height: 12),

                    // No WhatsApp
                    _buildTextField(
                      controller: _phoneController,
                      label: 'Nomor WhatsApp / HP Aktif',
                      icon: Icons.phone_android_rounded,
                      keyboardType: TextInputType.phone,
                      validator: (val) => val == null || val.isEmpty ? 'Nomor HP wajib diisi' : null,
                    ),
                    const SizedBox(height: 12),

                    // NIK KTP
                    _buildTextField(
                      controller: _nikController,
                      label: 'Nomor Induk Kependudukan (NIK KTP)',
                      icon: Icons.badge_outlined,
                      keyboardType: TextInputType.number,
                      hint: '16 digit NIK KTP',
                      validator: (val) {
                        if (val == null || val.isEmpty) return 'NIK KTP wajib diisi';
                        if (val.length < 16) return 'NIK harus 16 digit';
                        return null;
                      },
                    ),
                    const SizedBox(height: 20),

                    const Text('Kendaraan & Lisensi Mengemudi', style: TextStyle(fontSize: 13.5, fontWeight: FontWeight.w800, color: AppTheme.textPrimary)),
                    const SizedBox(height: 12),

                    // No SIM
                    _buildTextField(
                      controller: _simController,
                      label: 'Nomor SIM (C / A Aktif)',
                      icon: Icons.credit_card_rounded,
                      hint: 'Nomor SIM Anda',
                      validator: (val) => val == null || val.isEmpty ? 'Nomor SIM wajib diisi' : null,
                    ),
                    const SizedBox(height: 12),

                    // Jenis Kendaraan Dropdown
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: AppTheme.border),
                      ),
                      child: DropdownButtonHideUnderline(
                        child: DropdownButton<String>(
                          value: _selectedVehicle,
                          isExpanded: true,
                          icon: const Icon(Icons.keyboard_arrow_down_rounded, color: AppTheme.textSecondary),
                          items: _vehicleTypes.map((type) {
                            return DropdownMenuItem(
                              value: type,
                              child: Row(
                                children: [
                                  Icon(
                                    type.contains('Motor') ? Icons.two_wheeler_rounded : Icons.directions_car_rounded,
                                    color: AppTheme.primary,
                                    size: 20,
                                  ),
                                  const SizedBox(width: 10),
                                  Text(type, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600)),
                                ],
                              ),
                            );
                          }).toList(),
                          onChanged: (val) {
                            if (val != null) setState(() => _selectedVehicle = val);
                          },
                        ),
                      ),
                    ),
                    const SizedBox(height: 12),

                    // Plat Nomor Kendaraan
                    _buildTextField(
                      controller: _plateController,
                      label: 'Nomor Plat Kendaraan',
                      icon: Icons.pin_outlined,
                      hint: 'Contoh: L 4242 NK / B 1234 ABC',
                      validator: (val) => val == null || val.isEmpty ? 'Plat nomor wajib diisi' : null,
                    ),
                    const SizedBox(height: 12),

                    // Kota Operasional
                    _buildTextField(
                      controller: _cityController,
                      label: 'Kota Utama Operasional',
                      icon: Icons.location_city_rounded,
                      hint: 'Contoh: Surabaya, Jakarta, Bandung, Malang',
                      validator: (val) => val == null || val.isEmpty ? 'Kota wajib diisi' : null,
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
                            'Saya menyetujui Syarat & Ketentuan Mitra Kurir NitipDong.',
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
                        onPressed: _isLoading ? null : _submitRegistration,
                        child: _isLoading
                            ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5))
                            : const Text(
                                'Kirim Pendaftaran & Aktifkan Kurir 🚀',
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
    TextInputType keyboardType = TextInputType.text,
    String? Function(String?)? validator,
  }) {
    return TextFormField(
      controller: controller,
      keyboardType: keyboardType,
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
