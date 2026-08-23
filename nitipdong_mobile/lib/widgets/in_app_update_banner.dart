import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../services/api_service.dart';
import '../theme/app_theme.dart';
import '../screens/update/app_update_progress_screen.dart';

class InAppUpdateBanner extends StatefulWidget {
  final VoidCallback? onDismissed;

  const InAppUpdateBanner({Key? key, this.onDismissed}) : super(key: key);

  @override
  State<InAppUpdateBanner> createState() => _InAppUpdateBannerState();
}

class _InAppUpdateBannerState extends State<InAppUpdateBanner> with SingleTickerProviderStateMixin {
  bool _hasUpdate = false;
  bool _isDismissed = false;
  String _latestVersion = '';
  String _updateUrl = 'https://budayakita.com/download/app';
  late AnimationController _animController;
  late Animation<double> _fadeScaleAnimation;

  @override
  void initState() {
    super.initState();
    _animController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 400),
    );
    _fadeScaleAnimation = CurvedAnimation(
      parent: _animController,
      curve: Curves.easeOutBack,
    );

    _checkForUpdates();
  }

  @override
  void dispose() {
    _animController.dispose();
    super.dispose();
  }

  Future<void> _checkForUpdates() async {
    try {
      final status = await ApiService.checkSystemStatus();
      if (!mounted) return;

      final serverVersion = status['latest_version']?.toString() ?? '';
      final currentVersion = ApiService.currentAppVersion;

      if (serverVersion.isNotEmpty && serverVersion != currentVersion) {
        setState(() {
          _hasUpdate = true;
          _latestVersion = serverVersion;
          _updateUrl = status['update_url'] ?? 'https://budayakita.com/download/app';
        });
        _animController.forward();
      }
    } catch (_) {}
  }

  void _launchUpdate() {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => AppUpdateProgressScreen(
          newVersion: _latestVersion,
          downloadUrl: _updateUrl,
        ),
      ),
    );
  }

  void _dismissBanner() {
    _animController.reverse().then((_) {
      if (mounted) {
        setState(() => _isDismissed = true);
        if (widget.onDismissed != null) widget.onDismissed!();
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    if (!_hasUpdate || _isDismissed) {
      return const SizedBox.shrink();
    }

    return SizeTransition(
      sizeFactor: _fadeScaleAnimation,
      child: FadeTransition(
        opacity: _fadeScaleAnimation,
        child: Container(
          margin: const EdgeInsets.fromLTRB(16, 8, 16, 14),
          decoration: BoxDecoration(
            gradient: const LinearGradient(
              colors: [Color(0xFF0F172A), Color(0xFF0E7490)],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
            borderRadius: BorderRadius.circular(20),
            boxShadow: [
              BoxShadow(
                color: AppTheme.primary.withOpacity(0.25),
                blurRadius: 20,
                offset: const Offset(0, 8),
              ),
            ],
            border: Border.all(
              color: Colors.white.withOpacity(0.15),
              width: 1,
            ),
          ),
          child: Stack(
            children: [
              // Background subtle pattern circle
              Positioned(
                right: -20,
                bottom: -20,
                child: Container(
                  width: 110,
                  height: 110,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: Colors.white.withOpacity(0.05),
                  ),
                ),
              ),

              Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Header Row: Tag & Close Button
                    Row(
                      children: [
                        // Tag Badge
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                          decoration: BoxDecoration(
                            color: AppTheme.primary.withOpacity(0.3),
                            borderRadius: BorderRadius.circular(20),
                            border: Border.all(
                              color: AppTheme.primaryLight.withOpacity(0.3),
                            ),
                          ),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              const Icon(Icons.rocket_launch_rounded, size: 12, color: Colors.cyanAccent),
                              const SizedBox(width: 5),
                              Text(
                                'Versi $_latestVersion Tersedia',
                                style: const TextStyle(
                                  fontSize: 10,
                                  fontWeight: FontWeight.w800,
                                  color: Colors.cyanAccent,
                                  letterSpacing: 0.3,
                                ),
                              ),
                            ],
                          ),
                        ),
                        const Spacer(),

                        // Close / Dismiss Button
                        Material(
                          color: Colors.transparent,
                          child: InkWell(
                            onTap: _dismissBanner,
                            borderRadius: BorderRadius.circular(20),
                            child: Padding(
                              padding: const EdgeInsets.all(4),
                              child: Icon(
                                Icons.close_rounded,
                                size: 18,
                                color: Colors.white.withOpacity(0.6),
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 10),

                    // Title & Description
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Container(
                          width: 42,
                          height: 42,
                          decoration: BoxDecoration(
                            color: Colors.white.withOpacity(0.12),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: const Center(
                            child: Icon(
                              Icons.system_update_alt_rounded,
                              color: Colors.white,
                              size: 22,
                            ),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text(
                                'Update NitipDong Terbaru! 🎉',
                                style: TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.w800,
                                  color: Colors.white,
                                  letterSpacing: -0.3,
                                ),
                              ),
                              const SizedBox(height: 3),
                              Text(
                                'Dapatkan performa lebih cepat, icon resmi baru, dan fitur checkout yang lebih mulus.',
                                style: TextStyle(
                                  fontSize: 11,
                                  color: Colors.white.withOpacity(0.8),
                                  height: 1.35,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 14),

                    // Actions Row
                    Row(
                      children: [
                        Expanded(
                          child: ElevatedButton(
                            onPressed: _launchUpdate,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.white,
                              foregroundColor: AppTheme.textPrimary,
                              elevation: 0,
                              padding: const EdgeInsets.symmetric(vertical: 10),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(10),
                              ),
                            ),
                            child: const Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Icon(Icons.download_rounded, size: 16, color: AppTheme.primaryDark),
                                SizedBox(width: 6),
                                Text(
                                  'Perbarui Sekarang',
                                  style: TextStyle(
                                    fontSize: 12,
                                    fontWeight: FontWeight.w800,
                                    color: AppTheme.primaryDark,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                        const SizedBox(width: 8),
                        TextButton(
                          onPressed: _dismissBanner,
                          style: TextButton.styleFrom(
                            foregroundColor: Colors.white70,
                            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                          ),
                          child: const Text(
                            'Nanti',
                            style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
