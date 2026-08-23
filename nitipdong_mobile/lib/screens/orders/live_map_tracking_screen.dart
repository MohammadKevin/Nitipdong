import 'dart:async';
import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../theme/app_theme.dart';
import '../../services/api_service.dart';

class LiveMapTrackingScreen extends StatefulWidget {
  final dynamic orderId;
  final String invoiceNumber;

  const LiveMapTrackingScreen({
    Key? key,
    required this.orderId,
    required this.invoiceNumber,
  }) : super(key: key);

  @override
  State<LiveMapTrackingScreen> createState() => _LiveMapTrackingScreenState();
}

class _LiveMapTrackingScreenState extends State<LiveMapTrackingScreen> with SingleTickerProviderStateMixin {
  bool _isLoading = true;
  Map<String, dynamic>? _trackingData;
  Timer? _pollingTimer;
  double _animationProgress = 0.45;
  late AnimationController _pulseController;

  @override
  void initState() {
    super.initState();
    _pulseController = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 2),
    )..repeat(reverse: true);

    _loadTrackingData();
    // Auto-refresh live tracking every 6 seconds
    _pollingTimer = Timer.periodic(const Duration(seconds: 6), (_) => _loadTrackingData(isSilent: true));
  }

  @override
  void dispose() {
    _pollingTimer?.cancel();
    _pulseController.dispose();
    super.dispose();
  }

  Future<void> _loadTrackingData({bool isSilent = false}) async {
    if (!isSilent) setState(() => _isLoading = true);
    final data = await ApiService.getOrderLiveTracking(widget.orderId);
    if (mounted) {
      setState(() {
        _trackingData = data;
        _isLoading = false;
        // Advance moving marker slightly on each poll
        _animationProgress = (_animationProgress + 0.08) > 0.9 ? 0.45 : (_animationProgress + 0.08);
      });
    }
  }

  Future<void> _contactCourier(String phone, String name) async {
    final cleanPhone = phone.replaceAll(RegExp(r'[^0-9]'), '');
    final formatted = cleanPhone.startsWith('0') ? '62${cleanPhone.substring(1)}' : cleanPhone;
    final uri = Uri.parse('https://wa.me/$formatted?text=Halo%20$name,%20saya%20pembeli%20pesanan%20${widget.invoiceNumber}');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        backgroundColor: AppTheme.accentNavy,
        elevation: 0,
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Live Map Tracking 🗺️', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w800, color: Colors.white)),
            Text(widget.invoiceNumber, style: const TextStyle(fontSize: 11, color: Colors.white60)),
          ],
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: AppTheme.primary))
          : RefreshIndicator(
              onRefresh: () => _loadTrackingData(isSilent: false),
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                child: Column(
                  children: [
                    // 1. Live Visual Map Canvas
                    Container(
                      height: 250,
                      width: double.infinity,
                      decoration: const BoxDecoration(
                        color: Color(0xFF0F172A),
                      ),
                      child: Stack(
                        children: [
                          Positioned.fill(
                            child: CustomPaint(
                              painter: LiveMapPainter(
                                progress: _animationProgress,
                                pulseScale: _pulseController.value,
                              ),
                            ),
                          ),

                          // Live Badge Overlay
                          Positioned(
                            top: 16,
                            left: 16,
                            child: Container(
                              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                              decoration: BoxDecoration(
                                color: Colors.black.withOpacity(0.65),
                                borderRadius: BorderRadius.circular(20),
                                border: Border.all(color: Colors.cyanAccent.withOpacity(0.5)),
                              ),
                              child: const Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  Icon(Icons.radar_rounded, color: Colors.cyanAccent, size: 14),
                                  SizedBox(width: 6),
                                  Text(
                                    'Pelacakan GPS Real-Time',
                                    style: TextStyle(color: Colors.white, fontSize: 10.5, fontWeight: FontWeight.w800),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),

                    Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // 2. Courier Profile & Contact Card
                          if (_trackingData != null && _trackingData!['courier_info'] != null)
                            Container(
                              padding: const EdgeInsets.all(16),
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(16),
                                border: Border.all(color: AppTheme.border),
                                boxShadow: [
                                  BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 10, offset: const Offset(0, 4)),
                                ],
                              ),
                              child: Row(
                                children: [
                                  Container(
                                    width: 48,
                                    height: 48,
                                    decoration: BoxDecoration(
                                      color: AppTheme.primaryLight,
                                      borderRadius: BorderRadius.circular(14),
                                      border: Border.all(color: AppTheme.primary.withOpacity(0.3)),
                                    ),
                                    child: const Icon(Icons.delivery_dining_rounded, color: AppTheme.primary, size: 28),
                                  ),
                                  const SizedBox(width: 12),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          _trackingData!['courier_info']['name'] ?? 'Kurir Mitra NitipDong',
                                          style: const TextStyle(fontSize: 13.5, fontWeight: FontWeight.w800),
                                        ),
                                        const SizedBox(height: 2),
                                        Text(
                                          _trackingData!['courier_info']['vehicle'] ?? 'Driver Ekspedisi Resmi',
                                          style: const TextStyle(fontSize: 11, color: AppTheme.textMuted),
                                        ),
                                        const SizedBox(height: 4),
                                        Row(
                                          children: [
                                            const Icon(Icons.star_rounded, color: Colors.amber, size: 14),
                                            const SizedBox(width: 4),
                                            Text(
                                              '${_trackingData!['courier_info']['rating'] ?? '4.95'} • 1.2k+ Pengantaran',
                                              style: const TextStyle(fontSize: 10.5, fontWeight: FontWeight.w700, color: AppTheme.textSecondary),
                                            ),
                                          ],
                                        ),
                                      ],
                                    ),
                                  ),
                                  IconButton(
                                    icon: Container(
                                      padding: const EdgeInsets.all(8),
                                      decoration: BoxDecoration(color: Colors.green.shade50, borderRadius: BorderRadius.circular(10)),
                                      child: const Icon(Icons.chat_bubble_outline_rounded, color: Colors.green, size: 20),
                                    ),
                                    onPressed: () => _contactCourier(
                                      _trackingData!['courier_info']['phone'] ?? '081234567890',
                                      _trackingData!['courier_info']['name'] ?? 'Kurir',
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          const SizedBox(height: 16),

                          // 3. Pickup, Hub DC & Destination Points
                          Container(
                            padding: const EdgeInsets.all(14),
                            decoration: BoxDecoration(
                              color: Colors.white,
                              borderRadius: BorderRadius.circular(14),
                              border: Border.all(color: AppTheme.border),
                            ),
                            child: Column(
                              children: [
                                Row(
                                  children: [
                                    const Icon(Icons.storefront_rounded, color: AppTheme.primary, size: 18),
                                    const SizedBox(width: 8),
                                    Expanded(
                                      child: Text(
                                        'Toko: ${_trackingData?['locations']?['store']?['name'] ?? 'Toko Official'}',
                                        style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700),
                                      ),
                                    ),
                                  ],
                                ),
                                const Padding(
                                  padding: EdgeInsets.symmetric(vertical: 6),
                                  child: Divider(height: 1, indent: 26),
                                ),
                                Row(
                                  children: [
                                    const Icon(Icons.warehouse_rounded, color: Colors.amber, size: 18),
                                    const SizedBox(width: 8),
                                    Expanded(
                                      child: Text(
                                        'Gudang Hub: ${_trackingData?['locations']?['hub_warehouse']?['name'] ?? 'NitipDong Hub DC Regional'}',
                                        style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: Colors.amber),
                                      ),
                                    ),
                                  ],
                                ),
                                const Padding(
                                  padding: EdgeInsets.symmetric(vertical: 6),
                                  child: Divider(height: 1, indent: 26),
                                ),
                                Row(
                                  children: [
                                    const Icon(Icons.location_on_rounded, color: Colors.redAccent, size: 18),
                                    const SizedBox(width: 8),
                                    Expanded(
                                      child: Text(
                                        'Tujuan: ${_trackingData?['locations']?['destination']?['address'] ?? 'Alamat Anda'}',
                                        style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700),
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    ),
                                  ],
                                ),
                              ],
                            ),
                          ),
                          const SizedBox(height: 16),

                          // 3.5. Proof of Delivery Card (If available)
                          if (_trackingData != null && _trackingData!['delivery_proof_url'] != null)
                            Container(
                              margin: const EdgeInsets.only(bottom: 16),
                              padding: const EdgeInsets.all(14),
                              decoration: BoxDecoration(
                                color: Colors.green.shade50,
                                borderRadius: BorderRadius.circular(14),
                                border: Border.all(color: Colors.green.shade200),
                              ),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                    children: [
                                      const Row(
                                        children: [
                                          Icon(Icons.camera_alt_rounded, color: Colors.green, size: 16),
                                          SizedBox(width: 6),
                                          Text('Foto Bukti Penerimaan Barang', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w800, color: Colors.green)),
                                        ],
                                      ),
                                      Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                        decoration: BoxDecoration(color: Colors.green, borderRadius: BorderRadius.circular(6)),
                                        child: const Text('TERKIRIM', style: TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.w900)),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 8),
                                  ClipRRect(
                                    borderRadius: BorderRadius.circular(10),
                                    child: Image.network(
                                      _trackingData!['delivery_proof_url'],
                                      height: 160,
                                      width: double.infinity,
                                      fit: BoxFit.cover,
                                      errorBuilder: (_, __, ___) => const SizedBox(),
                                    ),
                                  ),
                                  if (_trackingData!['delivery_notes'] != null) ...[
                                    const SizedBox(height: 6),
                                    Text(
                                      'Catatan: ${_trackingData!['delivery_notes']}',
                                      style: TextStyle(fontSize: 11, color: Colors.green.shade900, fontStyle: FontStyle.italic),
                                    ),
                                  ],
                                ],
                              ),
                            ),

                          // 4. Timeline Section
                          const Text('Timeline Perjalanan Paket ⏱️', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w800)),
                          const SizedBox(height: 12),

                          if (_trackingData != null && _trackingData!['timeline'] != null)
                            ListView.builder(
                              shrinkWrap: true,
                              physics: const NeverScrollableScrollPhysics(),
                              itemCount: (_trackingData!['timeline'] as List).length,
                              itemBuilder: (context, idx) {
                                final step = _trackingData!['timeline'][idx];
                                final isDone = step['is_done'] == true;
                                final isLast = idx == (_trackingData!['timeline'] as List).length - 1;

                                return Row(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Column(
                                      children: [
                                        Container(
                                          width: 22,
                                          height: 22,
                                          decoration: BoxDecoration(
                                            color: isDone ? AppTheme.primary : Colors.grey.shade300,
                                            shape: BoxShape.circle,
                                          ),
                                          child: Icon(
                                            isDone ? Icons.check : Icons.circle,
                                            size: 12,
                                            color: Colors.white,
                                          ),
                                        ),
                                        if (!isLast)
                                          Container(
                                            width: 2,
                                            height: 40,
                                            color: isDone ? AppTheme.primary : Colors.grey.shade300,
                                          ),
                                      ],
                                    ),
                                    const SizedBox(width: 12),
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            step['title'] ?? '',
                                            style: TextStyle(
                                              fontSize: 12.5,
                                              fontWeight: FontWeight.w800,
                                              color: isDone ? AppTheme.textPrimary : AppTheme.textMuted,
                                            ),
                                          ),
                                          const SizedBox(height: 2),
                                          Text(
                                            step['description'] ?? '',
                                            style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary),
                                          ),
                                          if (step['time'] != null)
                                            Text(
                                              step['time'],
                                              style: const TextStyle(fontSize: 10, color: AppTheme.textMuted, fontStyle: FontStyle.italic),
                                            ),
                                          const SizedBox(height: 12),
                                        ],
                                      ),
                                    ),
                                  ],
                                );
                              },
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

class LiveMapPainter extends CustomPainter {
  final double progress;
  final double pulseScale;

  LiveMapPainter({required this.progress, required this.pulseScale});

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

    // Radar Ripple
    final pulseRadius = 14.0 + (pulseScale * 8.0);
    canvas.drawCircle(courierPos, pulseRadius, Paint()..color = Colors.cyanAccent.withOpacity(0.3 * (1.0 - pulseScale)));
    
    canvas.drawCircle(courierPos, 12, Paint()..color = const Color(0xFF0F172A));
    canvas.drawCircle(courierPos, 8, Paint()..color = Colors.cyanAccent);
    canvas.drawCircle(courierPos, 3, Paint()..color = Colors.white);
  }

  @override
  bool shouldRepaint(covariant LiveMapPainter oldDelegate) => true;
}
