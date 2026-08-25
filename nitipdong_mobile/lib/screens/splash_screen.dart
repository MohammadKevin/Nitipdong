import 'dart:async';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../theme/app_theme.dart';
import '../providers/auth_provider.dart';
import '../services/api_service.dart';
import '../screens/main_nav_screen.dart';
import '../screens/maintenance_screen.dart';
import '../screens/update/app_update_progress_screen.dart';
import '../screens/auth/login_screen.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({Key? key}) : super(key: key);

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> with SingleTickerProviderStateMixin {
  late AnimationController _animController;
  late Animation<double> _scaleAnimation;
  late Animation<double> _fadeAnimation;
  late Animation<double> _glowAnimation;

  @override
  void initState() {
    super.initState();

    _animController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1400),
    )..repeat(reverse: true);

    _scaleAnimation = Tween<double>(begin: 0.95, end: 1.03).animate(
      CurvedAnimation(parent: _animController, curve: Curves.easeInOut),
    );

    _fadeAnimation = Tween<double>(begin: 0.7, end: 1.0).animate(
      CurvedAnimation(parent: _animController, curve: Curves.easeInOut),
    );

    _glowAnimation = Tween<double>(begin: 18.0, end: 32.0).animate(
      CurvedAnimation(parent: _animController, curve: Curves.easeInOut),
    );

    _initApp();
  }

  @override
  void dispose() {
    _animController.dispose();
    super.dispose();
  }

  Future<void> _initApp() async {
    try {
      final results = await Future.wait([
        ApiService.checkSystemStatus(),
        Provider.of<AuthProvider>(context, listen: false).checkAuth(),
        Future.delayed(const Duration(milliseconds: 1200)),
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

      // KAI-Style Version Gate (Mandatory Force Update Check)
      final String minVersion = systemStatus['min_version']?.toString() ?? '1.0.0';
      final String latestVersion = systemStatus['latest_version']?.toString() ?? ApiService.currentAppVersion;
      final String downloadUrl = systemStatus['update_url']?.toString() ?? 'https://budayakita.com/download/app';

      if (ApiService.isVersionLower(ApiService.currentAppVersion, minVersion)) {
        if (!mounted) return;
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(
            builder: (context) => AppUpdateProgressScreen(
              newVersion: latestVersion,
              downloadUrl: downloadUrl,
              isForceUpdate: true,
            ),
          ),
        );
        return;
      }
    } catch (_) {}

    if (!mounted) return;

    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    if (authProvider.isAuthenticated) {
      Navigator.pushReplacement(
        context,
        PageRouteBuilder(
          pageBuilder: (_, __, ___) => const MainNavScreen(),
          transitionsBuilder: (_, a, __, c) => FadeTransition(opacity: a, child: c),
          transitionDuration: const Duration(milliseconds: 400),
        ),
      );
    } else {
      Navigator.pushReplacement(
        context,
        PageRouteBuilder(
          pageBuilder: (_, __, ___) => const LoginScreen(isFromSplash: true),
          transitionsBuilder: (_, a, __, c) => FadeTransition(opacity: a, child: c),
          transitionDuration: const Duration(milliseconds: 400),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF070E1E),
      body: Stack(
        children: [
          // Background ambient gradient orb 1 (Top Right Cyan)
          Positioned(
            top: -60,
            right: -60,
            child: Container(
              width: 240,
              height: 240,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: const Color(0xFF06B6D4).withOpacity(0.12),
              ),
            ),
          ),

          // Background ambient gradient orb 2 (Bottom Left Orange)
          Positioned(
            bottom: -80,
            left: -80,
            child: Container(
              width: 260,
              height: 260,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: const Color(0xFFF97316).withOpacity(0.10),
              ),
            ),
          ),

          // Main Center Content
          Center(
            child: AnimatedBuilder(
              animation: _animController,
              builder: (context, child) {
                return Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    // Glowing Icon Wrapper
                    Transform.scale(
                      scale: _scaleAnimation.value,
                      child: Container(
                        width: 104,
                        height: 104,
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(26),
                          boxShadow: [
                            BoxShadow(
                              color: const Color(0xFF06B6D4).withOpacity(0.35 * _fadeAnimation.value),
                              blurRadius: _glowAnimation.value,
                              spreadRadius: 2,
                              offset: const Offset(0, 8),
                            ),
                            BoxShadow(
                              color: const Color(0xFFF97316).withOpacity(0.20 * _fadeAnimation.value),
                              blurRadius: _glowAnimation.value * 0.8,
                              offset: const Offset(4, 12),
                            ),
                          ],
                        ),
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(26),
                          child: Image.asset(
                            'assets/icon/app_icon.png',
                            width: 104,
                            height: 104,
                            fit: BoxFit.cover,
                            errorBuilder: (_, __, ___) => Container(
                              color: const Color(0xFF0F172A),
                              child: const Center(
                                child: Icon(Icons.shopping_bag_rounded, color: Color(0xFF06B6D4), size: 48),
                              ),
                            ),
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(height: 24),

                    // App Title with Dual-tone Gradient
                    RichText(
                      text: const TextSpan(
                        style: TextStyle(
                          fontSize: 28,
                          fontWeight: FontWeight.w900,
                          color: Colors.white,
                          letterSpacing: -0.5,
                        ),
                        children: [
                          TextSpan(text: 'Nitip'),
                          TextSpan(
                            text: 'Dong',
                            style: TextStyle(
                              color: Color(0xFF06B6D4),
                              fontWeight: FontWeight.w900,
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 6),

                    // Tagline
                    const Text(
                      'Marketplace Jastip Terpercaya & Tercepat',
                      style: TextStyle(
                        color: Colors.white70,
                        fontSize: 12,
                        fontWeight: FontWeight.w500,
                        letterSpacing: 0.3,
                      ),
                    ),
                    const SizedBox(height: 14),

                    // Version Pill Badge
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.08),
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: Colors.white.withOpacity(0.12)),
                      ),
                      child: const Text(
                        'v5.0.0 Official Release 🚀',
                        style: TextStyle(
                          color: Color(0xFF38BDF8),
                          fontSize: 10.5,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                    ),
                    const SizedBox(height: 48),

                    // Modern Pulse Loading Indicator
                    SizedBox(
                      width: 140,
                      child: ClipRRect(
                        borderRadius: BorderRadius.circular(10),
                        child: const LinearProgressIndicator(
                          minHeight: 3.5,
                          backgroundColor: Color(0xFF1E293B),
                          valueColor: AlwaysStoppedAnimation<Color>(Color(0xFF06B6D4)),
                        ),
                      ),
                    ),
                  ],
                );
              },
            ),
          ),

          // Bottom copyright info
          Positioned(
            bottom: 24,
            left: 0,
            right: 0,
            child: Center(
              child: Text(
                '© 2026 NitipDong Inc. All rights reserved.',
                style: TextStyle(
                  color: Colors.white.withOpacity(0.35),
                  fontSize: 10.5,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
