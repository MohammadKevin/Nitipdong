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
    // Road Path
    final roadPaint = Paint()
      ..color = Colors.white.withOpacity(0.12)
      ..strokeWidth = 14
      ..style = PaintingStyle.stroke
      ..strokeCap = StrokeCap.round;

    final glowPaint = Paint()
      ..color = Colors.cyanAccent.withOpacity(0.5)
      ..strokeWidth = 4
      ..style = PaintingStyle.stroke;

    final start = Offset(size.width * 0.15, size.height * 0.75);
    final end = Offset(size.width * 0.85, size.height * 0.25);
    final cp1 = Offset(size.width * 0.4, size.height * 0.9);
    final cp2 = Offset(size.width * 0.6, size.height * 0.1);

    final path = Path()
      ..moveTo(start.dx, start.dy)
      ..cubicTo(cp1.dx, cp1.dy, cp2.dx, cp2.dy, end.dx, end.dy);

    canvas.drawPath(path, roadPaint);
    canvas.drawPath(path, glowPaint);

    // Store Marker (Origin)
    canvas.drawCircle(start, 12, Paint()..color = const Color(0xFF0E7490));
    canvas.drawCircle(start, 5, Paint()..color = Colors.white);

    // Home Marker (Destination)
    canvas.drawCircle(end, 12, Paint()..color = Colors.redAccent);
    canvas.drawCircle(end, 5, Paint()..color = Colors.white);

    // Moving Courier Marker with Radar Pulse
    final courierX = start.dx + (end.dx - start.dx) * progress;
    final courierY = start.dy + (end.dy - start.dy) * progress;
    final courierPos = Offset(courierX, courierY);

    // Radar Ripple
    canvas.drawCircle(courierPos, 14 + (pulseScale * 8), Paint()..color = Colors.cyanAccent.withOpacity(0.25 * (1 - pulseScale)));
    canvas.drawCircle(courierPos, 10, Paint()..color = Colors.cyanAccent);
    canvas.drawCircle(courierPos, 4, Paint()..color = Colors.white);
  }

  @override
  bool shouldRepaint(covariant LiveMapPainter oldDelegate) => true;
}
