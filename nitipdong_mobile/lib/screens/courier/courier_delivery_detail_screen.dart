import 'dart:async';
import 'dart:convert';
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
  String? _proofImageBase64;

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

  Future<void> _openGoogleMapsNavigation({required bool isToCustomer}) async {
    final lat = isToCustomer
        ? (widget.orderData['dropoff_lat'] as num?)?.toDouble()
        : (widget.orderData['pickup_lat'] as num?)?.toDouble();
    final lng = isToCustomer
        ? (widget.orderData['dropoff_lng'] as num?)?.toDouble()
        : (widget.orderData['pickup_lng'] as num?)?.toDouble();

    final address = isToCustomer
        ? (widget.orderData['shipping_address'] ?? 'Alamat Pembeli')
        : (widget.orderData['store_address'] ?? 'Alamat Toko Penjual');

    Uri uri;
    if (lat != null && lng != null && lat != 0 && lng != 0) {
      uri = Uri.parse('https://www.google.com/maps/dir/?api=1&destination=$lat,$lng&travelmode=driving');
    } else {
      uri = Uri.parse('https://www.google.com/maps/dir/?api=1&destination=${Uri.encodeComponent(address)}&travelmode=driving');
    }

    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }

  void _showPhotoSourceDialog(BuildContext parentCtx, StateSetter setModalState) {
    showDialog(
      context: parentCtx,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Text('Pilih Sumber Foto Bukti', style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            ListTile(
              leading: const Icon(Icons.camera_alt_rounded, color: AppTheme.primary),
              title: const Text('Ambil dari Kamera (Simulasi)', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold)),
              onTap: () {
                Navigator.pop(ctx);
                _openSimulatedCamera(parentCtx, setModalState);
              },
            ),
            const Divider(height: 1),
            ListTile(
              leading: const Icon(Icons.photo_library_rounded, color: AppTheme.primary),
              title: const Text('Pilih dari Galeri (Simulasi)', style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold)),
              onTap: () {
                Navigator.pop(ctx);
                _openSimulatedGallery(parentCtx, setModalState);
              },
            ),
          ],
        ),
      ),
    );
  }

  void _openSimulatedCamera(BuildContext context, StateSetter setModalState) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => SimulatedCameraScreen(
          onCaptured: (base64) {
            setModalState(() {
              _proofImageBase64 = base64;
            });
          },
        ),
      ),
    );
  }

  void _openSimulatedGallery(BuildContext context, StateSetter setModalState) {
    // This is a valid 100x100 white JPEG base64 image
    const mockBase64 = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCABkAGQBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=';
    setModalState(() {
      _proofImageBase64 = mockBase64;
    });
    
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('Foto bukti penerimaan dipilih dari galeri! 🖼️'),
        backgroundColor: AppTheme.success,
        behavior: SnackBarBehavior.floating,
      ),
    );
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
                Icon(Icons.check_circle_outline_rounded, color: AppTheme.primary, size: 24),
                SizedBox(width: 8),
                Text('Konfirmasi Selesai Antar 📦', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
              ],
            ),
            const SizedBox(height: 10),
            const Text('Pastikan paket telah diterima oleh pembeli. Anda wajib mengambil foto bukti penerimaan dan menambahkan catatan serah terima di bawah ini.', style: TextStyle(fontSize: 12, color: AppTheme.textSecondary)),
            const SizedBox(height: 16),

            // Foto Bukti Selector
            StatefulBuilder(
              builder: (context, setModalState) {
                return Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Foto Bukti Penerimaan (Wajib)', style: TextStyle(fontSize: 12.5, fontWeight: FontWeight.w800, color: AppTheme.textPrimary)),
                    const SizedBox(height: 8),
                    _proofImageBase64 == null
                        ? GestureDetector(
                            onTap: () => _showPhotoSourceDialog(ctx, setModalState),
                            child: Container(
                              height: 120,
                              width: double.infinity,
                              decoration: BoxDecoration(
                                color: Colors.grey.shade50,
                                borderRadius: BorderRadius.circular(14),
                                border: Border.all(color: Colors.grey.shade300, style: BorderStyle.solid),
                              ),
                              child: const Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Icon(Icons.add_a_photo_outlined, color: AppTheme.primary, size: 36),
                                  const SizedBox(height: 8),
                                  Text('Ambil Foto atau Pilih Gambar', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppTheme.primary)),
                                  SizedBox(height: 2),
                                  Text('Kamera / Galeri', style: TextStyle(fontSize: 10, color: AppTheme.textMuted)),
                                ],
                              ),
                            ),
                          )
                        : Stack(
                            children: [
                              Container(
                                height: 160,
                                width: double.infinity,
                                decoration: BoxDecoration(
                                  borderRadius: BorderRadius.circular(14),
                                  border: Border.all(color: AppTheme.border),
                                ),
                                child: ClipRRect(
                                  borderRadius: BorderRadius.circular(12),
                                  child: Image.memory(
                                    const Base64Decoder().convert(
                                      _proofImageBase64!.replaceFirst(RegExp(r'data:image/\w+;base64,'), ''),
                                    ),
                                    fit: BoxFit.cover,
                                    width: double.infinity,
                                    height: 160,
                                  ),
                                ),
                              ),
                              Positioned(
                                top: 8,
                                right: 8,
                                child: GestureDetector(
                                  onTap: () {
                                    setModalState(() {
                                      _proofImageBase64 = null;
                                    });
                                  },
                                  child: Container(
                                    padding: const EdgeInsets.all(6),
                                    decoration: const BoxDecoration(color: Colors.redAccent, shape: BoxShape.circle),
                                    child: const Icon(Icons.close_rounded, color: Colors.white, size: 16),
                                  ),
                                ),
                              ),
                            ],
                          ),
                  ],
                );
              },
            ),
            const SizedBox(height: 16),

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

            StatefulBuilder(
              builder: (context, setModalState) {
                return SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppTheme.success,
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    ),
                    onPressed: _isSubmitting || _proofImageBase64 == null
                        ? null
                        : () async {
                            setModalState(() => _isSubmitting = true);
                            final res = await ApiService.completeCourierDelivery(
                              widget.orderData['id'],
                              notes: _notesController.text,
                              proofBase64: _proofImageBase64,
                            );
                            setModalState(() => _isSubmitting = false);

                            if (res['success'] == true && mounted) {
                              Navigator.pop(ctx); // Close sheet
                              Navigator.pop(context); // Close detail screen
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(
                                  content: Text('🎉 Pengantaran paket berhasil diselesaikan! Bukti foto telah terkirim ke buyer.'),
                                  backgroundColor: AppTheme.success,
                                  behavior: SnackBarBehavior.floating,
                                ),
                              );
                            }
                          },
                    child: _isSubmitting
                        ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                        : Text(
                            _proofImageBase64 == null 
                                ? 'Harap Ambil Foto Bukti Dahulu 📸' 
                                : 'Konfirmasi Paket Diterima ✅', 
                            style: const TextStyle(fontWeight: FontWeight.w800),
                          ),
                  ),
                );
              },
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

                  // Google Maps Nav Button Overlay
                  Positioned(
                    top: 16,
                    right: 16,
                    child: InkWell(
                      onTap: () => _openGoogleMapsNavigation(isToCustomer: true),
                      borderRadius: BorderRadius.circular(20),
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                        decoration: BoxDecoration(
                          color: Colors.black.withOpacity(0.7),
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: Colors.white30),
                        ),
                        child: const Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(Icons.directions_rounded, color: Colors.cyanAccent, size: 13),
                            SizedBox(width: 4),
                            Text(
                              'Google Maps 🧭',
                              style: TextStyle(color: Colors.white, fontSize: 10.5, fontWeight: FontWeight.w800),
                            ),
                          ],
                        ),
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
                        const SizedBox(height: 10),
                        SizedBox(
                          width: double.infinity,
                          child: OutlinedButton.icon(
                            icon: const Icon(Icons.turn_right_rounded, size: 16, color: AppTheme.primary),
                            label: const Text('Navigasi ke Toko di Google Maps 🧭', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w800, color: AppTheme.primary)),
                            style: OutlinedButton.styleFrom(
                              side: BorderSide(color: AppTheme.primary.withOpacity(0.3)),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                              padding: const EdgeInsets.symmetric(vertical: 8),
                            ),
                            onPressed: () => _openGoogleMapsNavigation(isToCustomer: false),
                          ),
                        ),
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
                        const SizedBox(height: 10),
                        SizedBox(
                          width: double.infinity,
                          child: ElevatedButton.icon(
                            icon: const Icon(Icons.navigation_rounded, size: 16, color: Colors.white),
                            label: const Text('Navigasi ke Pembeli di Google Maps 🧭', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w800, color: Colors.white)),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.redAccent,
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                              padding: const EdgeInsets.symmetric(vertical: 8),
                            ),
                            onPressed: () => _openGoogleMapsNavigation(isToCustomer: true),
                          ),
                        ),
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
    // 1. Draw Land Background (Light Warm Gray/Blue)
    final landPaint = Paint()..color = const Color(0xFFF1F5F9);
    canvas.drawRect(Rect.fromLTWH(0, 0, size.width, size.height), landPaint);

    // 2. Draw Green Parks (Green Zones)
    final parkPaint = Paint()..color = const Color(0xFFDCFCE7);
    // Park 1
    canvas.drawRRect(
      RRect.fromRectAndRadius(
        Rect.fromLTWH(size.width * 0.05, size.height * 0.1, size.width * 0.22, size.height * 0.35),
        const Radius.circular(16),
      ),
      parkPaint,
    );
    // Park 2
    canvas.drawRRect(
      RRect.fromRectAndRadius(
        Rect.fromLTWH(size.width * 0.72, size.height * 0.55, size.width * 0.23, size.height * 0.38),
        const Radius.circular(16),
      ),
      parkPaint,
    );

    // 3. Draw River / Water Flow (Light Sky Blue)
    final waterPaint = Paint()
      ..color = const Color(0xFFBAE6FD)
      ..strokeWidth = 32
      ..style = PaintingStyle.stroke
      ..strokeCap = StrokeCap.round;

    final riverPath = Path()
      ..moveTo(-30, size.height * 0.45)
      ..cubicTo(size.width * 0.35, size.height * 0.35, size.width * 0.65, size.height * 0.75, size.width + 30, size.height * 0.65);
    canvas.drawPath(riverPath, waterPaint);

    // 4. Draw City Streets / Road Grid (White lines with grey borders)
    final roadBasePaint = Paint()
      ..color = const Color(0xFFCBD5E1)
      ..strokeWidth = 14
      ..style = PaintingStyle.stroke
      ..strokeCap = StrokeCap.round;

    final roadMiddlePaint = Paint()
      ..color = Colors.white
      ..strokeWidth = 10
      ..style = PaintingStyle.stroke
      ..strokeCap = StrokeCap.round;

    final streets = <Path>[
      // Vertical streets
      Path()..moveTo(size.width * 0.25, -20)..lineTo(size.width * 0.25, size.height + 20),
      Path()..moveTo(size.width * 0.5, -20)..lineTo(size.width * 0.5, size.height + 20),
      Path()..moveTo(size.width * 0.75, -20)..lineTo(size.width * 0.75, size.height + 20),
      // Horizontal streets
      Path()..moveTo(-20, size.height * 0.2)..lineTo(size.width + 20, size.height * 0.2),
      Path()..moveTo(-20, size.height * 0.5)..lineTo(size.width + 20, size.height * 0.5),
      Path()..moveTo(-20, size.height * 0.8)..lineTo(size.width + 20, size.height * 0.8),
    ];

    for (var street in streets) {
      canvas.drawPath(street, roadBasePaint);
      canvas.drawPath(street, roadMiddlePaint);
    }

    // 5. Draw Active Courier Route Path (Vibrant blue with semi-transparent shadow)
    final startPoint = Offset(size.width * 0.15, size.height * 0.75);
    final endPoint = Offset(size.width * 0.85, size.height * 0.25);
    final cp1 = Offset(size.width * 0.4, size.height * 0.9);
    final cp2 = Offset(size.width * 0.6, size.height * 0.1);

    final routePath = Path()
      ..moveTo(startPoint.dx, startPoint.dy)
      ..cubicTo(cp1.dx, cp1.dy, cp2.dx, cp2.dy, endPoint.dx, endPoint.dy);

    final routeShadowPaint = Paint()
      ..color = AppTheme.primary.withOpacity(0.2)
      ..strokeWidth = 8
      ..style = PaintingStyle.stroke
      ..strokeCap = StrokeCap.round;

    final routeLinePaint = Paint()
      ..color = AppTheme.primary
      ..strokeWidth = 4
      ..style = PaintingStyle.stroke
      ..strokeCap = StrokeCap.round;

    canvas.drawPath(routePath, routeShadowPaint);
    canvas.drawPath(routePath, routeLinePaint);

    // 6. Draw Store Marker (Origin)
    final storePaint = Paint()..color = const Color(0xFF0F172A);
    canvas.drawCircle(startPoint, 14, storePaint);
    canvas.drawCircle(startPoint, 10, Paint()..color = const Color(0xFF0E7490));
    canvas.drawCircle(startPoint, 4, Paint()..color = Colors.white);

    // 7. Draw Home Marker (Destination)
    final destPaint = Paint()..color = const Color(0xFF0F172A);
    canvas.drawCircle(endPoint, 14, destPaint);
    canvas.drawCircle(endPoint, 10, Paint()..color = Colors.redAccent);
    canvas.drawCircle(endPoint, 4, Paint()..color = Colors.white);

    // 8. Draw Moving Courier Marker with Pulse
    final courierX = startPoint.dx + (endPoint.dx - startPoint.dx) * progress;
    final courierY = startPoint.dy + (endPoint.dy - startPoint.dy) * progress;
    final courierPos = Offset(courierX, courierY);

    // Simulated Radar Ripple locally based on progress
    final pulseScale = (progress * 5) % 1.0;
    final pulseRadius = 14.0 + (pulseScale * 8.0);
    canvas.drawCircle(courierPos, pulseRadius, Paint()..color = Colors.cyanAccent.withOpacity(0.3 * (1.0 - pulseScale)));
    
    canvas.drawCircle(courierPos, 12, Paint()..color = const Color(0xFF0F172A));
    canvas.drawCircle(courierPos, 8, Paint()..color = Colors.cyanAccent);
    canvas.drawCircle(courierPos, 3, Paint()..color = Colors.white);
  }

  @override
  bool shouldRepaint(covariant RouteMapPainter oldDelegate) => oldDelegate.progress != progress;
}

