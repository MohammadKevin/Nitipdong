import 'package:flutter/material.dart';

class InAppNotification {
  static OverlayEntry? _currentEntry;

  static void show(
    BuildContext context, {
    required String title,
    required String message,
    IconData icon = Icons.notifications_active_rounded,
  }) {
    // Dismiss current notification if visible
    dismiss();

    final overlayState = Overlay.of(context);
    
    _currentEntry = OverlayEntry(
      builder: (context) => NotificationBannerWidget(
        title: title,
        message: message,
        icon: icon,
        onDismiss: () {
          dismiss();
        },
      ),
    );

    overlayState.insert(_currentEntry!);

    // Auto dismiss after 4.5 seconds
    Future.delayed(const Duration(milliseconds: 4500), () {
      dismiss();
    });
  }

  static void dismiss() {
    if (_currentEntry != null) {
      try {
        _currentEntry!.remove();
      } catch (_) {}
      _currentEntry = null;
    }
  }
}

class NotificationBannerWidget extends StatefulWidget {
  final String title;
  final String message;
  final IconData icon;
  final VoidCallback onDismiss;

  const NotificationBannerWidget({
    Key? key,
    required this.title,
    required this.message,
    required this.icon,
    required this.onDismiss,
  }) : super(key: key);

  @override
  State<NotificationBannerWidget> createState() => _NotificationBannerWidgetState();
}

class _NotificationBannerWidgetState extends State<NotificationBannerWidget> with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<Offset> _offsetAnimation;
  bool _isDismissed = false;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 350),
    );
    _offsetAnimation = Tween<Offset>(
      begin: const Offset(0.0, -1.2),
      end: Offset.zero,
    ).animate(CurvedAnimation(
      parent: _controller,
      curve: Curves.easeOutBack,
    ));

    _controller.forward();
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  void _handleDismiss() {
    if (_isDismissed) return;
    setState(() => _isDismissed = true);
    _controller.reverse().then((_) {
      widget.onDismiss();
    });
  }

  @override
  Widget build(BuildContext context) {
    final mediaQuery = MediaQuery.of(context);
    final topPadding = mediaQuery.padding.top > 0 ? mediaQuery.padding.top + 8 : 36.0;

    return Positioned(
      top: topPadding,
      left: 12,
      right: 12,
      child: SlideTransition(
        position: _offsetAnimation,
        child: Material(
          color: Colors.transparent,
          child: GestureDetector(
            onVerticalDragUpdate: (details) {
              if (details.primaryDelta! < -5) {
                _handleDismiss();
              }
            },
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              decoration: BoxDecoration(
                color: const Color(0xFF0F172A).withOpacity(0.95), // Slate-900 transparent
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: Colors.cyanAccent.withOpacity(0.4), width: 1.2),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.3),
                    blurRadius: 16,
                    offset: const Offset(0, 8),
                  ),
                ],
              ),
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: Colors.cyanAccent.withOpacity(0.12),
                      shape: BoxShape.circle,
                    ),
                    child: Icon(widget.icon, color: Colors.cyanAccent, size: 22),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text(
                          widget.title,
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 12.5,
                            fontWeight: FontWeight.w900,
                            letterSpacing: 0.2,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          widget.message,
                          style: const TextStyle(
                            color: Colors.white70,
                            fontSize: 11,
                          ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 8),
                  IconButton(
                    icon: const Icon(Icons.close_rounded, color: Colors.white60, size: 18),
                    padding: EdgeInsets.zero,
                    constraints: const BoxConstraints(),
                    onPressed: _handleDismiss,
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
