import 'dart:async';
import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../theme/app_theme.dart';
import '../../services/api_service.dart';

class CourierDeliveryDetailScreen extends StatefulWidget {
  final Map<String, dynamic> orderData;

  const CourierDeliveryDetailScreen({Key? key, required this.orderData}) : super(key: key);

  @override
  State<CourierDeliveryDetailScreen> createState() => _CourierDeliveryDetailScreenState();
}

class _CourierDeliveryDetailScreenState extends State<CourierDeliveryDetailScreen> {
  late double _currentLat;
  late double _currentLng;
  bool _isBroadcastingGps = true;
  Timer? _gpsBroadcastTimer;
  int _gpsTick = 0;
  bool _isSubmitting = false;

  final TextEditingController _notesController = TextEditingController(text: 'Paket telah diterima dengan baik oleh pembeli.');

  @override
  void initState() {
    super.initState();
    _currentLat = (widget.orderData['current_courier_lat'] as num?)?.toDouble() ?? -7.2575;
    _currentLng = (widget.orderData['current_courier_lng'] as num?)?.toDouble() ?? 112.7521;

    // Start auto-broadcasting GPS movements every 6 seconds
    _startGpsBroadcast();
  }

  @override
  void dispose() {
    _gpsBroadcastTimer?.cancel();
    _notesController.dispose();
    super.dispose();
  }

  void _startGpsBroadcast() {
    _gpsBroadcastTimer?.cancel();
    _gpsBroadcastTimer = Timer.periodic(const Duration(seconds: 6), (timer) async {
      if (!_isBroadcastingGps || !mounted) return;

      // Simulate smooth step movement towards customer dropoff
      setState(() {
        _gpsTick++;
        _currentLat += 0.0003;
        _currentLng -= 0.0002;
      });

      await ApiService.updateCourierGps(
        widget.orderData['id'],
        _currentLat,
        _currentLng,
      );
    });
  }

  Future<void> _openWhatsApp(String phone, String name) async {
    final cleanPhone = phone.replaceAll(RegExp(r'[^0-9]'), '');
    final formatted = cleanPhone.startsWith('0') ? '62${cleanPhone.substring(1)}' : cleanPhone;
    final uri = Uri.parse('https://wa.me/$formatted?text=Halo%20$name,%20saya%20kurir%20NitipDong%20yang%20mengantar%20pesanan%20${widget.orderData['invoice_number']}');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }

