import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../providers/auth_provider.dart';
import '../main_nav_screen.dart';
import '../../widgets/server_config_dialog.dart';
import 'register_screen.dart';

class LoginScreen extends StatefulWidget {
  final bool isFromSplash;

  const LoginScreen({Key? key, this.isFromSplash = false}) : super(key: key);

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _obscurePassword = true;

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Masuk ke Akun'),
        automaticallyImplyLeading: !widget.isFromSplash,
        actions: [
          IconButton(
            icon: const Icon(Icons.dns_rounded, size: 20),
            tooltip: 'Atur Server API',
            onPressed: () => ServerConfigDialog.show(context),
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SizedBox(height: 10),
            Text(
              'Selamat Datang di NitipDong! 👋',
              style: Theme.of(context).textTheme.displayLarge?.copyWith(fontSize: 22),
            ),
            const SizedBox(height: 6),
            Text(
              'Masuk atau daftar untuk mengakses ribuan promo, keranjang, dan riwayat pesanan Anda.',
              style: Theme.of(context).textTheme.bodyMedium,
            ),
            const SizedBox(height: 30),

            // Email Input
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
            const SizedBox(height: 20),

            // Password Input
            const Text('Kata Sandi', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13)),
            const SizedBox(height: 8),
            TextField(
              controller: _passwordController,
              obscureText: _obscurePassword,
              decoration: InputDecoration(
                hintText: 'Masukkan kata sandi Anda',
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
            const SizedBox(height: 12),

            // Forgot password
            Align(
              alignment: Alignment.centerRight,
              child: TextButton(
                onPressed: () {},
                child: const Text(
                  'Lupa Kata Sandi?',
                  style: TextStyle(color: AppTheme.primary, fontWeight: FontWeight.w700, fontSize: 12),
                ),
              ),
            ),
            const SizedBox(height: 20),

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

            // Login Button
            SizedBox(
              width: double.infinity,
              height: 50,
              child: ElevatedButton(
                onPressed: authProvider.isLoading
                    ? null
                    : () async {
                        final email = _emailController.text.trim();
                        final password = _passwordController.text.trim();
                        if (email.isEmpty || password.isEmpty) {
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(content: Text('Harap isi email dan kata sandi')),
                          );
                          return;
                        }

                        final success = await authProvider.login(email, password);
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
                    : const Text('Masuk Sekarang'),
              ),
            ),
            const SizedBox(height: 20),

            // Register Link
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Text('Belum punya akun?', style: TextStyle(fontSize: 13, color: AppTheme.textSecondary)),
                TextButton(
                  onPressed: () {
                    Navigator.pushReplacement(
                      context,
                      MaterialPageRoute(
                        builder: (context) => RegisterScreen(isFromSplash: widget.isFromSplash),
                      ),
                    );
                  },
                  child: const Text(
                    'Daftar Akun Baru',
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
