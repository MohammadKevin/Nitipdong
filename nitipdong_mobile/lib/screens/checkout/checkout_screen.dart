import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../providers/cart_provider.dart';
import '../../providers/auth_provider.dart';
import '../../services/api_service.dart';
import '../orders/orders_screen.dart';

class CheckoutScreen extends StatefulWidget {
  const CheckoutScreen({Key? key}) : super(key: key);

  @override
  State<CheckoutScreen> createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends State<CheckoutScreen> {
  final _addressController = TextEditingController();
  final _voucherController = TextEditingController();
  String _selectedPayment = 'QRIS Instant (BCA, Mandiri, GoPay, OVO)';
  String _selectedCourier = 'J&T Express (Gratis Ongkir Rp0)';
  bool _isProcessing = false;
  bool _isValidatingVoucher = false;
  String? _appliedVoucherCode;
  double _discountAmount = 0.0;

  @override
  void initState() {
    super.initState();
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    _addressController.text = authProvider.user?.phone.isNotEmpty == true
        ? 'Jl. Merdeka No. 45, Jakarta Selatan (0812-3456-7890)'
        : 'Jl. Sudirman No. 123, Jakarta Pusat';
  }

  String _formatCurrency(double amount) {
    final formatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    return formatter.format(amount);
  }

  Future<void> _applyVoucher() async {
    if (_voucherController.text.trim().isEmpty) return;
    setState(() => _isValidatingVoucher = true);
    final result = await ApiService.validateVoucher(_voucherController.text.trim());
    setState(() {
      _isValidatingVoucher = false;
      if (result['success'] == true) {
        _appliedVoucherCode = result['code'];
        _discountAmount = result['discount_amount'] ?? 0.0;
      } else {
        _appliedVoucherCode = null;
        _discountAmount = 0.0;
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(result['message'] ?? 'Voucher tidak dapat digunakan')),
        );
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final cartProvider = Provider.of<CartProvider>(context);

    return Scaffold(
      appBar: AppBar(title: const Text('Checkout Pembelian')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // 1. Shipping Address Card
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
                      Icon(Icons.location_on_outlined, color: AppTheme.primary, size: 20),
                      SizedBox(width: 6),
                      Text('Alamat Pengiriman', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13)),
                    ],
                  ),
                  const SizedBox(height: 10),
                  TextField(
                    controller: _addressController,
                    maxLines: 2,
                    style: const TextStyle(fontSize: 12),
                    decoration: const InputDecoration(
                      hintText: 'Alamat lengkap pengiriman...',
                      contentPadding: EdgeInsets.all(12),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 12),

            // 2. Shipping Courier Selection
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
                      Text('Opsi Pengiriman', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13)),
                    ],
                  ),
                  const SizedBox(height: 8),
                  DropdownButtonFormField<String>(
                    value: _selectedCourier,
                    items: [
                      'J&T Express (Gratis Ongkir Rp0)',
                      'SiCepat Reguler (Gratis Ongkir Rp0)',
                      'JNE Regular (Gratis Ongkir Rp0)',
                    ].map((c) => DropdownMenuItem(value: c, child: Text(c, style: const TextStyle(fontSize: 12)))).toList(),
                    onChanged: (val) => setState(() => _selectedCourier = val!),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 12),