  void _showCompleteModal() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.white,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (ctx) => Padding(
        padding: EdgeInsets.fromLTRB(20, 16, 20, MediaQuery.of(ctx).viewInsets.bottom + 30),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(child: Container(width: 40, height: 4, decoration: BoxDecoration(color: Colors.grey.shade300, borderRadius: BorderRadius.circular(2)))),
            const SizedBox(height: 16),
            const Row(
              children: [
                Icon(Icons.camera_alt_rounded, color: AppTheme.primary, size: 24),
                SizedBox(width: 8),
                Text('Konfirmasi Selesai Antar 📦', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
              ],
            ),
            const SizedBox(height: 10),
            const Text('Pastikan paket telah diterima oleh pembeli. Anda dapat menambahkan catatan serah terima di bawah ini.', style: TextStyle(fontSize: 12, color: AppTheme.textSecondary)),
            const SizedBox(height: 14),

            TextField(
              controller: _notesController,
              maxLines: 2,
              decoration: InputDecoration(
                labelText: 'Catatan Pengiriman',
                hintText: 'Contoh: Diterima oleh Pak Budi (Security)',
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
              ),
            ),
            const SizedBox(height: 20),

            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.success,
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                onPressed: _isSubmitting
                    ? null
                    : () async {
                        setState(() => _isSubmitting = true);
                        final res = await ApiService.completeCourierDelivery(
                          widget.orderData['id'],
                          notes: _notesController.text,
                        );
                        setState(() => _isSubmitting = false);

                        if (res['success'] == true && mounted) {
                          Navigator.pop(ctx); // Close sheet
                          Navigator.pop(context); // Close detail screen
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(
                              content: Text('🎉 Pengantaran paket berhasil diselesaikan! Komisi telah ditambahkan.'),
                              backgroundColor: AppTheme.success,
                              behavior: SnackBarBehavior.floating,
                            ),
                          );
                        }
                      },
                child: _isSubmitting
                    ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                    : const Text('Konfirmasi Paket Diterima ✅', style: TextStyle(fontWeight: FontWeight.w800)),
              ),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final order = widget.orderData;

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: AppTheme.accentNavy,
        elevation: 0,
        title: Text(
          'Navigasi Antaran #${order['invoice_number']}',
          style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w800, color: Colors.white),
        ),
        actions: [
          IconButton(
            icon: Icon(_isBroadcastingGps ? Icons.gps_fixed_rounded : Icons.gps_off_rounded, color: _isBroadcastingGps ? Colors.cyanAccent : Colors.white60),
            onPressed: () {
              setState(() => _isBroadcastingGps = !_isBroadcastingGps);
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(
                  content: Text(_isBroadcastingGps ? 'Broadcast GPS Aktif 📡' : 'Broadcast GPS Dijeda ⏸️'),
                  duration: const Duration(seconds: 1),
                ),
              );
            },
          ),
        ],
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            // Visual Map Route Canvas
            Container(
              height: 240,
              width: double.infinity,
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFF0F172A), Color(0xFF1E293B)],
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                ),
                boxShadow: [
                  BoxShadow(color: Colors.black.withOpacity(0.1), blurRadius: 10, offset: const Offset(0, 4)),
                ],
              ),
              child: Stack(
                children: [
                  // Road grid visualization
                  Positioned.fill(
                    child: CustomPaint(
                      painter: RouteMapPainter(progress: (_gpsTick % 10) / 10.0),
                    ),
                  ),

                  // GPS Live Status Badge
                  Positioned(
                    top: 16,
                    left: 16,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                      decoration: BoxDecoration(
                        color: Colors.black.withOpacity(0.6),
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: Colors.cyanAccent.withOpacity(0.5)),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Container(width: 8, height: 8, decoration: const BoxDecoration(color: Colors.cyanAccent, shape: BoxShape.circle)),
                          const SizedBox(width: 6),
                          Text(
                            'GPS Live: ${_currentLat.toStringAsFixed(4)}, ${_currentLng.toStringAsFixed(4)}',
                            style: const TextStyle(color: Colors.white, fontSize: 10.5, fontWeight: FontWeight.bold),
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),

            // Order & Contact Information Card
            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                children: [
                  // Store Pickup Card
                  Container(
                    padding: const EdgeInsets.all(14),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(14),
                      border: Border.all(color: AppTheme.border),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Container(
                              padding: const EdgeInsets.all(6),
                              decoration: BoxDecoration(color: AppTheme.primaryLight, borderRadius: BorderRadius.circular(8)),
                              child: const Icon(Icons.storefront_rounded, color: AppTheme.primary, size: 18),
                            ),
                            const SizedBox(width: 8),
                            const Text('Titik Penjemputan Toko', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w800, color: AppTheme.primaryDark)),
                            const Spacer(),
                            IconButton(
                              icon: const Icon(Icons.chat_bubble_outline_rounded, color: Colors.green, size: 20),
                              onPressed: () => _openWhatsApp(order['store_phone'] ?? '081298765432', order['store_name'] ?? 'Toko'),
                            ),
                          ],
                        ),
                        const SizedBox(height: 6),
                        Text(order['store_name'] ?? 'Toko Official', style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w800)),
                        Text(order['store_address'] ?? 'Alamat toko', style: const TextStyle(fontSize: 11.5, color: AppTheme.textSecondary)),
                      ],
                    ),
                  ),
                  const SizedBox(height: 12),

                  // Customer Dropoff Card
                  Container(
                    padding: const EdgeInsets.all(14),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(14),
                      border: Border.all(color: AppTheme.border),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Container(
                              padding: const EdgeInsets.all(6),
                              decoration: BoxDecoration(color: Colors.red.shade50, borderRadius: BorderRadius.circular(8)),
                              child: const Icon(Icons.location_on_rounded, color: Colors.redAccent, size: 18),
                            ),
                            const SizedBox(width: 8),
                            const Text('Alamat Tujuan Pembeli', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w800, color: Colors.redAccent)),
                            const Spacer(),
                            IconButton(
                              icon: const Icon(Icons.phone_in_talk_rounded, color: Colors.green, size: 20),
                              onPressed: () => _openWhatsApp(order['recipient_phone'] ?? '081234567890', order['recipient_name'] ?? 'Pembeli'),
                            ),
                          ],
                        ),
                        const SizedBox(height: 6),
                        Text('${order['recipient_name']} (${order['recipient_phone']})', style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w800)),
                        Text(order['shipping_address'] ?? 'Alamat penerima', style: const TextStyle(fontSize: 11.5, color: AppTheme.textSecondary)),
                      ],
                    ),
                  ),
                  const SizedBox(height: 20),

                  // Complete Delivery Big Button
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton.icon(
                      icon: const Icon(Icons.check_circle_outline_rounded, size: 20),
                      label: const Text('Selesaikan Antaran & Upload Bukti 📸', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w800)),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppTheme.success,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                        shadowColor: AppTheme.success.withOpacity(0.4),
                        elevation: 4,
                      ),
                      onPressed: _showCompleteModal,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class RouteMapPainter extends CustomPainter {
  final double progress;

  RouteMapPainter({required this.progress});

  @override
  void paint(Canvas canvas, Size size) {
    final roadPaint = Paint()
      ..color = Colors.white.withOpacity(0.12)
      ..strokeWidth = 12
      ..style = PaintingStyle.stroke
      ..strokeCap = StrokeCap.round;

    final pathPaint = Paint()
      ..color = Colors.cyanAccent.withOpacity(0.6)
      ..strokeWidth = 4
      ..style = PaintingStyle.stroke;

    final startPoint = Offset(size.width * 0.2, size.height * 0.7);
    final endPoint = Offset(size.width * 0.8, size.height * 0.3);
    final controlPoint1 = Offset(size.width * 0.4, size.height * 0.85);
    final controlPoint2 = Offset(size.width * 0.6, size.height * 0.15);

    final path = Path()
      ..moveTo(startPoint.dx, startPoint.dy)
      ..cubicTo(controlPoint1.dx, controlPoint1.dy, controlPoint2.dx, controlPoint2.dy, endPoint.dx, endPoint.dy);

    canvas.drawPath(path, roadPaint);
    canvas.drawPath(path, pathPaint);

    // Draw Store Marker
    final storePaint = Paint()..color = const Color(0xFF0E7490);
    canvas.drawCircle(startPoint, 10, storePaint);
    canvas.drawCircle(startPoint, 4, Paint()..color = Colors.white);

    // Draw Destination Marker
    final destPaint = Paint()..color = Colors.redAccent;
    canvas.drawCircle(endPoint, 10, destPaint);
    canvas.drawCircle(endPoint, 4, Paint()..color = Colors.white);

    // Draw Moving Courier Marker
    final courierX = startPoint.dx + (endPoint.dx - startPoint.dx) * progress;
    final courierY = startPoint.dy + (endPoint.dy - startPoint.dy) * progress;
    final courierOffset = Offset(courierX, courierY);

    canvas.drawCircle(courierOffset, 14, Paint()..color = Colors.cyanAccent.withOpacity(0.3));
    canvas.drawCircle(courierOffset, 8, Paint()..color = Colors.cyanAccent);
  }

  @override
  bool shouldRepaint(covariant RouteMapPainter oldDelegate) => oldDelegate.progress != progress;
}
