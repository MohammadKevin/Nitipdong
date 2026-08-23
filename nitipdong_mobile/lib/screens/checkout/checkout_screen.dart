import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../providers/cart_provider.dart';
import '../../providers/auth_provider.dart';
import '../../services/api_service.dart';
import '../orders/orders_screen.dart';
import 'payment_direct_screen.dart';

class CheckoutScreen extends StatefulWidget {
  final List<int>? selectedCartIds;

  const CheckoutScreen({Key? key, this.selectedCartIds}) : super(key: key);

  @override
  State<CheckoutScreen> createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends State<CheckoutScreen> {
  final _addressController = TextEditingController();
  final _voucherController = TextEditingController();

  String _selectedPayment = 'QRIS Instant (Semua Bank & E-Wallet)';
  String _selectedCourier = 'J&T Express (Gratis Ongkir Rp0)';
  bool _isProcessing = false;
  double _discountAmount = 0.0;
  String? _appliedVoucherCode;

  final List<Map<String, dynamic>> _paymentOptions = [
    {'name': 'QRIS Instant (Semua Bank & E-Wallet)', 'icon': Icons.qr_code_scanner_rounded, 'badge': 'Instan'},
    {'name': 'BCA Virtual Account', 'icon': Icons.account_balance_rounded, 'badge': 'Otomatis'},
    {'name': 'Mandiri Virtual Account', 'icon': Icons.account_balance_rounded, 'badge': 'Otomatis'},
    {'name': 'BRI Virtual Account', 'icon': Icons.account_balance_rounded, 'badge': 'Otomatis'},
    {'name': 'GoPay / ShopeePay / DANA', 'icon': Icons.phone_android_rounded, 'badge': 'E-Wallet'},
    {'name': 'Saldo NitipPay', 'icon': Icons.account_balance_wallet_outlined, 'badge': 'Bebas Admin'},
  ];

  final List<Map<String, dynamic>> _courierOptions = [
    {'name': 'J&T Express (Gratis Ongkir Rp0)', 'etd': '1-2 Hari', 'fee': 0},
    {'name': 'JNE Regular (Gratis Ongkir Rp0)', 'etd': '2-3 Hari', 'fee': 0},
    {'name': 'SiCepat Kilat (Gratis Ongkir Rp0)', 'etd': '1-2 Hari', 'fee': 0},
    {'name': 'Instant / SameDay Kurir', 'etd': 'Hari Ini', 'fee': 0},
  ];

  @override
  void initState() {
    super.initState();
    _loadAddress();
  }

  Future<void> _loadAddress() async {
    final data = await ApiService.getSavedAddress();
    if (mounted) {
      final addr = data['full_address'] ?? '';
      final name = data['recipient_name'] ?? '';
      final phone = data['phone'] ?? '';
      if (addr.isNotEmpty) {
        if (name.isNotEmpty && phone.isNotEmpty && !addr.contains(name)) {
          _addressController.text = '$addr ($name - $phone)';
        } else {
          _addressController.text = addr;
        }
      }
    }
  }

  String _formatCurrency(double amount) {
    final formatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    return formatter.format(amount);
  }

  void _applyVoucher(double subtotal) {
    final code = _voucherController.text.trim().toUpperCase();
    if (code == 'NITIPHEMAT') {
      setState(() {
        _discountAmount = subtotal * 0.15 > 50000 ? 50000 : subtotal * 0.15;
        _appliedVoucherCode = code;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Kupon NITIPHEMAT berhasil digunakan! Diskon ${_formatCurrency(_discountAmount)} 🎉'),
          backgroundColor: Colors.green,
          behavior: SnackBarBehavior.floating,
        ),
      );
    } else if (code == 'ONGKIRNOL') {
      setState(() {
        _discountAmount = 15000;
        _appliedVoucherCode = code;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Kupon ONGKIRNOL aktif! Gratis ongkos kirim seluruh Indonesia. 🚚'),
          backgroundColor: Colors.green,
          behavior: SnackBarBehavior.floating,
        ),
      );
    } else if (code == 'FLASHSALE20') {
      setState(() {
        _discountAmount = subtotal * 0.20;
        _appliedVoucherCode = code;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Kupon FLASHSALE20 aktif! Cashback ${_formatCurrency(_discountAmount)} 🎉'),
          backgroundColor: Colors.green,
          behavior: SnackBarBehavior.floating,
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Kode voucher tidak valid atau telah habis masa berlakunya.'),
          backgroundColor: Colors.redAccent,
          behavior: SnackBarBehavior.floating,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final cartProvider = Provider.of<CartProvider>(context);

    // Calculate subtotal for checked items or entire cart
    double subtotal = 0.0;
    if (widget.selectedCartIds != null && widget.selectedCartIds!.isNotEmpty) {
      for (var item in cartProvider.items) {
        if (widget.selectedCartIds!.contains(item.id)) {
          subtotal += item.subtotal;
        }
      }
    } else {
      subtotal = cartProvider.subtotal;
    }

    final totalAmount = (subtotal - _discountAmount) > 0 ? (subtotal - _discountAmount) : 0.0;

    return Scaffold(
      appBar: AppBar(title: const Text('Checkout Pembelian')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // 1. SHIPPING ADDRESS CARD
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
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Row(
                        children: [
                          Icon(Icons.location_on_outlined, color: AppTheme.primary, size: 20),
                          SizedBox(width: 6),
                          Text('Alamat Pengiriman', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 13)),
                        ],
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                        decoration: BoxDecoration(color: AppTheme.primaryLight, borderRadius: BorderRadius.circular(4)),
                        child: const Text('Utama', style: TextStyle(fontSize: 9, fontWeight: FontWeight.w800, color: AppTheme.primaryDark)),
                      ),
                    ],
                  ),
                  const SizedBox(height: 10),
                  TextField(
                    controller: _addressController,
                    maxLines: 2,
                    style: const TextStyle(fontSize: 12),
                    decoration: InputDecoration(
                      hintText: 'Alamat lengkap pengiriman...',
                      contentPadding: const EdgeInsets.all(12),
                      fillColor: Colors.grey.shade50,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 12),

            // 2. SHIPPING COURIER SELECTION
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
                  const Row(
                    children: [
                      Icon(Icons.local_shipping_outlined, color: AppTheme.primary, size: 20),
                      SizedBox(width: 6),
                      Text('Opsi Pengiriman (Kurir)', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 13)),
                    ],
                  ),
                  const SizedBox(height: 10),
                  ..._courierOptions.map((c) {
                    final isSelected = _selectedCourier == c['name'];
                    return InkWell(
                      onTap: () => setState(() => _selectedCourier = c['name']),
                      borderRadius: BorderRadius.circular(10),
                      child: Container(
                        margin: const EdgeInsets.only(bottom: 6),
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                        decoration: BoxDecoration(
                          color: isSelected ? AppTheme.primaryLight.withOpacity(0.4) : Colors.transparent,
                          borderRadius: BorderRadius.circular(10),
                          border: Border.all(color: isSelected ? AppTheme.primary : Colors.grey.shade200),
                        ),
                        child: Row(
                          children: [
                            Icon(
                              isSelected ? Icons.radio_button_checked : Icons.radio_button_off,
                              color: isSelected ? AppTheme.primary : Colors.grey,
                              size: 18,
                            ),
                            const SizedBox(width: 10),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(c['name'], style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
                                  Text('Estimasi tiba: ${c['etd']}', style: const TextStyle(fontSize: 10, color: AppTheme.textMuted)),
                                ],
                              ),
                            ),
                            const Text('Rp 0', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w900, color: AppTheme.success)),
                          ],
                        ),
                      ),
                    );
                  }).toList(),
                ],
              ),
            ),
            const SizedBox(height: 12),

            // 3. PAYMENT METHOD SELECTION
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
                  const Row(
                    children: [
                      Icon(Icons.payment_rounded, color: AppTheme.primary, size: 20),
                      SizedBox(width: 6),
                      Text('Metode Pembayaran', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 13)),
                    ],
                  ),
                  const SizedBox(height: 10),
                  ..._paymentOptions.map((p) {
                    final isSelected = _selectedPayment == p['name'];
                    return InkWell(
                      onTap: () => setState(() => _selectedPayment = p['name']),
                      borderRadius: BorderRadius.circular(10),
                      child: Container(
                        margin: const EdgeInsets.only(bottom: 6),
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                        decoration: BoxDecoration(
                          color: isSelected ? AppTheme.primaryLight.withOpacity(0.4) : Colors.transparent,
                          borderRadius: BorderRadius.circular(10),
                          border: Border.all(color: isSelected ? AppTheme.primary : Colors.grey.shade200),
                        ),
                        child: Row(
                          children: [
                            Icon(p['icon'], color: isSelected ? AppTheme.primaryDark : AppTheme.textSecondary, size: 20),
                            const SizedBox(width: 10),
                            Expanded(
                              child: Text(p['name'], style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                              decoration: BoxDecoration(
                                color: isSelected ? AppTheme.primary : Colors.grey.shade100,
                                borderRadius: BorderRadius.circular(4),
                              ),
                              child: Text(
                                p['badge'],
                                style: TextStyle(
                                  fontSize: 9,
                                  fontWeight: FontWeight.w700,
                                  color: isSelected ? Colors.white : Colors.grey.shade600,
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    );
                  }).toList(),
                ],
              ),
            ),
            const SizedBox(height: 12),

            // 4. VOUCHER & PROMO INPUT
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
                  const Row(
                    children: [
                      Icon(Icons.confirmation_number_outlined, color: AppTheme.primary, size: 20),
                      SizedBox(width: 6),
                      Text('Voucher & Kode Promo', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 13)),
                    ],
                  ),
                  const SizedBox(height: 10),
                  _appliedVoucherCode != null
                      ? Container(
                          decoration: BoxDecoration(
                            color: Colors.green.shade50,
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(color: Colors.green.shade200),
                          ),
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                          child: Row(
                            children: [
                              Container(
                                padding: const EdgeInsets.all(8),
                                decoration: BoxDecoration(
                                  color: Colors.green.shade100,
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: const Icon(Icons.confirmation_number_rounded, color: Colors.green, size: 18),
                              ),
                              const SizedBox(width: 10),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      _appliedVoucherCode!,
                                      style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 12, color: Colors.green),
                                    ),
                                    Text(
                                      'Diskon ${_formatCurrency(_discountAmount)}',
                                      style: TextStyle(fontSize: 11, color: Colors.green.shade800, fontWeight: FontWeight.w700),
                                    ),
                                  ],
                                ),
                              ),
                              TextButton(
                                onPressed: () {
                                  setState(() {
                                    _appliedVoucherCode = null;
                                    _discountAmount = 0.0;
                                    _voucherController.clear();
                                  });
                                },
                                child: const Text('Hapus', style: TextStyle(color: Colors.redAccent, fontSize: 11, fontWeight: FontWeight.bold)),
                              ),
                              const SizedBox(width: 4),
                              ElevatedButton(
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: AppTheme.primary,
                                  foregroundColor: Colors.white,
                                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                                ),
                                onPressed: () => _showVoucherSelectionSheet(context, subtotal),
                                child: const Text('Ubah', style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold)),
                              ),
                            ],
                          ),
                        )
                      : InkWell(
                          onTap: () => _showVoucherSelectionSheet(context, subtotal),
                          borderRadius: BorderRadius.circular(12),
                          child: Container(
                            decoration: BoxDecoration(
                              color: Colors.grey.shade50,
                              borderRadius: BorderRadius.circular(12),
                              border: Border.all(color: Colors.grey.shade300, style: BorderStyle.solid),
                            ),
                            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Row(
                                  children: [
                                    Icon(Icons.discount_outlined, color: Colors.grey.shade600, size: 18),
                                    const SizedBox(width: 10),
                                    Text(
                                      'Pilih Voucher Diskon / Potongan Ongkir',
                                      style: TextStyle(
                                        fontSize: 12,
                                        fontWeight: FontWeight.bold,
                                        color: Colors.grey.shade700,
                                      ),
                                    ),
                                  ],
                                ),
                                const Icon(Icons.chevron_right_rounded, color: Colors.grey, size: 18),
                              ],
                            ),
                          ),
                        ),
                ],
              ),
            ),
            const SizedBox(height: 12),

            // 5. PAYMENT BREAKDOWN
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
                  const Text('Rincian Pembayaran', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 13)),
                  const SizedBox(height: 10),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('Subtotal Produk', style: TextStyle(fontSize: 12, color: AppTheme.textSecondary)),
                      Text(_formatCurrency(subtotal), style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
                    ],
                  ),
                  const SizedBox(height: 6),
                  const Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text('Biaya Pengiriman (Ongkir)', style: TextStyle(fontSize: 12, color: AppTheme.textSecondary)),
                      Text('Rp 0 (Gratis)', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppTheme.success)),
                    ],
                  ),
                  if (_discountAmount > 0) ...[
                    const SizedBox(height: 6),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text('Diskon Kupon Promo', style: TextStyle(fontSize: 12, color: Colors.green)),
                        Text('- ${_formatCurrency(_discountAmount)}', style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w800, color: Colors.green)),
                      ],
                    ),
                  ],
                  const SizedBox(height: 10),
                  const Divider(height: 1),
                  const SizedBox(height: 10),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('Total Tagihan', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w900)),
                      Text(
                        _formatCurrency(totalAmount),
                        style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w900, color: AppTheme.primaryDark),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 30),

            // 6. PAY BUTTON
            SizedBox(
              width: double.infinity,
              height: 50,
              child: ElevatedButton(
                onPressed: _isProcessing
                    ? null
                    : () async {
                        setState(() => _isProcessing = true);
                        final result = await ApiService.checkout(
                          shippingAddress: _addressController.text,
                          paymentMethod: _selectedPayment,
                          courier: _selectedCourier,
                          cartIds: widget.selectedCartIds,
                          voucherCode: _appliedVoucherCode,
                        );
                        setState(() => _isProcessing = false);

                        if (result['success'] == true && mounted) {
                          ApiService.saveAddress(fullAddress: _addressController.text.trim());
                          await cartProvider.fetchCart();

                          // Direct Midtrans Core API Charge
                          final chargeRes = await ApiService.chargeMidtransCore(
                            orderId: result['order_id'] ?? result['order_number'],
                            paymentMethod: _selectedPayment,
                          );

                          if (mounted) {
                            Navigator.pushReplacement(
                              context,
                              MaterialPageRoute(
                                builder: (_) => PaymentDirectScreen(
                                  orderId: result['order_id'] ?? result['order_number'],
                                  invoiceNumber: result['order_number'] ?? result['invoice_number'] ?? 'INV-ORDER',
                                  totalAmount: totalAmount,
                                  paymentType: chargeRes['payment_type'] ?? 'qris',
                                  bank: chargeRes['bank'],
                                  qrImageUrl: chargeRes['qr_image_url'],
                                  qrString: chargeRes['qr_string'],
                                  vaNumber: chargeRes['va_number'],
                                  billerCode: chargeRes['biller_code'],
                                  billKey: chargeRes['bill_key'],
                                  expiryTime: chargeRes['expiry_time'],
                                  instructions: chargeRes['instructions'] ?? [],
                                ),
                              ),
                            );
                          }
                        } else if (mounted) {
                          ScaffoldMessenger.of(context).showSnackBar(
                            SnackBar(
                              content: Text(result['message'] ?? 'Gagal membuat pesanan. Coba lagi.'),
                              backgroundColor: Colors.redAccent,
                            ),
                          );
                        }
                      },
                child: _isProcessing
                    ? const SizedBox(
                        width: 22,
                        height: 22,
                        child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                      )
                    : Text(
                        'Bayar ${_formatCurrency(totalAmount)}',
                        style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 14),
                      ),
              ),
            ),
            const SizedBox(height: 20),
          ],
        ),
      ),
    );
  }

  void _showPaymentInstructionDialog({
    required BuildContext context,
    required dynamic orderId,
    required String orderNumber,
    required double totalAmount,
    required String paymentMethod,
  }) {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
        title: Row(
          children: const [
            Icon(Icons.check_circle_rounded, color: AppTheme.success, size: 28),
            SizedBox(width: 8),
            Text('Pesanan Dibuat! 🎉', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w900)),
          ],
        ),
        content: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('Nomor Pesanan: $orderNumber', style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w800)),
              const SizedBox(height: 4),
              Text('Total Pembayaran: ${_formatCurrency(totalAmount)}', style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w900, color: AppTheme.primaryDark)),
              const SizedBox(height: 12),
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(color: AppTheme.primaryLight, borderRadius: BorderRadius.circular(10)),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Metode: $paymentMethod', style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: AppTheme.primaryDark)),
                    const SizedBox(height: 4),
                    const Text('Silakan selesaikan pembayaran untuk memproses pengiriman produk Anda.', style: TextStyle(fontSize: 10, color: AppTheme.textMuted)),
                  ],
                ),
              ),
            ],
          ),
        ),
        actions: [
          ElevatedButton(
            onPressed: () {
              Navigator.pop(ctx);
              Navigator.pushReplacement(
                context,
                MaterialPageRoute(builder: (context) => const OrdersScreen()),
              );
            },
            child: const Text('Lihat Pesanan Saya'),
          ),
        ],
      ),
    );
  }

  void _showVoucherSelectionSheet(BuildContext context, double subtotal) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) {
        return _VoucherSelectionWidget(
          subtotal: subtotal,
          appliedVoucherCode: _appliedVoucherCode,
          onVoucherApplied: (String code, double discount) {
            setState(() {
              _appliedVoucherCode = code;
              _discountAmount = discount;
              _voucherController.text = code;
            });
          },
        );
      },
    );
  }
}

