import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:intl/intl.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../theme/app_theme.dart';
import '../../services/api_service.dart';
import 'payment_success_screen.dart';

class PaymentDirectScreen extends StatefulWidget {
  final dynamic orderId;
  final String invoiceNumber;
  final double totalAmount;
  final String paymentType; // 'qris', 'bank_transfer', 'echannel', 'shopeepay'
  final String? bank;
  final String? qrImageUrl;
  final String? qrString;
  final String? vaNumber;
  final String? billerCode;
  final String? billKey;
  final String? expiryTime;
  final List<dynamic> instructions;

  const PaymentDirectScreen({
    Key? key,
    required this.orderId,
    required this.invoiceNumber,
    required this.totalAmount,
    required this.paymentType,
    this.bank,
    this.qrImageUrl,
    this.qrString,
    this.vaNumber,
    this.billerCode,
    this.billKey,
    this.expiryTime,
    this.instructions = const [],
  }) : super(key: key);

  @override
  State<PaymentDirectScreen> createState() => _PaymentDirectScreenState();
}

class _PaymentDirectScreenState extends State<PaymentDirectScreen> {
  Timer? _countdownTimer;
  Timer? _pollingTimer;
  Duration _remainingTime = const Duration(hours: 24);
  bool _isCheckingStatus = false;
  bool _isSimulating = false;

  @override
  void initState() {
    super.initState();
    _initCountdown();
    _startAutoPolling();
  }

  @override
  void dispose() {
    _countdownTimer?.cancel();
    _pollingTimer?.cancel();
    super.dispose();
  }

