import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../theme/app_theme.dart';
import '../services/api_service.dart';
import 'main_nav_screen.dart';

class MaintenanceScreen extends StatefulWidget {
  final String title;
  final String message;

  const MaintenanceScreen({
    Key? key,
    this.title = 'Mode Pemeliharaan & Peningkatan Sistem 🛠️',
    this.message = 'Aplikasi NitipDong sedang dalam tahap pembaruan fitur & optimalisasi performa server untuk pengalaman belanja yang lebih lancar. Silakan coba kembali beberapa saat lagi.',
  }) : super(key: key);

  @override
  State<MaintenanceScreen> createState() => _MaintenanceScreenState();
}

class _MaintenanceScreenState extends State<MaintenanceScreen> with SingleTickerProviderStateMixin {
  bool _isChecking = false;
  late String _currentTitle;
  late String _currentMessage;
  late AnimationController _animController;
  late Animation<double> _pulseAnimation;

  @override
  void initState() {
    super.initState();
    _currentTitle = widget.title;
    _currentMessage = widget.message;

    _animController = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 2),
    )..repeat(reverse: true);

    _pulseAnimation = Tween<double>(begin: 0.95, end: 1.05).animate(
      CurvedAnimation(parent: _animController, curve: Curves.easeInOut),
    );
  }

  @override
  void dispose() {
    _animController.dispose();
    super.dispose();
  }

  Future<void> _recheckStatus() async {
    setState(() => _isChecking = true);
    final status = await ApiService.checkSystemStatus();
    setState(() {
      _isChecking = false;
      if (status['is_maintenance'] == true) {
        if (status['maintenance_title'] != null && status['maintenance_title'].toString().isNotEmpty) {
          _currentTitle = status['maintenance_title'];
        }
        if (status['maintenance_message'] != null && status['maintenance_message'].toString().isNotEmpty) {
          _currentMessage = status['maintenance_message'];
        }
      }
    });

    if (status['is_maintenance'] == false && mounted) {
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => const MainNavScreen()),
      );
    } else if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: const Row(
            children: [
              Icon(Icons.info_outline_rounded, color: Colors.white, size: 18),
              SizedBox(width: 8),
              Expanded(
                child: Text(
                  'Sistem masih dalam tahap pemeliharaan. Mohon tunggu beberapa saat lagi.',
                  style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600),
                ),
              ),
            ],
          ),
          backgroundColor: AppTheme.accentOrange,
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          margin: const EdgeInsets.all(16),
          duration: const Duration(seconds: 3),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      body: Stack(
        children: [
          // Background ambient soft glow circles
          Positioned(
            top: -60,
            right: -60,
            child: Container(
              width: 240,
              height: 240,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: const Color(0xFF0891B2).withOpacity(0.06),
              ),
            ),
          ),
          Positioned(
            bottom: -80,
            left: -80,
            child: Container(
              width: 260,
              height: 260,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: const Color(0xFFEA580C).withOpacity(0.04),
              ),
            ),
          ),

          SafeArea(
            child: Center(
              child: SingleChildScrollView(
                physics: const BouncingScrollPhysics(),
                padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 24),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    // Brand Header
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Container(
                          width: 32,
                          height: 32,
                          decoration: BoxDecoration(
                            gradient: const LinearGradient(
                              colors: [Color(0xFF0891B2), Color(0xFF0E7490)],
                            ),
                            borderRadius: BorderRadius.circular(10),
                            boxShadow: [
                              BoxShadow(
                                color: const Color(0xFF0891B2).withOpacity(0.3),
                                blurRadius: 8,
                                offset: const Offset(0, 3),
                              ),
                            ],
                          ),
                          child: const Icon(Icons.shopping_bag_rounded, color: Colors.white, size: 18),
                        ),
                        const SizedBox(width: 8),
                        Text(
                          'NitipDong',
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 20,
                            fontWeight: FontWeight.w900,
                            color: const Color(0xFF0F172A),
                            letterSpacing: -0.5,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 28),

                    // Hero Animated Construction Card
                    ScaleTransition(
                      scale: _pulseAnimation,
                      child: Container(
                        width: 104,
                        height: 104,
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(30),
                          border: Border.all(color: const Color(0xFFCFFAFE), width: 2),
                          boxShadow: [
                            BoxShadow(
                              color: const Color(0xFF0891B2).withOpacity(0.12),
                              blurRadius: 28,
                              offset: const Offset(0, 10),
                            ),
                          ],
                        ),
                        child: Center(
                          child: Container(
                            width: 68,
                            height: 68,
                            decoration: BoxDecoration(
                              gradient: const LinearGradient(
                                colors: [Color(0xFFECFEFF), Color(0xFFCFFAFE)],
                                begin: Alignment.topLeft,
                                end: Alignment.bottomRight,
                              ),
                              borderRadius: BorderRadius.circular(20),
                            ),
                            child: const Center(
                              child: Icon(
                                Icons.construction_rounded,
                                color: Color(0xFF0891B2),
                                size: 36,
                              ),
                            ),
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(height: 24),

                    // Status Badge Pill
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                      decoration: BoxDecoration(
                        color: const Color(0xFFFFF7ED),
                        borderRadius: BorderRadius.circular(24),
                        border: Border.all(color: const Color(0xFFFED7AA)),
                        boxShadow: [
                          BoxShadow(
                            color: const Color(0xFFEA580C).withOpacity(0.06),
                            blurRadius: 8,
                            offset: const Offset(0, 2),
                          ),
                        ],
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Container(
                            width: 8,
                            height: 8,
                            decoration: const BoxDecoration(
                              shape: BoxShape.circle,
                              color: Color(0xFFEA580C),
                            ),
                          ),
                          const SizedBox(width: 6),
                          Text(
                            'PEMELIHARAAN SISTEM',
                            style: GoogleFonts.plusJakartaSans(
                              color: const Color(0xFFC2410C),
                              fontSize: 10.5,
                              fontWeight: FontWeight.w900,
                              letterSpacing: 0.6,
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Title
                    Text(
                      _currentTitle,
                      textAlign: TextAlign.center,
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 20,
                        fontWeight: FontWeight.w800,
                        color: const Color(0xFF0F172A),
                        letterSpacing: -0.5,
                        height: 1.3,
                      ),
                    ),
                    const SizedBox(height: 14),

                    // Message Card (Clean Light Design)
                    Container(
                      padding: const EdgeInsets.all(18),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: const Color(0xFFE2E8F0)),
                        boxShadow: [
                          BoxShadow(
                            color: const Color(0xFF0F172A).withOpacity(0.04),
                            blurRadius: 16,
                            offset: const Offset(0, 4),
                          ),
                        ],
                      ),
                      child: Text(
                        _currentMessage,
                        textAlign: TextAlign.center,
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 13,
                          color: const Color(0xFF475569),
                          height: 1.6,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ),
                    const SizedBox(height: 18),

                    // Feature / Improvement Highlight Card
                    Container(
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(
                        color: const Color(0xFFF1F5F9).withOpacity(0.8),
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: const Color(0xFFE2E8F0)),
                      ),
                      child: Column(
                        children: [
                          _buildHighlightItem(
                            icon: Icons.speed_rounded,
                            title: 'Peningkatan Kecepatan & Respons Server',
                            iconColor: const Color(0xFF0891B2),
                          ),
                          const Divider(height: 16, color: Color(0xFFE2E8F0)),
                          _buildHighlightItem(
                            icon: Icons.security_rounded,
                            title: 'Optimalisasi Keamanan Transaksi & Akun',
                            iconColor: const Color(0xFF059669),
                          ),
                          const Divider(height: 16, color: Color(0xFFE2E8F0)),
                          _buildHighlightItem(
                            icon: Icons.local_offer_rounded,
                            title: 'Sinkronisasi Promo & Fitur Belanja Terbaru',
                            iconColor: const Color(0xFFEA580C),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 28),

                    // Primary Action Button (Muat Ulang)
                    SizedBox(
                      width: double.infinity,
                      height: 50,
                      child: ElevatedButton(
                        onPressed: _isChecking ? null : _recheckStatus,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFF0891B2),
                          foregroundColor: Colors.white,
                          elevation: 0,
                          shadowColor: const Color(0xFF0891B2).withOpacity(0.3),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                          ),
                          padding: const EdgeInsets.symmetric(horizontal: 20),
                        ),
                        child: _isChecking
                            ? const SizedBox(
                                width: 22,
                                height: 22,
                                child: CircularProgressIndicator(
                                  strokeWidth: 2.5,
                                  color: Colors.white,
                                ),
                              )
                            : Row(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  const Icon(Icons.refresh_rounded, size: 20),
                                  const SizedBox(width: 8),
                                  Text(
                                    'Coba Muat Ulang Aplikasi',
                                    style: GoogleFonts.plusJakartaSans(
                                      fontWeight: FontWeight.w800,
                                      fontSize: 14,
                                      letterSpacing: -0.2,
                                    ),
                                  ),
                                ],
                              ),
                      ),
                    ),
                    const SizedBox(height: 16),

                    // App Version Footer
                    Text(
                      'NitipDong Mobile • Versi ${ApiService.currentAppVersion}',
                      style: GoogleFonts.plusJakartaSans(
                        color: const Color(0xFF94A3B8),
                        fontSize: 11.5,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildHighlightItem({
    required IconData icon,
    required String title,
    required Color iconColor,
  }) {
    return Row(
      children: [
        Container(
          width: 28,
          height: 28,
          decoration: BoxDecoration(
            color: iconColor.withOpacity(0.12),
            borderRadius: BorderRadius.circular(8),
          ),
          child: Icon(icon, size: 16, color: iconColor),
        ),
        const SizedBox(width: 10),
        Expanded(
          child: Text(
            title,
            style: GoogleFonts.plusJakartaSans(
              fontSize: 12,
              fontWeight: FontWeight.w600,
              color: const Color(0xFF334155),
            ),
          ),
        ),
        const Icon(Icons.check_circle_rounded, size: 16, color: Color(0xFF059669)),
      ],
    );
  }
}
