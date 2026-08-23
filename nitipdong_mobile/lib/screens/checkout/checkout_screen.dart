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
                      Text('Gunakan Voucher Diskon', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 13)),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Expanded(
                        child: TextField(
                          controller: _voucherController,
                          textCapitalization: TextCapitalization.characters,
                          style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700),
                          decoration: InputDecoration(
                            hintText: 'Masukkan kode kupon (misal: ONGKIRNOL)',
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
                        onPressed: () => _applyVoucher(subtotal),
                        child: const Text('Pakai', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
                      ),
                    ],
                  ),
                  if (_appliedVoucherCode != null) ...[
                    const SizedBox(height: 8),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(color: Colors.green.shade50, borderRadius: BorderRadius.circular(6)),
                      child: Row(
                        children: [
                          const Icon(Icons.check_circle_rounded, color: Colors.green, size: 14),
                          const SizedBox(width: 6),
                          Text('Voucher $_appliedVoucherCode aktif (- ${_formatCurrency(_discountAmount)})', style: const TextStyle(fontSize: 11, color: Colors.green, fontWeight: FontWeight.w700)),
                        ],
                      ),
                    ),
                  ],
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
}