  void _initCountdown() {
    if (widget.expiryTime != null) {
      try {
        final expiry = DateTime.parse(widget.expiryTime!);
        final diff = expiry.difference(DateTime.now());
        if (diff.isNegative) {
          _remainingTime = Duration.zero;
        } else {
          _remainingTime = diff;
        }
      } catch (_) {
        _remainingTime = const Duration(hours: 24);
      }
    }

    _countdownTimer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (!mounted) return;
      if (_remainingTime.inSeconds > 0) {
        setState(() {
          _remainingTime = _remainingTime - const Duration(seconds: 1);
        });
      } else {
        timer.cancel();
      }
    });
  }

  void _startAutoPolling() {
    // Poll status every 3 seconds to catch webhook or settlement
    _pollingTimer = Timer.periodic(const Duration(seconds: 3), (_) {
      _pollPaymentStatus();
    });
  }

  Future<void> _pollPaymentStatus() async {
    if (!mounted) return;
    final res = await ApiService.checkPaymentStatus(widget.orderId);
    if (res['is_paid'] == true && mounted) {
      _pollingTimer?.cancel();
      _countdownTimer?.cancel();
      _navigateToSuccess();
    }
  }

  void _navigateToSuccess() {
    Navigator.pushReplacement(
      context,
      MaterialPageRoute(
        builder: (_) => PaymentSuccessScreen(
          orderId: widget.orderId,
          invoiceNumber: widget.invoiceNumber,
          totalAmount: widget.totalAmount,
          paymentMethod: _getReadablePaymentName(),
        ),
      ),
    );
  }

  String _formatCurrency(double amount) {
    final formatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    return formatter.format(amount);
  }

  String _formatDuration(Duration d) {
    String twoDigits(int n) => n.toString().padLeft(2, '0');
    final hours = twoDigits(d.inHours);
    final minutes = twoDigits(d.inMinutes.remainder(60));
    final seconds = twoDigits(d.inSeconds.remainder(60));
    return '$hours : $minutes : $seconds';
  }

  void _copyToClipboard(String text, String message) {
    Clipboard.setData(ClipboardData(text: text));
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Row(
          children: [
            const Icon(Icons.check_circle, color: Colors.white, size: 18),
            const SizedBox(width: 8),
            Text(message),
          ],
        ),
        backgroundColor: const Color(0xFF0F172A),
        behavior: SnackBarBehavior.floating,
        duration: const Duration(seconds: 2),
      ),
    );
  }

  String _getReadablePaymentName() {
    if (widget.paymentType == 'qris') return 'QRIS Instant';
    if (widget.paymentType == 'bank_transfer') return '${widget.bank?.toUpperCase() ?? 'BCA'} Virtual Account';
    if (widget.paymentType == 'echannel') return 'Mandiri Bill Payment';
    if (widget.paymentType == 'shopeepay') return 'ShopeePay';
    return 'Midtrans Payment';
  }

  Future<void> _handleManualCheck() async {
    setState(() => _isCheckingStatus = true);
    final res = await ApiService.checkPaymentStatus(widget.orderId);
    setState(() => _isCheckingStatus = false);

    if (!mounted) return;

    if (res['is_paid'] == true) {
      _navigateToSuccess();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Pembayaran belum terdeteksi. Silakan selesaikan pembayaran.'),
          backgroundColor: Colors.orange,
          behavior: SnackBarBehavior.floating,
        ),
      );
    }
  }

  Future<void> _handleSimulatePaid() async {
    setState(() => _isSimulating = true);
    final success = await ApiService.simulatePaymentSuccess(widget.orderId);
    setState(() => _isSimulating = false);

    if (!mounted) return;

    if (success) {
      _navigateToSuccess();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Gagal melakukan simulasi pembayaran.'),
          backgroundColor: Colors.red,
          behavior: SnackBarBehavior.floating,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final isQris = widget.paymentType == 'qris';
    final isMandiri = widget.paymentType == 'echannel';

    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: Text(
          'Pembayaran ${_getReadablePaymentName()}',
          style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
        ),
        backgroundColor: Colors.white,
        elevation: 0.5,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios, color: Color(0xFF0F172A), size: 18),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // ══════════════════════════════════════════════
            // 1. COUNTDOWN & TOTAL AMOUNT BANNER
            // ══════════════════════════════════════════════
            Container(
              padding: const EdgeInsets.all(18),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFF0B1528), Color(0xFF1E293B)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.08),
                    blurRadius: 12,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text(
                        'Batas Waktu Pembayaran',
                        style: TextStyle(fontSize: 12, color: Colors.white70),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.amber.shade900.withOpacity(0.4),
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(color: Colors.amber.shade400, width: 1),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            const Icon(Icons.timer_outlined, color: Colors.amber, size: 14),
                            const SizedBox(width: 4),
                            Text(
                              _formatDuration(_remainingTime),
                              style: const TextStyle(
                                color: Colors.amber,
                                fontWeight: FontWeight.bold,
                                fontSize: 13,
                                letterSpacing: 0.5,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const Divider(height: 24, color: Colors.white24),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Total Tagihan',
                            style: TextStyle(fontSize: 12, color: Colors.white70),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            _formatCurrency(widget.totalAmount),
                            style: const TextStyle(
                              fontSize: 20,
                              fontWeight: FontWeight.bold,
                              color: Colors.white,
                            ),
                          ),
                        ],
                      ),
                      TextButton.icon(
                        onPressed: () => _copyToClipboard(
                          widget.totalAmount.toInt().toString(),
                          'Nominal ${_formatCurrency(widget.totalAmount)} berhasil disalin!',
                        ),
                        icon: const Icon(Icons.copy_rounded, size: 16, color: AppTheme.accentCyan),
                        label: const Text(
                          'Salin',
                          style: TextStyle(color: AppTheme.accentCyan, fontWeight: FontWeight.bold, fontSize: 12),
                        ),
                        style: TextButton.styleFrom(
                          backgroundColor: AppTheme.accentCyan.withOpacity(0.15),
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // ══════════════════════════════════════════════
            // 2. PAYMENT DETAILS (QRIS / VIRTUAL ACCOUNT)
            // ══════════════════════════════════════════════
            if (isQris) ...[
              _buildQrisSection(),
            ] else if (isMandiri) ...[
              _buildMandiriSection(),
            ] else ...[
              _buildVirtualAccountSection(),
            ],

            const SizedBox(height: 24),

            // ══════════════════════════════════════════════
            // 3. STEP-BY-STEP INSTRUCTIONS ACCORDION
            // ══════════════════════════════════════════════
            _buildInstructionsSection(),

            const SizedBox(height: 28),

            // ══════════════════════════════════════════════
            // 4. ACTION BUTTONS (CHECK STATUS & SIMULATION)
            // ══════════════════════════════════════════════
            SizedBox(
              height: 48,
              child: ElevatedButton(
                onPressed: _isCheckingStatus ? null : _handleManualCheck,
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.primaryNavy,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  elevation: 0,
                ),
                child: _isCheckingStatus
                    ? const SizedBox(
                        width: 20,
                        height: 20,
                        child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2),
                      )
                    : const Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(Icons.refresh_rounded, size: 18),
                          SizedBox(width: 8),
                          Text(
                            'Cek Status Pembayaran',
                            style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: Colors.white),
                          ),
                        ],
                      ),
              ),
            ),
            const SizedBox(height: 12),

            // ⚡ Sandbox Demo Helper Button
            SizedBox(
              height: 44,
              child: OutlinedButton(
                onPressed: _isSimulating ? null : _handleSimulatePaid,
                style: OutlinedButton.styleFrom(
                  side: const BorderSide(color: Color(0xFF0D9488)),
                  backgroundColor: const Color(0xFFF0FDFA),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                child: _isSimulating
                    ? const SizedBox(
                        width: 18,
                        height: 18,
                        child: CircularProgressIndicator(color: Color(0xFF0D9488), strokeWidth: 2),
                      )
                    : const Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(Icons.bolt, color: Color(0xFF0D9488), size: 18),
                          SizedBox(width: 6),
                          Text(
                            '⚡ Simulasi Bayar Lunas (Testing)',
                            style: TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: Color(0xFF0D9488)),
                          ),
                        ],
                      ),
              ),
            ),
            const SizedBox(height: 32),
          ],
        ),
      ),
    );
  }

  // ══════════════════════════════════════════════════
  // WIDGET BUILDERS
  // ══════════════════════════════════════════════════

  Widget _buildQrisSection() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: Colors.red.shade50,
                  borderRadius: BorderRadius.circular(6),
                  border: Border.all(color: Colors.red.shade200),
                ),
                child: const Row(
                  children: [
                    Icon(Icons.qr_code, size: 16, color: Colors.red),
                    SizedBox(width: 4),
                    Text(
                      'QRIS NASIONAL (GPN)',
                      style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.red),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),

          // QR Code Card
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: Colors.grey.shade300, width: 2),
              boxShadow: [
                BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 8),
              ],
            ),
            child: widget.qrImageUrl != null
                ? CachedNetworkImage(
                    imageUrl: widget.qrImageUrl!,
                    width: 220,
                    height: 220,
                    fit: BoxFit.contain,
                    placeholder: (_, __) => const SizedBox(
                      width: 220,
                      height: 220,
                      child: Center(child: CircularProgressIndicator(strokeWidth: 2)),
                    ),
                    errorWidget: (_, __, ___) => const Icon(Icons.qr_code, size: 180, color: Colors.grey),
                  )
                : const Icon(Icons.qr_code, size: 180, color: Colors.grey),
          ),
          const SizedBox(height: 16),

          const Text(
            'Scan QR Code di atas menggunakan aplikasi:\nGoPay, OVO, ShopeePay, DANA, BCA, BRI, Mandiri, dll.',
            style: TextStyle(fontSize: 12, color: Color(0xFF64748B), height: 1.4),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 12),

          OutlinedButton.icon(
            onPressed: () => _copyToClipboard(
              widget.qrString ?? widget.invoiceNumber,
              'Kode QRIS berhasil disalin!',
            ),
            icon: const Icon(Icons.copy, size: 14),
            label: const Text('Salin String QRIS', style: TextStyle(fontSize: 12)),
            style: OutlinedButton.styleFrom(
              foregroundColor: AppTheme.primaryNavy,
              side: BorderSide(color: Colors.grey.shade300),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildVirtualAccountSection() {
    final va = widget.vaNumber ?? '1234500000000000';
    final bankName = widget.bank?.toUpperCase() ?? 'BCA';

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'Virtual Account $bankName',
                style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                decoration: BoxDecoration(
                  color: AppTheme.primaryNavy.withOpacity(0.08),
                  borderRadius: BorderRadius.circular(6),
                ),
                child: Text(
                  bankName,
                  style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppTheme.primaryNavy),
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          const Text(
            'Nomor Virtual Account:',
            style: TextStyle(fontSize: 12, color: Color(0xFF64748B)),
          ),
          const SizedBox(height: 6),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: BoxDecoration(
              color: const Color(0xFFF1F5F9),
              borderRadius: BorderRadius.circular(10),
              border: Border.all(color: const Color(0xFFCBD5E1)),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                SelectableText(
                  va,
                  style: const TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                    letterSpacing: 1.2,
                    color: Color(0xFF0F172A),
                    fontFamily: 'monospace',
                  ),
                ),
                IconButton(
                  onPressed: () => _copyToClipboard(va, 'Nomor VA $va berhasil disalin!'),
                  icon: const Icon(Icons.copy_rounded, color: AppTheme.primaryNavy, size: 20),
                  tooltip: 'Salin No. VA',
                  padding: EdgeInsets.zero,
                  constraints: const BoxConstraints(),
                ),
              ],
            ),
          ),
          const SizedBox(height: 12),
          const Text(
            'Proses verifikasi pembayaran berlangsung otomatis tanpa perlu konfirmasi manual.',
            style: TextStyle(fontSize: 11, color: Color(0xFF64748B)),
          ),
        ],
      ),
    );
  }

  Widget _buildMandiriSection() {
    final biller = widget.billerCode ?? '70012';
    final billKey = widget.billKey ?? '8800000000';

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'Mandiri Bill Payment',
                style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
              ),
              Text(
                'MANDIRI',
                style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.blue),
              ),
            ],
          ),
          const SizedBox(height: 16),
          _buildCopyableField('Kode Perusahaan / Biller Code', biller),
          const SizedBox(height: 12),
          _buildCopyableField('Nomor Tagihan / Bill Key', billKey),
        ],
      ),
    );
  }

  Widget _buildCopyableField(String label, String value) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontSize: 11, color: Color(0xFF64748B))),
        const SizedBox(height: 4),
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
          decoration: BoxDecoration(
            color: const Color(0xFFF1F5F9),
            borderRadius: BorderRadius.circular(8),
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                value,
                style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold, fontFamily: 'monospace'),
              ),
              InkWell(
                onTap: () => _copyToClipboard(value, '$label $value berhasil disalin!'),
                child: const Icon(Icons.copy, size: 16, color: AppTheme.primaryNavy),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildInstructionsSection() {
    if (widget.instructions.isEmpty) {
      return const SizedBox.shrink();
    }

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Padding(
            padding: EdgeInsets.fromLTRB(18, 18, 18, 8),
            child: Text(
              'Cara Pembayaran',
              style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
            ),
          ),
          ...widget.instructions.map((guide) {
            final title = guide['title'] ?? 'Panduan';
            final steps = (guide['steps'] as List<dynamic>?) ?? [];

            return Theme(
              data: Theme.of(context).copyWith(dividerColor: Colors.transparent),
              child: ExpansionTile(
                title: Text(
                  title,
                  style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: Color(0xFF334155)),
                ),
                children: [
                  Padding(
                    padding: const EdgeInsets.fromLTRB(18, 0, 18, 16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: steps.asMap().entries.map((entry) {
                        return Padding(
                          padding: const EdgeInsets.only(bottom: 8),
                          child: Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Container(
                                width: 20,
                                height: 20,
                                alignment: Alignment.center,
                                decoration: BoxDecoration(
                                  color: AppTheme.primaryNavy.withOpacity(0.08),
                                  shape: BoxShape.circle,
                                ),
                                child: Text(
                                  '${entry.key + 1}',
                                  style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: AppTheme.primaryNavy),
                                ),
                              ),
                              const SizedBox(width: 10),
                              Expanded(
                                child: Text(
                                  entry.value.toString(),
                                  style: const TextStyle(fontSize: 12, color: Color(0xFF475569), height: 1.35),
                                ),
                              ),
                            ],
                          ),
                        );
                      }).toList(),
                    ),
                  ),
                ],
              ),
            );
          }).toList(),
        ],
      ),
    );
  }
}
