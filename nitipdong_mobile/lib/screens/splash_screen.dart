import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../theme/app_theme.dart';
import '../providers/auth_provider.dart';
import '../services/api_service.dart';
import '../screens/main_nav_screen.dart';
import '../screens/maintenance_screen.dart';
import '../screens/auth/login_screen.dart';
import '../screens/courier/courier_home_screen.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({Key? key}) : super(key: key);

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _initApp();
  }

  Future<void> _initApp() async {
    // Quick parallel check (Splash animation + System status + Auth session)
    try {
      final results = await Future.wait([
        ApiService.checkSystemStatus(),
        Provider.of<AuthProvider>(context, listen: false).checkAuth(),
        Future.delayed(const Duration(milliseconds: 700)),
      ]).timeout(const Duration(milliseconds: 3500));

      final systemStatus = results[0] as Map<String, dynamic>;
      if (systemStatus['is_maintenance'] == true) {
        if (!mounted) return;
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(
            builder: (context) => MaintenanceScreen(
              title: systemStatus['maintenance_title'] ?? 'Mode Pemeliharaan & Pengembangan 🛠️',
              message: systemStatus['maintenance_message'] ?? 'Aplikasi sedang dalam tahap peningkatan sistem.',
            ),
          ),
        );
        return;
      }
    } catch (_) {}

    if (!mounted) return;

    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    if (authProvider.isAuthenticated) {
      // User/Buyer always lands on standard Marketplace Dashboard Home
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => const MainNavScreen()),
      );
    } else {
      // New download / Not logged in -> Go to Login / Register Screen
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => const LoginScreen(isFromSplash: true)),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.accentNavy,
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // Logo Container
            Container(
              width: 96,
              height: 96,
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(24),
                boxShadow: [
                  BoxShadow(
                    color: AppTheme.primary.withOpacity(0.4),
                    blurRadius: 30,
                    offset: const Offset(0, 10),
                  ),
                ],
              ),
              child: ClipRRect(
                borderRadius: BorderRadius.circular(24),
                child: Image.asset(
                  'assets/icon/app_icon.png',
                  width: 96,
                  height: 96,
                  fit: BoxFit.cover,
                  errorBuilder: (context, error, stackTrace) => Container(
                    color: AppTheme.surface,
                    child: const Center(
                      child: Icon(
                        Icons.shopping_bag,
                        color: AppTheme.primary,
                        size: 48,
                      ),
                    ),
                  ),
                ),
              ),
            ),
            const SizedBox(height: 20),

            // App Name
            RichText(
              text: const TextSpan(
                style: TextStyle(
                  fontSize: 26,
                  fontWeight: FontWeight.w800,
                  color: Colors.white,
                  letterSpacing: -0.5,
                ),
                children: [
                  TextSpan(text: 'Nitip'),
                  TextSpan(
                    text: 'Dong',
                    style: TextStyle(color: AppTheme.primary, fontWeight: FontWeight.w900),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 6),
            const Text(
              'Official Store & Marketplace Terpercaya',
              style: TextStyle(
                color: Colors.white60,
                fontSize: 11,
                fontWeight: FontWeight.w500,
                letterSpacing: 0.5,
              ),
            ),
            const SizedBox(height: 40),

            // Loading Spinner
            const CircularProgressIndicator(
              strokeWidth: 2.5,
              valueColor: AlwaysStoppedAnimation<Color>(AppTheme.primary),
            ),
          ],
        ),
      ),
    );
  }
}
