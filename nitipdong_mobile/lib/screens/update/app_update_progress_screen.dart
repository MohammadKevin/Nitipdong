import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../theme/app_theme.dart';
import '../../services/api_service.dart';

class AppUpdateProgressScreen extends StatefulWidget {
  final String newVersion;
  final String downloadUrl;
  final bool isForceUpdate;

  const AppUpdateProgressScreen({
    Key? key,
    required this.newVersion,
    required this.downloadUrl,
    this.isForceUpdate = false,
  }) : super(key: key);

  @override
  State<AppUpdateProgressScreen> createState() => _AppUpdateProgressScreenState();
}

class _AppUpdateProgressScreenState extends State<AppUpdateProgressScreen> with SingleTickerProviderStateMixin {
  bool _isDownloading = false;
  late AnimationController _pulseController;

  @override
  void initState() {
    super.initState();
    _pulseController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1500),
    )..repeat(reverse: true);
  }

  @override
  void dispose() {
    _pulseController.dispose();
    super.dispose();
  }

  Future<void> _startDownload() async {
    setState(() => _isDownloading = true);
    try {
      final uri = Uri.parse(widget.downloadUrl);
      if (await canLaunchUrl(uri)) {
        await launchUrl(uri, mode: LaunchMode.externalApplication);
      } else {
        final fallbackUri = Uri.parse('https://budayakita.com/download/app');
        await launchUrl(fallbackUri, mode: LaunchMode.externalApplication);
      }
    } catch (_) {
      final fallbackUri = Uri.parse('https://budayakita.com/apps');
      await launchUrl(fallbackUri, mode: LaunchMode.externalApplication);
    }
  }

  Future<void> _openWebLanding() async {
    try {
      final uri = Uri.parse('https://budayakita.com/apps');
      if (await canLaunchUrl(uri)) {
        await launchUrl(uri, mode: LaunchMode.externalApplication);
      }
    } catch (_) {}
  }

  @override
  Widget build(BuildContext context) {
    final currentVer = ApiService.currentAppVersion;

    return PopScope(
      canPop: !widget.isForceUpdate,
      child: Scaffold(
        backgroundColor: const Color(0xFF0B1528),
        appBar: AppBar(
          backgroundColor: Colors.transparent,
          elevation: 0,
          leading: widget.isForceUpdate
              ? IconButton(
                  icon: const Icon(Icons.power_settings_new_rounded, color: Colors.redAccent, size: 22),
                  tooltip: 'Keluar Aplikasi',
                  onPressed: () => SystemNavigator.pop(),
                )
              : IconButton(
                  icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white70, size: 20),
                  onPressed: () => Navigator.pop(context),
                ),
          title: Text(
            widget.isForceUpdate ? 'Pembaruan Wajib Sistem 🔒' : 'Pusat Pembaruan Sistem',
            style: const TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.w700),
          ),
          centerTitle: true,
        ),
        body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
          child: Column(
            children: [
              const SizedBox(height: 10),

              // Animated Rocket Icon in pulsing container
              Center(
                child: Stack(
                  alignment: Alignment.center,
                  children: [
                    AnimatedBuilder(
                      animation: _pulseController,
                      builder: (context, child) {
                        return Container(
                          width: 120 + (_pulseController.value * 16),
                          height: 120 + (_pulseController.value * 16),
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: const Color(0xFF06B6D4).withOpacity(0.08 * (1 - _pulseController.value)),
                          ),
                        );
                      },
                    ),
                    Container(
                      width: 90,
                      height: 90,
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(
                          colors: [Color(0xFF0E7490), Color(0xFF06B6D4)],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                        shape: BoxShape.circle,
                        boxShadow: [
                          BoxShadow(
                            color: const Color(0xFF06B6D4).withOpacity(0.4),
                            blurRadius: 25,
                            offset: const Offset(0, 8),
                          ),
                        ],
                      ),
                      child: const Icon(
                        Icons.rocket_launch_rounded,
                        color: Colors.white,
                        size: 42,
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 24),

              // Title
              Text(
                'Pembaruan NitipDong v${widget.newVersion} 🎉',
                textAlign: TextAlign.center,
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 20,
                  fontWeight: FontWeight.w800,
                  letterSpacing: -0.5,
                ),
              ),
              const SizedBox(height: 6),
              Text(
                'Versi saat ini: v$currentVer ➔ Tersedia: v${widget.newVersion}',
                textAlign: TextAlign.center,
                style: const TextStyle(color: Colors.white60, fontSize: 12),
              ),

              const SizedBox(height: 20),

              // Changelog Box
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.05),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: Colors.white.withOpacity(0.08)),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Row(
                      children: [
                        Icon(Icons.auto_awesome_rounded, color: Colors.amberAccent, size: 16),
                        SizedBox(width: 8),
                        Text(
                          'Peningkatan & Fitur Baru:',
                          style: TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.w800),
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),
                    _buildChangelogItem('🚚 Modul Driver & Live GPS Route Tracking Mitra Kurir'),
                    _buildChangelogItem('💳 Sinkronisasi Pembayaran QRIS & Virtual Account Midtrans'),
                    _buildChangelogItem('⚡ Fast-Track Checkout Instant Tanpa Keranjang'),
                    _buildChangelogItem('🛡️ Peningkatan Keamanan & Optimalisasi Performa Aplikasi'),
                  ],
                ),
              ),

              const SizedBox(height: 16),

              // 3 Easy Steps Guide
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: const Color(0xFF0F172A),
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: Colors.cyan.withOpacity(0.2)),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      '📌 3 Langkah Mudah Memasang:',
                      style: TextStyle(color: Colors.cyanAccent, fontSize: 12, fontWeight: FontWeight.w800),
                    ),
                    const SizedBox(height: 8),
                    _buildStepItem('1', 'Tekan tombol "Unduh Pembaruan Sekarang" di bawah.'),
                    _buildStepItem('2', 'Tarik bilah notifikasi di atas layar HP saat unduhan selesai.'),
                    _buildStepItem('3', 'Tekan file unduhan APK untuk langsung memasang (Update).'),
                  ],
                ),
              ),

              const Spacer(),

              // Action Buttons
              SizedBox(
                width: double.infinity,
                height: 52,
                child: ElevatedButton.icon(
                  icon: Icon(_isDownloading ? Icons.cloud_download_rounded : Icons.download_rounded, size: 22),
                  label: Text(
                    _isDownloading ? 'Sedang Mengunduh... (Buka Notifikasi HP) 📥' : 'Unduh Pembaruan Sekarang 🚀',
                    style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w800),
                  ),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF06B6D4),
                    foregroundColor: Colors.white,
                    elevation: 4,
                    shadowColor: const Color(0xFF06B6D4).withOpacity(0.4),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                  ),
                  onPressed: _startDownload,
                ),
              ),
              const SizedBox(height: 10),

              // Alternative Web Download Hub Link
              Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  TextButton.icon(
                    icon: const Icon(Icons.language_rounded, size: 16, color: Colors.white54),
                    label: const Text(
                      'Buka Halaman Download Web (Apps Hub)',
                      style: TextStyle(color: Colors.white54, fontSize: 12),
                    ),
                    onPressed: _openWebLanding,
                  ),
                ],
              ),
              const SizedBox(height: 6),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildChangelogItem(String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 6),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('✨ ', style: TextStyle(fontSize: 11)),
          Expanded(
            child: Text(
              text,
              style: const TextStyle(color: Colors.white70, fontSize: 12, height: 1.3),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStepItem(String number, String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 4),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 16,
            height: 16,
            alignment: Alignment.center,
            decoration: BoxDecoration(
              color: Colors.cyan.withOpacity(0.2),
              shape: BoxShape.circle,
            ),
            child: Text(
              number,
              style: const TextStyle(color: Colors.cyanAccent, fontSize: 10, fontWeight: FontWeight.w900),
            ),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: Text(
              text,
              style: const TextStyle(color: Colors.white60, fontSize: 11, height: 1.3),
            ),
          ),
        ],
      ),
    );
  }
}