class SimulatedCameraScreen extends StatefulWidget {
  final Function(String) onCaptured;

  const SimulatedCameraScreen({Key? key, required this.onCaptured}) : super(key: key);

  @override
  State<SimulatedCameraScreen> createState() => _SimulatedCameraScreenState();
}

class _SimulatedCameraScreenState extends State<SimulatedCameraScreen> {
  bool _isCaptured = false;

  void _captureImage() {
    setState(() => _isCaptured = true);
    
    // Play visual flash effect
    Future.delayed(const Duration(milliseconds: 150), () {
      if (mounted) {
        // This is a valid 100x100 white JPEG base64 image that decodes perfectly
        const mockBase64 = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCABkAGQBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=';
        widget.onCaptured(mockBase64);
        Navigator.pop(context);
        
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Foto bukti penerimaan berhasil diambil! 📸'),
            backgroundColor: AppTheme.success,
            behavior: SnackBarBehavior.floating,
          ),
        );
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      body: Stack(
        children: [
          // 1. Camera Viewport Grid
          Positioned.fill(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Spacer(),
                // Simulated Lens Viewport
                Container(
                  width: double.infinity,
                  height: 350,
                  margin: const EdgeInsets.symmetric(horizontal: 24),
                  decoration: BoxDecoration(
                    color: const Color(0xFF1E293B),
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(color: Colors.white24, width: 2),
                  ),
                  child: Stack(
                    alignment: Alignment.center,
                    children: [
                      // Grid lines
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                        children: [
                          Container(width: 1, color: Colors.white10),
                          Container(width: 1, color: Colors.white10),
                        ],
                      ),
                      Column(
                        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                        children: [
                          Container(height: 1, color: Colors.white10),
                          Container(height: 1, color: Colors.white10),
                        ],
                      ),
                      // Mock Package Image inside Viewport
                      Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Container(
                            padding: const EdgeInsets.all(16),
                            decoration: BoxDecoration(
                              color: AppTheme.primary.withOpacity(0.15),
                              shape: BoxShape.circle,
                            ),
                            child: const Icon(
                              Icons.all_inbox_rounded,
                              size: 72,
                              color: Colors.cyanAccent,
                            ),
                          ),
                          const SizedBox(height: 14),
                          const Text(
                            'BUKTI SERAH TERIMA PAKET',
                            style: TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.w900, letterSpacing: 1.2),
                          ),
                          const SizedBox(height: 4),
                          const Text(
                            'Posisikan paket di dalam kotak kamera',
                            style: TextStyle(color: Colors.white60, fontSize: 10),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
                const Spacer(),
              ],
            ),
          ),

          // 2. Header: Camera Info & Back
          Positioned(
            top: 48,
            left: 16,
            right: 16,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                IconButton(
                  icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white, size: 20),
                  onPressed: () => Navigator.pop(context),
                ),
                const Text(
                  'KAMERA BUKTI PENGIRIMAN',
                  style: TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.bold, letterSpacing: 1.5),
                ),
                const SizedBox(width: 40), // spacer
              ],
            ),
          ),

          // 3. Bottom Controls: Shutter Button
          Positioned(
            bottom: 48,
            left: 0,
            right: 0,
            child: Column(
              children: [
                GestureDetector(
                  onTap: _isCaptured ? null : _captureImage,
                  child: Container(
                    padding: const EdgeInsets.all(4),
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      border: Border.all(color: Colors.white, width: 4),
                    ),
                    child: Container(
                      width: 64,
                      height: 64,
                      decoration: const BoxDecoration(
                        color: Colors.white,
                        shape: BoxShape.circle,
                      ),
                    ),
                  ),
                ),
                const SizedBox(height: 12),
                const Text(
                  'TAP UNTUK MENGAMBIL FOTO',
                  style: TextStyle(color: Colors.white60, fontSize: 10, fontWeight: FontWeight.w700, letterSpacing: 1),
                ),
              ],
            ),
          ),

          // 4. White Flash Screen Overlay (On Capture)
          if (_isCaptured)
            Positioned.fill(
              child: Container(
                color: Colors.white,
              ),
            ),
        ],
      ),
    );
  }
}
