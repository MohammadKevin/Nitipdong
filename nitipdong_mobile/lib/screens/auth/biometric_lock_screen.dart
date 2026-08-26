import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:local_auth/local_auth.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';
import '../../services/api_service.dart';
import '../../screens/main_nav_screen.dart';
import '../../screens/auth/login_screen.dart';

class BiometricLockScreen extends StatefulWidget {
  const BiometricLockScreen({Key? key}) : super(key: key);

  @override
  State<BiometricLockScreen> createState() => _BiometricLockScreenState();
}

class _BiometricLockScreenState extends State<BiometricLockScreen> with SingleTickerProviderStateMixin {
  final LocalAuthentication _localAuth = LocalAuthentication();
  late AnimationController _pulseController;
  late Animation<double> _scaleAnimation;
  late Animation<double> _glowAnimation;

  String _currentMode = 'fingerprint'; // 'fingerprint' or 'face'
  bool _isAuthenticating = false;
  String _statusMessage = 'Menyiapkan sensor keamanan...';
  bool _hasError = false;

  @override
  void initState() {
    super.initState();

    _pulseController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1600),
    )..repeat(reverse: true);

    _scaleAnimation = Tween<double>(begin: 0.95, end: 1.05).animate(
      CurvedAnimation(parent: _pulseController, curve: Curves.easeInOut),
    );

    _glowAnimation = Tween<double>(begin: 8.0, end: 24.0).animate(
      CurvedAnimation(parent: _pulseController, curve: Curves.easeInOut),
    );

    _initSecurityMode();
  }

  Future<void> _initSecurityMode() async {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    String preferredType = authProvider.user?.biometricType ?? await ApiService.getBiometricTypeLocally();
    
    if (preferredType == 'face') {
      _currentMode = 'face';
    } else {
      _currentMode = 'fingerprint';
    }

    if (mounted) {
      setState(() {
        _statusMessage = _currentMode == 'face'
            ? 'Arahkan kamera ke wajah Anda untuk membuka aplikasi'
            : 'Sentuh sensor sidik jari untuk membuka aplikasi';
      });
    }

    // Auto-trigger authentication
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _authenticate();
    });
  }

  @override
  void dispose() {
    _pulseController.dispose();
    super.dispose();
  }

  Future<void> _authenticate() async {
    if (_isAuthenticating) return;

    setState(() {
      _isAuthenticating = true;
      _hasError = false;
      _statusMessage = _currentMode == 'face' ? 'Memindai wajah...' : 'Memindai sidik jari...';
    });

    try {
      final bool canCheck = await _localAuth.canCheckBiometrics;
      final bool isDeviceSupported = await _localAuth.isDeviceSupported();

      if (!canCheck && !isDeviceSupported) {
        // Jika perangkat tidak mendukung biometrik, langsung buka aplikasi
        _proceedToApp();
        return;
      }

      final String localizedReason = _currentMode == 'face'
          ? 'Pindai wajah Anda untuk membuka aplikasi NitipDong'
          : 'Pindai sidik jari Anda untuk membuka aplikasi NitipDong';

      final bool didAuthenticate = await _localAuth.authenticate(
        localizedReason: localizedReason,
        options: const AuthenticationOptions(
          biometricOnly: false,
          stickyAuth: true,
          useErrorDialogs: true,
        ),
      );

      if (didAuthenticate) {
        setState(() {
          _statusMessage = 'Verifikasi Berhasil! Membuka aplikasi...';
        });
        await Future.delayed(const Duration(milliseconds: 300));
        _proceedToApp();
      } else {
        setState(() {
          _hasError = true;
          _statusMessage = 'Autentikasi dibatalkan atau tidak cocok. Silakan coba lagi.';
        });
      }
    } on PlatformException catch (e) {
      setState(() {
        _hasError = true;
        _statusMessage = 'Kendala biometrik: ${e.message ?? 'Gagal memverifikasi'}';
      });
    } catch (e) {
      setState(() {
        _hasError = true;
        _statusMessage = 'Gagal memverifikasi. Silakan coba lagi.';
      });
    } finally {
      if (mounted) {
        setState(() {
          _isAuthenticating = false;
        });
      }
    }
  }

  void _switchMode(String newMode) {
    if (_currentMode == newMode) return;
    setState(() {
      _currentMode = newMode;
      _hasError = false;
      _statusMessage = newMode == 'face'
          ? 'Mode Pindai Wajah aktif. Ketuk untuk memindai.'
          : 'Mode Sidik Jari aktif. Ketuk sensor untuk memindai.';
    });
    _authenticate();
  }

  void _proceedToApp() {
    if (!mounted) return;
    final authProvider = Provider.of<AuthProvider>(context, listen: false);

    if (authProvider.isAuthenticated) {
      Navigator.pushReplacement(
        context,
        PageRouteBuilder(
          pageBuilder: (_, __, ___) => const MainNavScreen(),
          transitionsBuilder: (_, a, __, c) => FadeTransition(opacity: a, child: c),
          transitionDuration: const Duration(milliseconds: 350),
        ),
      );
    } else {
      Navigator.pushReplacement(
        context,
        PageRouteBuilder(
          pageBuilder: (_, __, ___) => const LoginScreen(isFromSplash: true),
          transitionsBuilder: (_, a, __, c) => FadeTransition(opacity: a, child: c),
          transitionDuration: const Duration(milliseconds: 350),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final bool isFace = _currentMode == 'face';

    return Scaffold(
      backgroundColor: const Color(0xFF070E1E),
      body: Stack(
        children: [
          // Background ambient light
          Positioned(
            top: -80,
            right: -80,
            child: Container(
              width: 280,
              height: 280,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: (isFace ? const Color(0xFF8B5CF6) : const Color(0xFF06B6D4)).withOpacity(0.12),
              ),
            ),
          ),
          Positioned(
            bottom: -80,
            left: -80,
            child: Container(
              width: 280,
              height: 280,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: const Color(0xFF2563EB).withOpacity(0.10),
              ),
            ),
          ),

          SafeArea(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 28.0, vertical: 24.0),
              child: Column(
                children: [
                  const Spacer(),

                  // App Logo Badge
                  Container(
                    width: 72,
                    height: 72,
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20),
                      boxShadow: [
                        BoxShadow(
                          color: (isFace ? const Color(0xFF8B5CF6) : const Color(0xFF06B6D4)).withOpacity(0.3),
                          blurRadius: 20,
                          offset: const Offset(0, 8),
                        ),
                      ],
                    ),
                    child: Image.asset(
                      'assets/icon/app_icon.png',
                      fit: BoxFit.contain,
                      errorBuilder: (_, __, ___) => Icon(
                        isFace ? Icons.face_retouching_natural_rounded : Icons.fingerprint_rounded,
                        color: const Color(0xFF0891B2),
                        size: 32,
                      ),
                    ),
                  ),

                  const SizedBox(height: 24),

                  Text(
                    isFace ? 'Kunci Pindai Wajah' : 'Kunci Sidik Jari',
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 22,
                      fontWeight: FontWeight.w800,
                      letterSpacing: -0.5,
                    ),
                  ),

                  const SizedBox(height: 8),

                  Text(
                    _statusMessage,
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      color: _hasError ? const Color(0xFFF87171) : const Color(0xFF94A3B8),
                      fontSize: 13,
                      height: 1.4,
                      fontWeight: _hasError ? FontWeight.w600 : FontWeight.w400,
                    ),
                  ),

                  const SizedBox(height: 36),

                  // Animated Sensor Button (Fingerprint or Face)
                  GestureDetector(
                    onTap: _authenticate,
                    child: AnimatedBuilder(
                      animation: _pulseController,
                      builder: (context, child) {
                        return Transform.scale(
                          scale: _scaleAnimation.value,
                          child: Container(
                            width: 108,
                            height: 108,
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              gradient: LinearGradient(
                                colors: isFace
                                    ? [const Color(0xFF8B5CF6), const Color(0xFF3B82F6)]
                                    : [const Color(0xFF06B6D4), const Color(0xFF2563EB)],
                                begin: Alignment.topLeft,
                                end: Alignment.bottomRight,
                              ),
                              boxShadow: [
                                BoxShadow(
                                  color: (isFace ? const Color(0xFF8B5CF6) : const Color(0xFF06B6D4)).withOpacity(0.4),
                                  blurRadius: _glowAnimation.value,
                                  spreadRadius: 2,
                                ),
                              ],
                            ),
                            child: Center(
                              child: _isAuthenticating
                                  ? const SizedBox(
                                      width: 36,
                                      height: 36,
                                      child: CircularProgressIndicator(
                                        color: Colors.white,
                                        strokeWidth: 3,
                                      ),
                                    )
                                  : Icon(
                                      isFace ? Icons.face_unlock_rounded : Icons.fingerprint_rounded,
                                      color: Colors.white,
                                      size: 56,
                                    ),
                            ),
                          ),
                        );
                      },
                    ),
                  ),

                  const SizedBox(height: 20),

                  const Text(
                    'Ketuk untuk Pindai Ulang',
                    style: TextStyle(
                      color: Color(0xFF64748B),
                      fontSize: 12,
                      fontWeight: FontWeight.w500,
                    ),
                  ),

                  const SizedBox(height: 28),

                  // Mode Switcher Pills (Pilihan: Sidik Jari vs Scan Wajah)
                  Container(
                    padding: const EdgeInsets.all(4),
                    decoration: BoxDecoration(
                      color: const Color(0xFF0F172A),
                      borderRadius: BorderRadius.circular(24),
                      border: Border.all(color: const Color(0xFF1E293B)),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        InkWell(
                          onTap: () => _switchMode('fingerprint'),
                          borderRadius: BorderRadius.circular(20),
                          child: Container(
                            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                            decoration: BoxDecoration(
                              color: !isFace ? const Color(0xFF06B6D4) : Colors.transparent,
                              borderRadius: BorderRadius.circular(20),
                            ),
                            child: Row(
                              children: [
                                Icon(
                                  Icons.fingerprint_rounded,
                                  size: 16,
                                  color: !isFace ? const Color(0xFF070E1E) : const Color(0xFF94A3B8),
                                ),
                                const SizedBox(width: 6),
                                Text(
                                  'Sidik Jari',
                                  style: TextStyle(
                                    fontSize: 12,
                                    fontWeight: !isFace ? FontWeight.w800 : FontWeight.w600,
                                    color: !isFace ? const Color(0xFF070E1E) : const Color(0xFF94A3B8),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                        InkWell(
                          onTap: () => _switchMode('face'),
                          borderRadius: BorderRadius.circular(20),
                          child: Container(
                            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                            decoration: BoxDecoration(
                              color: isFace ? const Color(0xFF8B5CF6) : Colors.transparent,
                              borderRadius: BorderRadius.circular(20),
                            ),
                            child: Row(
                              children: [
                                Icon(
                                  Icons.face_unlock_rounded,
                                  size: 16,
                                  color: isFace ? Colors.white : const Color(0xFF94A3B8),
                                ),
                                const SizedBox(width: 6),
                                Text(
                                  'Scan Wajah',
                                  style: TextStyle(
                                    fontSize: 12,
                                    fontWeight: isFace ? FontWeight.w800 : FontWeight.w600,
                                    color: isFace ? Colors.white : const Color(0xFF94A3B8),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),

                  const Spacer(),

                  // Fallback to Password/PIN
                  SizedBox(
                    width: double.infinity,
                    height: 48,
                    child: OutlinedButton.icon(
                      onPressed: _proceedToApp,
                      icon: const Icon(Icons.lock_open_rounded, size: 18, color: Color(0xFFCBD5E1)),
                      label: const Text(
                        'Buka dengan Password / PIN Akun',
                        style: TextStyle(
                          color: Color(0xFFCBD5E1),
                          fontSize: 13,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                      style: OutlinedButton.styleFrom(
                        side: const BorderSide(color: Color(0xFF334155)),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(14),
                        ),
                      ),
                    ),
                  ),

                  const SizedBox(height: 12),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}