            // 3. Payment Method
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
                      Icon(Icons.account_balance_wallet_outlined, color: AppTheme.primary, size: 20),
                      SizedBox(width: 6),
                      Text('Metode Pembayaran', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13)),
                    ],
                  ),
                  const SizedBox(height: 8),
                  DropdownButtonFormField<String>(
                    value: _selectedPayment,
                    items: [
                      'QRIS Instant (BCA, Mandiri, GoPay, OVO)',
                      'Transfer Virtual Account BCA',
                      'Transfer Virtual Account Mandiri',
                      'Saldo Dompet NitipPay',
                    ].map((p) => DropdownMenuItem(value: p, child: Text(p, style: const TextStyle(fontSize: 12)))).toList(),
                    onChanged: (val) => setState(() => _selectedPayment = val!),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 12),

            // Voucher Card
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
                      Text('Voucher Promo Toko / Platform', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13)),
                    ],
                  ),
                  const SizedBox(height: 10),
                  Row(
                    children: [
                      Expanded(
                        child: TextField(
                          controller: _voucherController,
                          decoration: const InputDecoration(
                            hintText: 'Masukkan kode voucher...',
                            contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                          ),
                          style: const TextStyle(fontSize: 12),
                        ),
                      ),
                      const SizedBox(width: 8),
                      ElevatedButton(
                        onPressed: _isValidatingVoucher ? null : _applyVoucher,
                        style: ElevatedButton.styleFrom(
                          padding: const EdgeInsets.symmetric(horizontal: 14),
                          minimumSize: const Size(0, 40),
                        ),
                        child: _isValidatingVoucher
                            ? const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                            : Text(_appliedVoucherCode != null ? 'Ganti' : 'Pakai', style: const TextStyle(fontSize: 12)),
                      ),
                    ],
                  ),
                  if (_appliedVoucherCode != null) ...[
                    const SizedBox(height: 8),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                      decoration: BoxDecoration(
                        color: AppTheme.success.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: Row(
                        children: [
                          const Icon(Icons.check_circle, color: AppTheme.success, size: 16),
                          const SizedBox(width: 6),
                          Expanded(
                            child: Text(
                              'Voucher $_appliedVoucherCode berhasil digunakan! Diskon ${_formatCurrency(_discountAmount)}',
                              style: const TextStyle(color: AppTheme.success, fontSize: 11, fontWeight: FontWeight.bold),
                            ),
                          ),
                          IconButton(
                            icon: const Icon(Icons.close, size: 16, color: AppTheme.success),
                            constraints: const BoxConstraints(),
                            padding: EdgeInsets.zero,
                            onPressed: () {
                              setState(() {
                                _appliedVoucherCode = null;
                                _discountAmount = 0.0;
                                _voucherController.clear();
                              });
                            },
                          )
                        ],
                      ),
                    )
                  ]
                ],
              ),
            ),
            const SizedBox(height: 12),

            // 4. Order Summary Card
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
                  const Text('Ringkasan Pembayaran', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13)),
                  const SizedBox(height: 10),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('Total Harga Produk', style: TextStyle(fontSize: 12, color: AppTheme.textSecondary)),
                      Text(_formatCurrency(cartProvider.subtotal), style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600)),
                    ],
                  ),
                  if (_discountAmount > 0) ...[
                    const SizedBox(height: 6),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text('Diskon Voucher', style: TextStyle(fontSize: 12, color: AppTheme.textSecondary)),
                        Text('- ${_formatCurrency(_discountAmount)}', style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: Colors.red)),
                      ],
                    ),
                  ],
                  const SizedBox(height: 6),
                  const Row(
                     mainAxisAlignment: MainAxisAlignment.spaceBetween,
                     children: [
                       Text('Ongkos Kirim', style: TextStyle(fontSize: 12, color: AppTheme.textSecondary)),
                       Text('Rp 0 (Voucher Bebas Ongkir)', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppTheme.success)),
                     ],
                  ),
                  const Divider(height: 20),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('Total Tagihan', style: TextStyle(fontSize: 13, fontWeight: FontWeight.w800)),
                      Text(
                        _formatCurrency((cartProvider.subtotal - _discountAmount) < 0 ? 0.0 : (cartProvider.subtotal - _discountAmount)),
                        style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w900, color: AppTheme.primaryDark),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 30),

            // Pay Button
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
                          voucherCode: _appliedVoucherCode,
                        );
                        setState(() => _isProcessing = false);

                        if (result['success'] == true && mounted) {
                          await cartProvider.fetchCart();
                          showDialog(
                            context: context,
                            barrierDismissible: false,
                            builder: (context) => AlertDialog(
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                              title: const Row(
                                children: [
                                  Icon(Icons.check_circle, color: AppTheme.success, size: 28),
                                  SizedBox(width: 8),
                                  Text('Pesanan Berhasil!', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
                                ],
                              ),
                              content: Text(
                                'Pesanan nomor ${result['order_number']} telah dibuat dan sedang menunggu proses pengiriman oleh toko.',
                                style: const TextStyle(fontSize: 12),
                              ),
                              actions: [
                                ElevatedButton(
                                  onPressed: () {
                                    Navigator.pop(context); // Close dialog
                                    Navigator.pushReplacement(
                                      context,
                                      MaterialPageRoute(builder: (context) => const OrdersScreen()),
                                    );
                                  },
                                  child: const Text('Lihat Status Pesanan'),
                                ),
                              ],
                            ),
                          );
                        } else if (mounted) {
                          ScaffoldMessenger.of(context).showSnackBar(
                            SnackBar(content: Text(result['message'] ?? 'Gagal membuat pesanan')),
                          );
                        }
                      },
                child: _isProcessing
                    ? const SizedBox(
                        width: 22,
                        height: 22,
                        child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                      )
                    : const Text('Bayar Sekarang', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 14)),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