class _VoucherSelectionWidget extends StatefulWidget {
  final double subtotal;
  final String? appliedVoucherCode;
  final Function(String code, double discount) onVoucherApplied;

  const _VoucherSelectionWidget({
    Key? key,
    required this.subtotal,
    this.appliedVoucherCode,
    required this.onVoucherApplied,
  }) : super(key: key);

  @override
  State<_VoucherSelectionWidget> createState() => _VoucherSelectionWidgetState();
}

class _VoucherSelectionWidgetState extends State<_VoucherSelectionWidget> {
  final _manualVoucherController = TextEditingController();
  List<Map<String, dynamic>> _vouchers = [];
  bool _isLoading = true;
  bool _isValidating = false;
  String? _validatingCode;

  @override
  void initState() {
    super.initState();
    _fetchVouchers();
  }

  Future<void> _fetchVouchers() async {
    if (!mounted) return;
    setState(() => _isLoading = true);
    final data = await ApiService.getAvailableVouchers();
    if (mounted) {
      setState(() {
        _vouchers = data;
        _isLoading = false;
      });
    }
  }

  String _formatCurrency(double amount) {
    final formatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    return formatter.format(amount);
  }

  Future<void> _applyVoucherCode(String code) async {
    final cleanCode = code.trim().toUpperCase();
    if (cleanCode.isEmpty) return;

    if (!mounted) return;
    setState(() {
      _isValidating = true;
      _validatingCode = cleanCode;
    });

    final res = await ApiService.validateVoucher(cleanCode);

    if (mounted) {
      setState(() {
        _isValidating = false;
        _validatingCode = null;
      });

      if (res['success'] == true) {
        final double discount = res['discount_amount'] ?? 0.0;
        widget.onVoucherApplied(cleanCode, discount);
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Voucher $cleanCode berhasil digunakan! Diskon ${_formatCurrency(discount)} 🎉'),
            backgroundColor: Colors.green,
            behavior: SnackBarBehavior.floating,
          ),
        );
      } else {
        _applyLocalFallback(cleanCode);
      }
    }
  }

  void _applyLocalFallback(String cleanCode) {
    double calculatedDiscount = 0.0;
    bool codeFound = false;

    if (cleanCode == 'NITIPHEMAT') {
      calculatedDiscount = widget.subtotal * 0.15 > 50000 ? 50000 : widget.subtotal * 0.15;
      codeFound = true;
    } else if (cleanCode == 'ONGKIRNOL') {
      calculatedDiscount = 15000;
      codeFound = true;
    } else if (cleanCode == 'FLASHSALE20') {
      calculatedDiscount = widget.subtotal * 0.20;
      codeFound = true;
    } else if (cleanCode == 'NITIPHEMAT20') {
      calculatedDiscount = widget.subtotal * 0.20 > 50000 ? 50000 : widget.subtotal * 0.20;
      codeFound = true;
    } else if (cleanCode == 'ONGKIRGRATIS') {
      calculatedDiscount = 25000;
      codeFound = true;
    } else if (cleanCode == 'GAJIANSERU50') {
      calculatedDiscount = widget.subtotal >= 200000 ? 50000 : 0.0;
      if (widget.subtotal < 200000) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Minimal belanja Rp 200.000 untuk menggunakan voucher ini.'),
            backgroundColor: Colors.redAccent,
            behavior: SnackBarBehavior.floating,
          ),
        );
        return;
      }
      codeFound = true;
    }

    if (codeFound) {
      widget.onVoucherApplied(cleanCode, calculatedDiscount);
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Voucher $cleanCode berhasil digunakan! Diskon ${_formatCurrency(calculatedDiscount)} 🎉'),
          backgroundColor: Colors.green,
          behavior: SnackBarBehavior.floating,
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Kode voucher tidak valid atau telah habis masa berlakunya.'),
          backgroundColor: Colors.redAccent,
          behavior: SnackBarBehavior.floating,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.only(
          topLeft: Radius.circular(20),
          topRight: Radius.circular(20),
        ),
      ),
      padding: EdgeInsets.only(
        top: 16,
        left: 16,
        right: 16,
        bottom: MediaQuery.of(context).viewInsets.bottom + 24,
      ),
      constraints: BoxConstraints(
        maxHeight: MediaQuery.of(context).size.height * 0.85,
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Center(
            child: Container(
              width: 40,
              height: 4,
              decoration: BoxDecoration(
                color: Colors.grey.shade300,
                borderRadius: BorderRadius.circular(2),
              ),
            ),
          ),
          const SizedBox(height: 16),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Pilih Voucher NitipDong',
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.w900,
                  color: AppTheme.accentNavy,
                ),
              ),
              IconButton(
                icon: const Icon(Icons.close, size: 20),
                onPressed: () => Navigator.pop(context),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Row(
            children: [
              Expanded(
                child: TextField(
                  controller: _manualVoucherController,
                  textCapitalization: TextCapitalization.characters,
                  style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700),
                  decoration: InputDecoration(
                    hintText: 'Masukkan kode promo secara manual...',
                    contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                    fillColor: Colors.grey.shade50,
                  ),
                ),
              ),
              const SizedBox(width: 8),
              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                ),
                onPressed: _isValidating 
                    ? null 
                    : () => _applyVoucherCode(_manualVoucherController.text),
                child: _isValidating && _validatingCode == _manualVoucherController.text.trim().toUpperCase()
                    ? const SizedBox(
                        width: 16,
                        height: 16,
                        child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                      )
                    : const Text('Pakai', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
              ),
            ],
          ),
          const SizedBox(height: 16),
          const Text(
            'Voucher Tersedia',
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.bold,
              color: AppTheme.textSecondary,
            ),
          ),
          const SizedBox(height: 8),
          Flexible(
            child: _isLoading
                ? const Padding(
                    padding: EdgeInsets.symmetric(vertical: 30),
                    child: Center(
                      child: CircularProgressIndicator(color: AppTheme.primary),
                    ),
                  )
                : _vouchers.isEmpty
                    ? Padding(
                        padding: const EdgeInsets.symmetric(vertical: 30),
                        child: Center(
                          child: Column(
                            children: [
                              Icon(Icons.confirmation_number_outlined, size: 40, color: Colors.grey.shade300),
                              const SizedBox(height: 8),
                              Text(
                                'Tidak ada voucher tersedia',
                                style: TextStyle(fontSize: 12, color: Colors.grey.shade500),
                              ),
                            ],
                          ),
                        ),
                      )
                    : ListView.builder(
                        shrinkWrap: true,
                        itemCount: _vouchers.length,
                        itemBuilder: (context, index) {
                          final voucher = _vouchers[index];
                          final code = voucher['code'] ?? '';
                          final name = voucher['name'] ?? '';
                          final desc = voucher['description'] ?? '';
                          final minSpend = (voucher['min_spend'] as num?)?.toDouble() ?? 0.0;
                          final formattedDiscount = voucher['formatted_discount'] ?? 'Diskon';
                          final expiresAt = voucher['expires_at'] ?? 'Berlaku Selamanya';
                          final badge = voucher['badge'] ?? '';
                          final isSelected = widget.appliedVoucherCode == code;
                          final isValidateLoading = _isValidating && _validatingCode == code;

                          IconData voucherIcon = Icons.confirmation_number_rounded;
                          if (name.toLowerCase().contains('ongkir')) {
                            voucherIcon = Icons.local_shipping_rounded;
                          } else if (name.toLowerCase().contains('star') || name.toLowerCase().contains('vip')) {
                            voucherIcon = Icons.star_rounded;
                          } else if (name.toLowerCase().contains('toko')) {
                            voucherIcon = Icons.storefront_rounded;
                          }

                          return Container(
                            margin: const EdgeInsets.only(bottom: 12),
                            height: 100,
                            child: Stack(
                              children: [
                                Container(
                                  decoration: BoxDecoration(
                                    color: Colors.white,
                                    borderRadius: BorderRadius.circular(12),
                                    boxShadow: [
                                      BoxShadow(
                                        color: Colors.black.withOpacity(0.05),
                                        blurRadius: 6,
                                        offset: const Offset(0, 2),
                                      ),
                                    ],
                                    border: Border.all(
                                      color: isSelected ? AppTheme.success : AppTheme.border,
                                      width: isSelected ? 1.5 : 1,
                                    ),
                                  ),
                                  child: Row(
                                    children: [
                                      Container(
                                        width: 85,
                                        decoration: const BoxDecoration(
                                          gradient: LinearGradient(
                                            begin: Alignment.topLeft,
                                            end: Alignment.bottomRight,
                                            colors: [
                                              Color(0xFFF43F5E),
                                              Color(0xFFE11D48),
                                            ],
                                          ),
                                          borderRadius: BorderRadius.only(
                                            topLeft: Radius.circular(11),
                                            bottomLeft: Radius.circular(11),
                                          ),
                                        ),
                                        child: Column(
                                          mainAxisAlignment: MainAxisAlignment.center,
                                          children: [
                                            Icon(voucherIcon, color: Colors.white, size: 28),
                                            const SizedBox(height: 4),
                                            Padding(
                                              padding: const EdgeInsets.symmetric(horizontal: 4),
                                              child: Text(
                                                name.toLowerCase().contains('ongkir') 
                                                    ? 'ONGKIR' 
                                                    : name.toLowerCase().contains('toko') 
                                                        ? 'TOKO' 
                                                        : 'PROMO',
                                                style: const TextStyle(
                                                  color: Colors.white,
                                                  fontSize: 8,
                                                  fontWeight: FontWeight.bold,
                                                  letterSpacing: 0.5,
                                                ),
                                                textAlign: TextAlign.center,
                                                maxLines: 2,
                                                overflow: TextOverflow.ellipsis,
                                              ),
                                            ),
                                          ],
                                        ),
                                      ),
                                      Expanded(
                                        child: Padding(
                                          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                                          child: Row(
                                            children: [
                                              Expanded(
                                                child: Column(
                                                  crossAxisAlignment: CrossAxisAlignment.start,
                                                  mainAxisAlignment: MainAxisAlignment.center,
                                                  children: [
                                                    Text(
                                                      name,
                                                      maxLines: 1,
                                                      overflow: TextOverflow.ellipsis,
                                                      style: const TextStyle(
                                                        fontWeight: FontWeight.w800,
                                                        fontSize: 12,
                                                        color: AppTheme.accentNavy,
                                                      ),
                                                    ),
                                                    const SizedBox(height: 2),
                                                    Text(
                                                      'Min. Blj ${_formatCurrency(minSpend)}',
                                                      style: const TextStyle(
                                                        fontSize: 10,
                                                        color: AppTheme.textSecondary,
                                                      ),
                                                    ),
                                                    const SizedBox(height: 4),
                                                    Row(
                                                      children: [
                                                        if (badge.isNotEmpty) ...[
                                                          Container(
                                                            padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                                            decoration: BoxDecoration(
                                                              color: Colors.orange.shade50,
                                                              borderRadius: BorderRadius.circular(4),
                                                            ),
                                                            child: Text(
                                                              badge,
                                                              style: TextStyle(
                                                                fontSize: 8,
                                                                color: Colors.orange.shade800,
                                                                fontWeight: FontWeight.bold,
                                                              ),
                                                            ),
                                                          ),
                                                          const SizedBox(width: 6),
                                                        ],
                                                        Expanded(
                                                          child: Text(
                                                            's.d. $expiresAt',
                                                            style: const TextStyle(
                                                              fontSize: 9,
                                                              color: AppTheme.textMuted,
                                                            ),
                                                            maxLines: 1,
                                                            overflow: TextOverflow.ellipsis,
                                                          ),
                                                        ),
                                                      ],
                                                    ),
                                                  ],
                                                ),
                                              ),
                                              const SizedBox(width: 8),
                                              isValidateLoading
                                                  ? const SizedBox(
                                                      width: 20,
                                                      height: 20,
                                                      child: CircularProgressIndicator(strokeWidth: 2, color: AppTheme.primary),
                                                    )
                                                  : isSelected
                                                      ? Container(
                                                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                                                          decoration: BoxDecoration(
                                                            color: Colors.green.shade50,
                                                            borderRadius: BorderRadius.circular(8),
                                                            border: Border.all(color: Colors.green.shade300),
                                                          ),
                                                          child: Row(
                                                            mainAxisSize: MainAxisSize.min,
                                                            children: const [
                                                              Icon(Icons.check_circle_outline_rounded, color: Colors.green, size: 12),
                                                              SizedBox(width: 4),
                                                              Text(
                                                                'Terpilih',
                                                                style: TextStyle(
                                                                  color: Colors.green,
                                                                  fontWeight: FontWeight.bold,
                                                                  fontSize: 9,
                                                                ),
                                                              ),
                                                            ],
                                                          ),
                                                        )
                                                      : ElevatedButton(
                                                          style: ElevatedButton.styleFrom(
                                                            backgroundColor: AppTheme.primary,
                                                            foregroundColor: Colors.white,
                                                            elevation: 0,
                                                            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                                                            minimumSize: Size.zero,
                                                            tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                                                            shape: RoundedRectangleBorder(
                                                              borderRadius: BorderRadius.circular(8),
                                                            ),
                                                          ),
                                                          onPressed: () => _applyVoucherCode(code),
                                                          child: const Text(
                                                            'Pakai',
                                                            style: TextStyle(
                                                              fontSize: 10,
                                                              fontWeight: FontWeight.bold,
                                                            ),
                                                          ),
                                                        ),
                                            ],
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                                Positioned(
                                  left: 80,
                                  top: -6,
                                  child: Container(
                                    width: 10,
                                    height: 10,
                                    decoration: const BoxDecoration(
                                      color: Colors.white,
                                      shape: BoxShape.circle,
                                    ),
                                  ),
                                ),
                                Positioned(
                                  left: 80,
                                  bottom: -6,
                                  child: Container(
                                    width: 10,
                                    height: 10,
                                    decoration: const BoxDecoration(
                                      color: Colors.white,
                                      shape: BoxShape.circle,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          );
                        },
                      ),
          ),
        ],
      ),
    );
  }
}
