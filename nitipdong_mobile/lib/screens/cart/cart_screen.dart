import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../theme/app_theme.dart';
import '../../providers/cart_provider.dart';
import '../../providers/auth_provider.dart';
import '../auth/login_screen.dart';
import '../checkout/checkout_screen.dart';

class CartScreen extends StatefulWidget {
  const CartScreen({Key? key}) : super(key: key);

  @override
  State<CartScreen> createState() => _CartScreenState();
}

class _CartScreenState extends State<CartScreen> {
  final Set<int> _selectedItemIds = {};
  bool _selectAll = true;

  String _formatCurrency(double amount) {
    final formatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    return formatter.format(amount);
  }

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadCartAndSelectAll();
    });
  }

  Future<void> _loadCartAndSelectAll() async {
    final cartProvider = Provider.of<CartProvider>(context, listen: false);
    await cartProvider.fetchCart();
    if (mounted) {
      setState(() {
        _selectedItemIds.clear();
        for (var item in cartProvider.items) {
          _selectedItemIds.add(item.id);
        }
        _selectAll = true;
      });
    }
  }

  double _calculateSelectedSubtotal(CartProvider cartProvider) {
    double total = 0.0;
    for (var item in cartProvider.items) {
      if (_selectedItemIds.contains(item.id)) {
        total += item.subtotal;
      }
    }
    return total;
  }

  int _countSelectedItems(CartProvider cartProvider) {
    int count = 0;
    for (var item in cartProvider.items) {
      if (_selectedItemIds.contains(item.id)) {
        count += item.quantity;
      }
    }
    return count;
  }

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);
    final cartProvider = Provider.of<CartProvider>(context);

    if (!authProvider.isAuthenticated) {
      return Scaffold(
        appBar: AppBar(title: const Text('Keranjang Belanja')),
        body: Center(
          child: Padding(
            padding: const EdgeInsets.all(30),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Container(
                  padding: const EdgeInsets.all(24),
                  decoration: BoxDecoration(
                    color: AppTheme.primaryLight,
                    borderRadius: BorderRadius.circular(15),
                  ),
                  child: const Icon(Icons.shopping_cart_outlined, size: 56, color: AppTheme.primary),
                ),
                const SizedBox(height: 20),
                const Text(
                  'Masuk ke Akun Anda',
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: AppTheme.textPrimary),
                ),
                const SizedBox(height: 8),
                const Text(
                  'Silakan login terlebih dahulu untuk melihat dan mengelola produk di keranjang belanja Anda.',
                  textAlign: TextAlign.center,
                  style: TextStyle(fontSize: 12.5, color: AppTheme.textSecondary, height: 1.4),
                ),
                const SizedBox(height: 24),
                ElevatedButton(
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(builder: (context) => const LoginScreen()),
                    );
                  },
                  child: const Text('Masuk Sekarang'),
                ),
              ],
            ),
          ),
        ),
      );
    }

    final selectedSubtotal = _calculateSelectedSubtotal(cartProvider);
    final selectedCount = _countSelectedItems(cartProvider);

    return Scaffold(
      appBar: AppBar(
        title: Text('Keranjang Belanja (${cartProvider.itemCount})'),
        actions: [
          if (cartProvider.items.isNotEmpty)
            TextButton(
              onPressed: () {
                setState(() {
                  if (_selectAll) {
                    _selectedItemIds.clear();
                    _selectAll = false;
                  } else {
                    _selectedItemIds.clear();
                    for (var item in cartProvider.items) {
                      _selectedItemIds.add(item.id);
                    }
                    _selectAll = true;
                  }
                });
              },
              child: Text(
                _selectAll ? 'Batalkan Semua' : 'Pilih Semua',
                style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: AppTheme.primaryDark),
              ),
            ),
        ],
      ),
      body: RefreshIndicator(
        color: AppTheme.primary,
        onRefresh: () => cartProvider.fetchCart(),
        child: cartProvider.isLoading && cartProvider.items.isEmpty
            ? const Center(child: CircularProgressIndicator(color: AppTheme.primary))
            : cartProvider.items.isEmpty
                ? Center(
                    child: SingleChildScrollView(
                      physics: const AlwaysScrollableScrollPhysics(),
                      child: Padding(
                        padding: const EdgeInsets.all(40),
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Container(
                              padding: const EdgeInsets.all(24),
                              decoration: BoxDecoration(
                                color: Colors.grey.shade100,
                                borderRadius: BorderRadius.circular(15),
                              ),
                              child: Icon(Icons.remove_shopping_cart_outlined, size: 56, color: Colors.grey.shade400),
                            ),
                            const SizedBox(height: 16),
                            const Text(
                              'Keranjang Belanja Masih Kosong',
                              style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800, color: AppTheme.textPrimary),
                            ),
                            const SizedBox(height: 6),
                            const Text(
                              'Yuk, cari dan tambahkan produk impian Anda sekarang!',
                              textAlign: TextAlign.center,
                              style: TextStyle(fontSize: 12, color: AppTheme.textMuted),
                            ),
                          ],
                        ),
                      ),
                    ),
                  )
                : ListView.builder(
                    padding: const EdgeInsets.fromLTRB(16, 12, 16, 140),
                    itemCount: cartProvider.items.length,
                    itemBuilder: (context, index) {
                      final item = cartProvider.items[index];
                      final isSelected = _selectedItemIds.contains(item.id);

                      return Container(
                        margin: const EdgeInsets.only(bottom: 12),
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(14),
                          border: Border.all(
                            color: isSelected ? AppTheme.primary : AppTheme.border,
                            width: isSelected ? 1.5 : 1,
                          ),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.02),
                              blurRadius: 8,
                              offset: const Offset(0, 2),
                            ),
                          ],
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // Store Header Row
                            Row(
                              children: [
                                SizedBox(
                                  width: 24,
                                  height: 24,
                                  child: Checkbox(
                                    value: isSelected,
                                    activeColor: AppTheme.primary,
                                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(4)),
                                    onChanged: (val) {
                                      setState(() {
                                        if (val == true) {
                                          _selectedItemIds.add(item.id);
                                        } else {
                                          _selectedItemIds.remove(item.id);
                                        }
                                        _selectAll = _selectedItemIds.length == cartProvider.items.length;
                                      });
                                    },
                                  ),
                                ),
                                const SizedBox(width: 8),
                                const Icon(Icons.storefront_rounded, size: 16, color: AppTheme.primaryDark),
                                const SizedBox(width: 4),
                                Expanded(
                                  child: Text(
                                    item.product?.storeName ?? 'NitipDong Official Store',
                                    style: const TextStyle(
                                      fontSize: 11.5,
                                      fontWeight: FontWeight.w700,
                                      color: AppTheme.textPrimary,
                                    ),
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                  ),
                                ),
                              ],
                            ),
                            const Divider(height: 16, thickness: 0.8),

                            // Item Detail Row
                            Row(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                // Product Image
                                ClipRRect(
                                  borderRadius: BorderRadius.circular(10),
                                  child: CachedNetworkImage(
                                    imageUrl: item.imageUrl,
                                    width: 76,
                                    height: 76,
                                    fit: BoxFit.cover,
                                    placeholder: (context, url) => Container(color: Colors.grey.shade100),
                                    errorWidget: (context, url, error) => Container(
                                      color: Colors.grey.shade100,
                                      child: const Icon(Icons.shopping_bag_outlined, color: Colors.grey),
                                    ),
                                  ),
                                ),
                                const SizedBox(width: 12),

                                // Product Info & Counter
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        item.name,
                                        maxLines: 2,
                                        overflow: TextOverflow.ellipsis,
                                        style: const TextStyle(
                                          fontSize: 12.5,
                                          fontWeight: FontWeight.w700,
                                          color: AppTheme.textPrimary,
                                          height: 1.25,
                                        ),
                                      ),
                                      if (item.variant != null && item.variant!.isNotEmpty) ...[
                                        const SizedBox(height: 4),
                                        Container(
                                          padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                          decoration: BoxDecoration(
                                            color: Colors.grey.shade100,
                                            borderRadius: BorderRadius.circular(4),
                                          ),
                                          child: Text(
                                            'Varian: ${item.variant}',
                                            style: TextStyle(fontSize: 9.5, color: Colors.grey.shade700, fontWeight: FontWeight.w600),
                                          ),
                                        ),
                                      ],
                                      const SizedBox(height: 6),
                                      Text(
                                        _formatCurrency(item.price),
                                        style: const TextStyle(
                                          fontSize: 13.5,
                                          fontWeight: FontWeight.w900,
                                          color: AppTheme.primaryDark,
                                        ),
                                      ),
                                      const SizedBox(height: 8),

                                      // Stepper & Delete Row
                                      Row(
                                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                        children: [
                                          IconButton(
                                            icon: const Icon(Icons.delete_outline_rounded, color: Colors.redAccent, size: 20),
                                            padding: EdgeInsets.zero,
                                            constraints: const BoxConstraints(),
                                            onPressed: () => cartProvider.removeItem(item.id),
                                          ),
                                          Container(
                                            decoration: BoxDecoration(
                                              border: Border.all(color: AppTheme.border),
                                              borderRadius: BorderRadius.circular(8),
                                            ),
                                            child: Row(
                                              children: [
                                                InkWell(
                                                  onTap: item.quantity > 1
                                                      ? () => cartProvider.updateQuantity(item.id, item.quantity - 1)
                                                      : null,
                                                  borderRadius: const BorderRadius.horizontal(left: Radius.circular(8)),
                                                  child: Padding(
                                                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                                    child: Icon(Icons.remove, size: 14, color: item.quantity > 1 ? Colors.black82 : Colors.grey.shade400),
                                                  ),
                                                ),
                                                Padding(
                                                  padding: const EdgeInsets.symmetric(horizontal: 8),
                                                  child: Text(
                                                    item.quantity.toString(),
                                                    style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w800),
                                                  ),
                                                ),
                                                InkWell(
                                                  onTap: item.quantity < item.stock
                                                      ? () => cartProvider.updateQuantity(item.id, item.quantity + 1)
                                                      : null,
                                                  borderRadius: const BorderRadius.horizontal(right: Radius.circular(8)),
                                                  child: Padding(
                                                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                                    child: Icon(Icons.add, size: 14, color: item.quantity < item.stock ? Colors.black82 : Colors.grey.shade400),
                                                  ),
                                                ),
                                              ],
                                            ),
                                          ),
                                        ],
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                      );
                    },
                  ),
      ),
      bottomSheet: cartProvider.items.isEmpty
          ? null
          : Container(
              padding: const EdgeInsets.fromLTRB(16, 12, 16, 16),
              decoration: BoxDecoration(
                color: Colors.white,
                border: const Border(top: BorderSide(color: AppTheme.border)),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.04),
                    blurRadius: 10,
                    offset: const Offset(0, -4),
                  ),
                ],
              ),
              child: SafeArea(
                child: Row(
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text(
                          'Total Belanja ($selectedCount barang)',
                          style: const TextStyle(fontSize: 11, color: AppTheme.textMuted),
                        ),
                        Text(
                          _formatCurrency(selectedSubtotal),
                          style: const TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.w900,
                            color: AppTheme.primaryDark,
                            letterSpacing: -0.3,
                          ),
                        ),
                      ],
                    ),
                    const Spacer(),
                    ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                      onPressed: selectedCount == 0
                          ? null
                          : () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(
                                  builder: (context) => CheckoutScreen(
                                    selectedCartIds: _selectedItemIds.toList(),
                                  ),
                                ),
                              );
                            },
                      child: Text('Checkout ($selectedCount)', style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 13)),
                    ),
                  ],
                ),
              ),
            ),
    );
  }
}
