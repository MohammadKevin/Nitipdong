import 'package:flutter/material.dart';
import '../theme/app_theme.dart';
import '../services/api_service.dart';
import '../widgets/server_config_dialog.dart';
import 'main_nav_screen.dart';

class MaintenanceScreen extends StatefulWidget {
  final String title;
  final String message;

  const MaintenanceScreen({
    Key? key,
    this.title = 'Mode Pemeliharaan & Pengembangan 🛠️',
    this.message = 'Aplikasi NitipDong sedang dalam tahap pembaruan fitur & optimalisasi sistem untuk pengalaman belanja yang lebih baik. Silakan coba kembali beberapa saat lagi.',
  }) : super(key: key);

  @override
  State<MaintenanceScreen> createState() => _MaintenanceScreenState();
}

class _MaintenanceScreenState extends State<MaintenanceScreen> {
  bool _isChecking = false;
  late String _currentTitle;
  late String _currentMessage;
  int _devTapCount = 0;

  @override
  void initState() {
    super.initState();
    _currentTitle = widget.title;
    _currentMessage = widget.message;
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
        const SnackBar(
          content: Text('Sistem masih dalam tahap pemeliharaan. Terima kasih atas kesabaran Anda!'),
          backgroundColor: AppTheme.accentOrange,
        ),
      );
    }
  }

  void _openDevSettings() {
    ServerConfigDialog.show(context, onSaved: () {
      _recheckStatus();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.accentNavy,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        automaticallyImplyLeading: false,
        actions: [
          // Developer / Server Config Quick Access
          IconButton(
            tooltip: 'Pengaturan Server & Mode Pengembang',
            icon: Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.12),
                borderRadius: BorderRadius.circular(10),
              ),
              child: const Icon(Icons.dns_rounded, color: Colors.cyanAccent, size: 18),
            ),
            onPressed: _openDevSettings,
          ),
          const SizedBox(width: 8),
        ],
      ),
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 10),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                // Animated Maintenance Icon Card
                Container(
                  width: 100,
                  height: 100,
                  decoration: BoxDecoration(
                    color: AppTheme.surface,
                    borderRadius: BorderRadius.circular(28),
                    boxShadow: [
                      BoxShadow(
                        color: AppTheme.primary.withOpacity(0.35),
                        blurRadius: 30,
                        offset: const Offset(0, 12),
                      ),
                    ],
                  ),
                  child: const Center(
                    child: Icon(
                      Icons.construction_rounded,
                      color: AppTheme.primary,
                      size: 52,
                    ),
                  ),
                ),
                const SizedBox(height: 24),

                // Badge
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                  decoration: BoxDecoration(
                    color: AppTheme.accentOrange.withOpacity(0.2),
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(color: AppTheme.accentOrange.withOpacity(0.4)),
                  ),
                  child: const Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(Icons.bolt, color: AppTheme.accentOrange, size: 14),
                      SizedBox(width: 4),
                      Text(
                        'PENGEMBANGAN SISTEM',
                        style: TextStyle(
                          color: AppTheme.accentOrange,
                          fontSize: 10,
                          fontWeight: FontWeight.w900,
                          letterSpacing: 0.5,
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
                  style: const TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.w800,
                    color: Colors.white,
                    letterSpacing: -0.5,
                  ),
                ),
                const SizedBox(height: 12),

                // Description Card
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.06),
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(color: Colors.white.withOpacity(0.1)),
                  ),
                  child: Text(
                    _currentMessage,
                    textAlign: TextAlign.center,
                    style: const TextStyle(
                      fontSize: 13,
                      color: Colors.white70,
                      height: 1.5,
                    ),
                  ),
                ),
                const SizedBox(height: 32),

                // Retry Button
                SizedBox(
                  width: double.infinity,
                  height: 48,
                  child: ElevatedButton(
                    onPressed: _isChecking ? null : _recheckStatus,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppTheme.primary,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                    ),
                    child: _isChecking
                        ? const SizedBox(
                            width: 20,
                            height: 20,
                            child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                          )
                        : const Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(Icons.refresh_rounded, size: 18),
                              SizedBox(width: 8),
                              Text('Coba Muat Ulang', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 13)),
                            ],
                          ),
                  ),
                ),
                const SizedBox(height: 20),

                // App info & Secret tap trigger for Developer Mode
                GestureDetector(
                  onTap: () {
                    _devTapCount++;
                    if (_devTapCount >= 3) {
                      _devTapCount = 0;
                      _openDevSettings();
                    }
                  },
                  child: Padding(
                    padding: const EdgeInsets.all(8.0),
                    child: Text(
                      'NitipDong Platform • Versi ${ApiService.currentAppVersion}',
                      style: const TextStyle(color: Colors.white38, fontSize: 11),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
