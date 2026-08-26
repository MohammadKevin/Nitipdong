import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../providers/auth_provider.dart';
import '../../services/api_service.dart';
import '../../services/google_auth_service.dart';
import 'login_screen.dart';
import 'otp_verification_screen.dart';
import '../../widgets/flip_page_route.dart';

class RegisterScreen extends StatefulWidget {
  final bool isFromSplash;

  const RegisterScreen({Key? key, this.isFromSplash = false}) : super(key: key);

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();

  bool _obscurePassword = true;
  bool _obscureConfirmPassword = true;
  bool _isGoogleLoading = false;

  void _handleGoogleSignIn() async {
    if (_isGoogleLoading) return;
    setState(() => _isGoogleLoading = true);

    try {
      await GoogleAuthService.signInWithGoogle(context);
    } catch (_) {
    } finally {
      if (mounted) {
        setState(() => _isGoogleLoading = false);
      }
    }
  }

  double _passwordStrength = 0.0;
  String _strengthLabel = 'Sangat Lemah';
  Color _strengthColor = Colors.red;

  @override
  void initState() {
    super.initState();
    _passwordController.addListener(_calculatePasswordStrength);
  }

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _passwordController.dispose();
    _confirmPasswordController.dispose();
    super.dispose();
  }

  void _calculatePasswordStrength() {
    final password = _passwordController.text;
    if (password.isEmpty) {
      setState(() {
        _passwordStrength = 0.0;
        _strengthLabel = 'Kosong';
        _strengthColor = Colors.grey;
      });
      return;
    }

    double strength = 0.0;
    if (password.length >= 8) strength += 0.35;
    if (password.contains(RegExp(r'[0-9]'))) strength += 0.35;
    if (password.contains(RegExp(r'[A-Z]')) || password.contains(RegExp(r'[!@#\$%^&*(),.?":{}|<>]'))) strength += 0.30;

    String label = 'Lemah';
    Color color = Colors.red;

    if (strength >= 0.9) {
      label = 'Sangat Kuat 🔒';
      color = const Color(0xFF10B981);
    } else if (strength >= 0.6) {
      label = 'Sedang 🛡️';
      color = const Color(0xFFF59E0B);
    }

    setState(() {
      _passwordStrength = strength.clamp(0.1, 1.0);
      _strengthLabel = label;
      _strengthColor = color;
    });
  }

  void _handleRegister() async {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    final name = _nameController.text.trim();
    final email = _emailController.text.trim();
    final phone = _phoneController.text.trim();
    final password = _passwordController.text.trim();
    final confirmPassword = _confirmPasswordController.text.trim();

    if (name.isEmpty || email.isEmpty || password.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Harap lengkapi semua kolom yang bertanda wajib.'),
          behavior: SnackBarBehavior.floating,
          backgroundColor: Colors.redAccent,
        ),
      );
      return;
    }

    if (password.length < 8) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Kata sandi harus terdiri dari minimal 8 karakter.'),
          behavior: SnackBarBehavior.floating,
          backgroundColor: Colors.redAccent,
        ),
      );
      return;
    }

    if (password != confirmPassword) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Konfirmasi kata sandi tidak cocok. Harap periksa kembali.'),
          behavior: SnackBarBehavior.floating,
          backgroundColor: Colors.redAccent,
        ),
      );
      return;
    }

    final res = await authProvider.register(name, email, password, confirmPassword, phone: phone);
    if (!mounted) return;

    if (res['success'] == true) {
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(
          builder: (context) => OtpVerificationScreen(
            identifier: email,
            otpPreview: res['otp_preview'],
            isFromSplash: widget.isFromSplash,
          ),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      body: SingleChildScrollView(
        child: Column(
          children: [
            // Top Hero Gradient Banner
            Container(
              width: double.infinity,
              padding: EdgeInsets.only(
                top: MediaQuery.of(context).padding.top + 20,
                bottom: 36,
                left: 24,
                right: 24,
              ),
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  colors: [Color(0xFF0F172A), Color(0xFF1E293B), Color(0xFF0284C7)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.only(
                  bottomLeft: Radius.circular(32),
                  bottomRight: Radius.circular(32),
                ),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Row(
                        children: [
                          Container(
                            width: 38,
                            height: 38,
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(10),
                              boxShadow: [
                                BoxShadow(
                                  color: const Color(0xFF06B6D4).withOpacity(0.4),
                                  blurRadius: 10,
                                  offset: const Offset(0, 4),
                                ),
                              ],
                            ),
                            child: ClipRRect(
                              borderRadius: BorderRadius.circular(10),
                              child: Image.asset(
                                'assets/icon/app_icon.png',
                                fit: BoxFit.cover,
                              ),
                            ),
                          ),
                          const SizedBox(width: 10),
                          RichText(
                            text: const TextSpan(
                              style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Colors.white),
                              children: [
                                TextSpan(text: 'Nitip'),
                                TextSpan(text: 'Dong', style: TextStyle(color: Color(0xFF38BDF8))),
                              ],
                            ),
                          ),
                        ],
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.12),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: Colors.white.withOpacity(0.18)),
                        ),
                        child: Text(
                          'v${ApiService.currentAppVersion}',
                          style: const TextStyle(color: Color(0xFF38BDF8), fontSize: 11, fontWeight: FontWeight.w700),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 24),

                  const Text(
                    'Gabung Bersama Kami! 🌟',
                    style: TextStyle(
                      fontSize: 22,
                      fontWeight: FontWeight.w900,
                      color: Colors.white,
                      letterSpacing: -0.5,
                    ),
                  ),
                  const SizedBox(height: 6),
                  const Text(
                    'Daftar akun untuk berbelanja, titip jastip, atau buka tokomu sendiri.',
                    style: TextStyle(fontSize: 12.5, color: Colors.white70, height: 1.4),
                  ),
                ],
              ),
            ),

            // Form Container Card
            Transform.translate(
              offset: const Offset(0, -16),
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 20),
                child: Container(
                  padding: const EdgeInsets.all(22),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(24),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.06),
                        blurRadius: 20,
                        offset: const Offset(0, 8),
                      ),
                    ],
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Full Name Input
                      const Text(
                        'Nama Lengkap *',
                        style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13, color: Color(0xFF1E293B)),
                      ),
                      const SizedBox(height: 6),
                      TextField(
                        controller: _nameController,
                        style: const TextStyle(fontSize: 13.5, fontWeight: FontWeight.w600),
                        decoration: InputDecoration(
                          hintText: 'Contoh: Kevin Wijaya',
                          hintStyle: TextStyle(color: Colors.grey.shade400, fontSize: 13),
                          prefixIcon: const Icon(Icons.person_outline_rounded, color: Color(0xFF0284C7), size: 19),
                          filled: true,
                          fillColor: const Color(0xFFF8FAFC),
                          border: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide(color: Colors.grey.shade200)),
                          enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide(color: Colors.grey.shade200)),
                          focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: const BorderSide(color: Color(0xFF0284C7), width: 1.8)),
                        ),
                      ),
                      const SizedBox(height: 14),

                      // Email Input
                      const Text(
                        'Alamat Email *',
                        style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13, color: Color(0xFF1E293B)),
                      ),
                      const SizedBox(height: 6),
                      TextField(
                        controller: _emailController,
                        keyboardType: TextInputType.emailAddress,
                        style: const TextStyle(fontSize: 13.5, fontWeight: FontWeight.w600),
                        decoration: InputDecoration(
                          hintText: 'nama@domain.com',
                          hintStyle: TextStyle(color: Colors.grey.shade400, fontSize: 13),
                          prefixIcon: const Icon(Icons.mail_outline_rounded, color: Color(0xFF0284C7), size: 19),
                          filled: true,
                          fillColor: const Color(0xFFF8FAFC),
                          border: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide(color: Colors.grey.shade200)),
                          enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide(color: Colors.grey.shade200)),
                          focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: const BorderSide(color: Color(0xFF0284C7), width: 1.8)),
                        ),
                      ),
                      const SizedBox(height: 14),

                      // Phone Input
                      const Text(
                        'Nomor Handphone (Opsional)',
                        style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13, color: Color(0xFF1E293B)),
                      ),
                      const SizedBox(height: 6),
                      TextField(
                        controller: _phoneController,
                        keyboardType: TextInputType.phone,
                        style: const TextStyle(fontSize: 13.5, fontWeight: FontWeight.w600),
                        decoration: InputDecoration(
                          hintText: 'Contoh: 081234567890',
                          hintStyle: TextStyle(color: Colors.grey.shade400, fontSize: 13),
                          prefixIcon: const Icon(Icons.phone_android_rounded, color: Color(0xFF0284C7), size: 19),
                          filled: true,
                          fillColor: const Color(0xFFF8FAFC),
                          border: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide(color: Colors.grey.shade200)),
                          enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide(color: Colors.grey.shade200)),
                          focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: const BorderSide(color: Color(0xFF0284C7), width: 1.8)),
                        ),
                      ),
                      const SizedBox(height: 14),

                      // Password Input
                      const Text(
                        'Kata Sandi *',
                        style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13, color: Color(0xFF1E293B)),
                      ),
                      const SizedBox(height: 6),
                      TextField(
                        controller: _passwordController,
                        obscureText: _obscurePassword,
                        style: const TextStyle(fontSize: 13.5, fontWeight: FontWeight.w600),
                        decoration: InputDecoration(
                          hintText: 'Minimal 8 karakter',
                          hintStyle: TextStyle(color: Colors.grey.shade400, fontSize: 13),
                          prefixIcon: const Icon(Icons.lock_outline_rounded, color: Color(0xFF0284C7), size: 19),
                          suffixIcon: IconButton(
                            icon: Icon(
                              _obscurePassword ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                              color: Colors.grey.shade500,
                              size: 19,
                            ),
                            onPressed: () => setState(() => _obscurePassword = !_obscurePassword),
                          ),
                          filled: true,
                          fillColor: const Color(0xFFF8FAFC),
                          border: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide(color: Colors.grey.shade200)),
                          enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide(color: Colors.grey.shade200)),
                          focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: const BorderSide(color: Color(0xFF0284C7), width: 1.8)),
                        ),
                      ),

                      // Password Strength Indicator
                      if (_passwordController.text.isNotEmpty) ...[
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            Expanded(
                              child: ClipRRect(
                                borderRadius: BorderRadius.circular(4),
                                child: LinearProgressIndicator(
                                  value: _passwordStrength,
                                  minHeight: 4,
                                  backgroundColor: Colors.grey.shade200,
                                  valueColor: AlwaysStoppedAnimation<Color>(_strengthColor),
                                ),
                              ),
                            ),
                            const SizedBox(width: 10),
                            Text(
                              _strengthLabel,
                              style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: _strengthColor),
                            ),
                          ],
                        ),
                      ],

                      const SizedBox(height: 14),

                      // Confirm Password Input
                      const Text(
                        'Konfirmasi Kata Sandi *',
                        style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13, color: Color(0xFF1E293B)),
                      ),
                      const SizedBox(height: 6),
                      TextField(
                        controller: _confirmPasswordController,
                        obscureText: _obscureConfirmPassword,
                        style: const TextStyle(fontSize: 13.5, fontWeight: FontWeight.w600),
                        decoration: InputDecoration(
                          hintText: 'Ulangi kata sandi',
                          hintStyle: TextStyle(color: Colors.grey.shade400, fontSize: 13),
                          prefixIcon: const Icon(Icons.shield_outlined, color: Color(0xFF0284C7), size: 19),
                          suffixIcon: IconButton(
                            icon: Icon(
                              _obscureConfirmPassword ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                              color: Colors.grey.shade500,
                              size: 19,
                            ),
                            onPressed: () => setState(() => _obscureConfirmPassword = !_obscureConfirmPassword),
                          ),
                          filled: true,
                          fillColor: const Color(0xFFF8FAFC),
                          border: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide(color: Colors.grey.shade200)),
                          enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: BorderSide(color: Colors.grey.shade200)),
                          focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: const BorderSide(color: Color(0xFF0284C7), width: 1.8)),
                        ),
                      ),

                      // Error State Banner
                      if (authProvider.errorMessage != null) ...[
                        const SizedBox(height: 12),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                          decoration: BoxDecoration(
                            color: Colors.red.shade50,
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(color: Colors.red.shade200),
                          ),
                          child: Row(
                            children: [
                              const Icon(Icons.error_outline_rounded, color: Colors.red, size: 18),
                              const SizedBox(width: 8),
                              Expanded(
                                child: Text(
                                  authProvider.errorMessage!,
                                  style: const TextStyle(color: Colors.red, fontSize: 12, fontWeight: FontWeight.w600),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],

                      const SizedBox(height: 20),

                      // Submit Register Button
                      Container(
                        width: double.infinity,
                        height: 50,
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(14),
                          gradient: const LinearGradient(
                            colors: [Color(0xFF0284C7), Color(0xFF0369A1)],
                            begin: Alignment.centerLeft,
                            end: Alignment.centerRight,
                          ),
                          boxShadow: [
                            BoxShadow(
                              color: const Color(0xFF0284C7).withOpacity(0.35),
                              blurRadius: 12,
                              offset: const Offset(0, 4),
                            ),
                          ],
                        ),
                        child: ElevatedButton(
                          onPressed: authProvider.isLoading ? null : _handleRegister,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.transparent,
                            shadowColor: Colors.transparent,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                          ),
                          child: authProvider.isLoading
                              ? const SizedBox(
                                  width: 22,
                                  height: 22,
                                  child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                                )
                              : const Row(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Text(
                                      'Daftar Sekarang',
                                      style: TextStyle(fontSize: 14, fontWeight: FontWeight.w800, color: Colors.white),
                                    ),
                                    SizedBox(width: 6),
                                    Icon(Icons.arrow_forward_rounded, size: 16, color: Colors.white),
                                  ],
                                ),
                        ),
                      ),

                      // Google Divider
                      Padding(
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        child: Row(
                          children: [
                            Expanded(child: Divider(color: Colors.grey.shade200, thickness: 1)),
                            Padding(
                              padding: const EdgeInsets.symmetric(horizontal: 10),
                              child: Text(
                                'atau daftar dengan',
                                style: TextStyle(fontSize: 11, color: Colors.grey.shade400, fontWeight: FontWeight.w600),
                              ),
                            ),
                            Expanded(child: Divider(color: Colors.grey.shade200, thickness: 1)),
                          ],
                        ),
                      ),

                      // Google Sign In Button
                      Container(
                        width: double.infinity,
                        height: 48,
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(14),
                          border: Border.all(color: const Color(0xFFE2E8F0)),
                          color: Colors.white,
                        ),
                        child: OutlinedButton(
                          onPressed: _isGoogleLoading ? null : _handleGoogleSignIn,
                          style: OutlinedButton.styleFrom(
                            side: BorderSide.none,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                          ),
                          child: _isGoogleLoading
                              ? const SizedBox(
                                  width: 20,
                                  height: 20,
                                  child: CircularProgressIndicator(
                                    strokeWidth: 2,
                                    color: Color(0xFF0891B2),
                                  ),
                                )
                              : const Row(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Icon(Icons.g_mobiledata_rounded, color: Colors.red, size: 28),
                                    SizedBox(width: 4),
                                    Text(
                                      'Daftar dengan Google',
                                      style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: Color(0xFF334155)),
                                    ),
                                  ],
                                ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),

            // Bottom Navigation Link to Login
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 24),
              child: Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const Text('Sudah punya akun?', style: TextStyle(fontSize: 13, color: Color(0xFF64748B))),
                      const SizedBox(width: 4),
                      TextButton(
                        onPressed: () {
                          Navigator.pushReplacement(
                            context,
                            Flip3DPageRoute(
                              widget: LoginScreen(isFromSplash: widget.isFromSplash),
                              isReverse: true,
                            ),
                          );
                        },
                        style: TextButton.styleFrom(padding: EdgeInsets.zero, minimumSize: Size.zero),
                        child: const Text(
                          'Masuk ke Akun 🚀',
                          style: TextStyle(color: Color(0xFF0284C7), fontWeight: FontWeight.w800, fontSize: 13),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 24),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
