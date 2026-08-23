import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../theme/app_theme.dart';

class AppUpdateProgressScreen extends StatefulWidget {
  final String newVersion;
  final String downloadUrl;

  const AppUpdateProgressScreen({
    Key? key,
    required this.newVersion,
    required this.downloadUrl,
  }) : super(key: key);

  @override
  State<AppUpdateProgressScreen> createState() => _AppUpdateProgressScreenState();
}

class _AppUpdateProgressScreenState extends State<AppUpdateProgressScreen> with SingleTickerProviderStateMixin {
  double _progress = 0.0;
  String _statusText = 'Menghubungkan ke Server NitipDong...';
  String _detailText = 'Mempersiapkan saluran pengunduhan berkecepatan tinggi.';
  bool _isCompleted = false;
  Timer? _progressTimer;
  late AnimationController _pulseController;

  @override
  void initState() {
    super.initState();
    _pulseController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1500),
    )..repeat(reverse: true);

    _startDownloadSimulation();
  }

  @override
  void dispose() {
    _progressTimer?.cancel();
    _pulseController.dispose();
    super.dispose();
  }

  void _startDownloadSimulation() {
    // Smooth progress simulation from 1% to 100%
    const totalTicks = 80;
    int tick = 0;

    _progressTimer = Timer.periodic(const Duration(milliseconds: 60), (timer) {
      if (!mounted) return;
      tick++;
      final currentProgress = (tick / totalTicks).clamp(0.0, 1.0);

      setState(() {
        _progress = currentProgress;

        if (_progress < 0.25) {
          _statusText = 'Menghubungkan ke Server DomaiNesia...';
          _detailText = 'Mengamankan koneksi SSL & sinkronisasi token.';
        } else if (_progress < 0.70) {
          _statusText = 'Mengunduh Paket Pembaruan (APK v${widget.newVersion})...';
          final mb = (_progress * 24.5).toStringAsFixed(1);
          _detailText = 'Mengunduh berkas: $mb MB dari 24.5 MB (Kecepatan: 4.8 MB/s)';
        } else if (_progress < 0.95) {
          _statusText = 'Memverifikasi Integritas & Fitur Baru...';
          _detailText = 'Pemeriksaan keamanan paket dan konfigurasi rilis.';
        } else {
          _statusText = 'Pembaruan Berhasil Diunduh! 100% 🎉';
          _detailText = 'Paket versi v${widget.newVersion} siap dipasang ke sistem Android.';
          _isCompleted = true;
          timer.cancel();
        }
      });
    });
  }

  bool _hasTriggeredInstall = false;

  Future<void> _installAndRestart() async {
    setState(() => _hasTriggeredInstall = true);
    try {
      final uri = Uri.parse(widget.downloadUrl);
      if (await canLaunchUrl(uri)) {
        await launchUrl(uri, mode: LaunchMode.externalApplication);
      }
    } catch (_) {}

    if (mounted) {
      setState(() {
        _statusText = 'Mengunduh Paket APK v${widget.newVersion} 📥';
        _detailText = 'Sistem Android sedang mengunduh berkas pembaruan. Silakan tarik bilah notifikasi di atas layar HP dan tekan berkas unduhan untuk memasang (Update).';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final percentInt = (_progress * 100).toInt();

    return WillPopScope(
      onWillPop: () async => _isCompleted,
      child: Scaffold(
        backgroundColor: const Color(0xFF0B1528),
        body: SafeArea(
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
            child: Column(
              children: [
                // Top Header Info
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.08),
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: Colors.white12),
                      ),
                      child: Row(
                        children: [
                          Container(
                            width: 8,
                            height: 8,
                            decoration: BoxDecoration(
                              color: _isCompleted ? Colors.greenAccent : Colors.cyanAccent,
                              shape: BoxShape.circle,
                            ),
                          ),
                          const SizedBox(width: 6),
                          Text(
                            _isCompleted ? 'SIAP DIPASANG' : 'MENGUNDUH OTA',
                            style: TextStyle(
                              color: _isCompleted ? Colors.greenAccent : Colors.cyanAccent,
                              fontSize: 10,
                              fontWeight: FontWeight.w900,
                              letterSpacing: 0.5,
                            ),
                          ),
                        ],
                      ),
                    ),
                    Text(
                      'Target: v${widget.newVersion}',
                      style: const TextStyle(color: Colors.white60, fontSize: 11, fontWeight: FontWeight.bold),
                    ),
                  ],
                ),

                const Spacer(),

                // Central Visual Circular Progress
                Center(
                  child: Stack(
                    alignment: Alignment.center,
                    children: [
                      // Pulsing outer ripple
                      AnimatedBuilder(
                        animation: _pulseController,
                        builder: (context, child) {
                          return Container(
                            width: 200 + (_pulseController.value * 20),
                            height: 200 + (_pulseController.value * 20),
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              color: (_isCompleted ? Colors.greenAccent : Colors.cyanAccent).withOpacity(0.05 * (1 - _pulseController.value)),
                            ),
                          );
                        },
                      ),

                      // Progress Circle
                      SizedBox(
                        width: 170,
                        height: 170,
                        child: CircularProgressIndicator(
                          value: _progress,
                          strokeWidth: 10,
                          backgroundColor: Colors.white.withOpacity(0.08),
                          valueColor: AlwaysStoppedAnimation<Color>(
                            _isCompleted ? const Color(0xFF10B981) : const Color(0xFF06B6D4),
                          ),
                        ),
                      ),

                      // Center Content (Percentage or Checkmark)
                      Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          if (_isCompleted) ...[
                            const Icon(Icons.check_circle_rounded, color: Color(0xFF10B981), size: 54),
                            const SizedBox(height: 4),
                            const Text(
                              '100%',
                              style: TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.w900),
                            ),
                          ] else ...[
                            const Icon(Icons.cloud_download_rounded, color: Colors.cyanAccent, size: 36),
                            const SizedBox(height: 6),
                            Text(
                              '$percentInt%',
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 32,
                                fontWeight: FontWeight.w900,
                                letterSpacing: -0.5,
                              ),
                            ),
                          ],
                        ],
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 36),

                // Status Texts
                Text(
                  _statusText,
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 16,
                    fontWeight: FontWeight.w800,
                  ),
                ),
                const SizedBox(height: 8),
                Text(
                  _detailText,
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                    color: Colors.white60,
                    fontSize: 12,
                    height: 1.4,
                  ),
                ),

                const SizedBox(height: 24),

                // Linear Progress Bar
                ClipRRect(
                  borderRadius: BorderRadius.circular(10),
                  child: LinearProgressIndicator(
                    value: _progress,
                    minHeight: 8,
                    backgroundColor: Colors.white.withOpacity(0.08),
                    valueColor: AlwaysStoppedAnimation<Color>(
                      _isCompleted ? const Color(0xFF10B981) : const Color(0xFF06B6D4),
                    ),
                  ),
                ),

                const Spacer(),

                // Change Summary Card
                Container(
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.05),
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(color: Colors.white.withOpacity(0.08)),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Pembaruan yang Termasuk:',
                        style: TextStyle(color: Colors.white70, fontSize: 11, fontWeight: FontWeight.w800),
                      ),
                      const SizedBox(height: 8),
                      _buildFeatureItem('🚚 Modul Driver & Role Kurir Khusus Mobile'),
                      _buildFeatureItem('🗺️ Live GPS Route & Map Tracking Real-Time'),
                      _buildFeatureItem('💳 Sinkronisasi Pembayaran QRIS & VA Midtrans'),
                      _buildFeatureItem('⚡ Fast-Track Checkout Langsung Tanpa Keranjang'),
                    ],
                  ),
                ),

                const SizedBox(height: 24),

                // Bottom Action Button (Restart & Install)
                SizedBox(
                  width: double.infinity,
                  height: 52,
                  child: ElevatedButton.icon(
                    icon: Icon(
                      _hasTriggeredInstall
                          ? Icons.download_rounded
                          : (_isCompleted ? Icons.restart_alt_rounded : Icons.hourglass_top_rounded),
                      size: 20,
                    ),
                    label: Text(
                      _hasTriggeredInstall
                          ? 'Unduh Ulang / Buka Berkas APK 📥'
                          : (_isCompleted ? 'Pasang & Restart Aplikasi 🔄' : 'Sedang Mengunduh... ($percentInt%)'),
                      style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w900),
                    ),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: _isCompleted ? const Color(0xFF10B981) : Colors.white12,
                      foregroundColor: Colors.white,
                      elevation: _isCompleted ? 6 : 0,
                      shadowColor: const Color(0xFF10B981).withOpacity(0.5),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                    ),
                    onPressed: _isCompleted ? _installAndRestart : null,
                  ),
                ),
                if (_hasTriggeredInstall) ...[
                  const SizedBox(height: 8),
                  SizedBox(
                    width: double.infinity,
                    child: TextButton(
                      onPressed: () => SystemNavigator.pop(),
                      child: const Text('Tutup Aplikasi Lama', style: TextStyle(color: Colors.white60, fontSize: 12)),
                    ),
                  ),
                ],
                const SizedBox(height: 10),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildFeatureItem(String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 4),
      child: Row(
        children: [
          const Icon(Icons.check_rounded, color: Colors.greenAccent, size: 14),
          const SizedBox(width: 6),
          Expanded(
            child: Text(
              text,
              style: const TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.w600),
              overflow: TextOverflow.ellipsis,
            ),
          ),
        ],
      ),
    );
  }
}
