import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../providers/auth_provider.dart';
import '../main_nav_screen.dart';
import 'login_screen.dart';

class RegisterScreen extends StatefulWidget {
  final bool isFromSplash;

  const RegisterScreen({Key? key, this.isFromSplash = false}) : super(key: key);

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();
  bool _obscurePassword = true;

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Daftar Akun Baru'),
        automaticallyImplyLeading: !widget.isFromSplash,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Bergabung dengan NitipDong 🛍️',
              style: Theme.of(context).textTheme.displayLarge?.copyWith(fontSize: 22),
            ),
            const SizedBox(height: 6),
            Text(
              'Daftar akun gratis dan nikmati jutaan produk diskon serta voucher gratis ongkir.',
              style: Theme.of(context).textTheme.bodyMedium,
            ),
            const SizedBox(height: 25),

            // Name
            const Text('Nama Lengkap', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13)),
            const SizedBox(height: 8),
            TextField(
              controller: _nameController,
              decoration: const InputDecoration(
                hintText: 'Nama lengkap Anda',
                prefixIcon: Icon(Icons.person_outline, color: AppTheme.textMuted, size: 20),
              ),
            ),
            const SizedBox(height: 16),

            // Email
            const Text('Alamat Email', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13)),
            const SizedBox(height: 8),
            TextField(
              controller: _emailController,
              keyboardType: TextInputType.emailAddress,
              decoration: const InputDecoration(
                hintText: 'nama@domain.com',
                prefixIcon: Icon(Icons.email_outlined, color: AppTheme.textMuted, size: 20),
              ),
            ),
            const SizedBox(height: 16),

            // Password
            const Text('Kata Sandi (Minimal 8 Karakter)', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13)),
            const SizedBox(height: 8),
            TextField(
              controller: _passwordController,
              obscureText: _obscurePassword,
              decoration: InputDecoration(
                hintText: 'Buat kata sandi aman',
                prefixIcon: const Icon(Icons.lock_outline, color: AppTheme.textMuted, size: 20),
                suffixIcon: IconButton(
                  icon: Icon(
                    _obscurePassword ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                    color: AppTheme.textMuted,
                    size: 20,
                  ),
                  onPressed: () => setState(() => _obscurePassword = !_obscurePassword),
                ),
              ),
            ),
            const SizedBox(height: 16),

            // Confirm Password
            const Text('Konfirmasi Kata Sandi', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13)),
            const SizedBox(height: 8),
            TextField(
              controller: _confirmPasswordController,
              obscureText: _obscurePassword,
              decoration: const InputDecoration(
                hintText: 'Ulangi kata sandi Anda',
                prefixIcon: Icon(Icons.lock_reset, color: AppTheme.textMuted, size: 20),
              ),
            ),
            const SizedBox(height: 25),

            // Error Message
            if (authProvider.errorMessage != null) ...[
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.red.shade50,
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: Colors.red.shade200),
                ),
                child: Row(
                  children: [
                    const Icon(Icons.error_outline, color: Colors.red, size: 20),
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
              const SizedBox(height: 20),
            ],

            // Submit Button
            SizedBox(
              width: double.infinity,
              height: 50,
              child: ElevatedButton(
                onPressed: authProvider.isLoading
                    ? null
                    : () async {
                        final name = _nameController.text.trim();
                        final email = _emailController.text.trim();
                        final password = _passwordController.text.trim();
                        final confirmPassword = _confirmPasswordController.text.trim();

                        if (name.isEmpty || email.isEmpty || password.isEmpty) {
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(content: Text('Harap isi semua kolom pendaftaran')),
                          );
                          return;
                        }

                        if (password != confirmPassword) {
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(content: Text('Konfirmasi kata sandi tidak cocok')),
                          );
                          return;
                        }

                        final success = await authProvider.register(name, email, password, confirmPassword);
                        if (success && mounted) {
                          if (widget.isFromSplash) {
                            Navigator.pushReplacement(
                              context,
                              MaterialPageRoute(builder: (context) => const MainNavScreen()),
                            );
                          } else {
                            Navigator.pop(context);
                          }
                        }
                      },
                child: authProvider.isLoading
                    ? const SizedBox(
                        width: 22,
                        height: 22,
                        child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                      )
                    : const Text('Daftar Sekarang'),
              ),
            ),
            const SizedBox(height: 20),

            // Login Link
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Text('Sudah punya akun?', style: TextStyle(fontSize: 13, color: AppTheme.textSecondary)),
                TextButton(
                  onPressed: () {
                    Navigator.pushReplacement(
                      context,
                      MaterialPageRoute(
                        builder: (context) => LoginScreen(isFromSplash: widget.isFromSplash),
                      ),
                    );
                  },
                  child: const Text(
                    'Masuk ke Akun',
                    style: TextStyle(color: AppTheme.primary, fontWeight: FontWeight.w700, fontSize: 13),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 10),

            // Guest Explore Option
            Center(
              child: TextButton.icon(
                onPressed: () {
                  Navigator.pushReplacement(
                    context,
                    MaterialPageRoute(builder: (context) => const MainNavScreen()),
                  );
                },
                icon: const Text(
                  'Lewati & Jelajahi sebagai Tamu',
                  style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppTheme.textMuted),
                ),
                label: const Icon(Icons.arrow_forward_rounded, size: 14, color: AppTheme.textMuted),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
