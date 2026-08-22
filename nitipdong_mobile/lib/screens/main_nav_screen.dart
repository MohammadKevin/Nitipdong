import 'dart:async';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../theme/app_theme.dart';
import '../services/api_service.dart';
import '../providers/cart_provider.dart';
import 'home/home_screen.dart';
import 'cart/cart_screen.dart';
import 'orders/orders_screen.dart';
import 'profile/profile_screen.dart';

class MainNavScreen extends StatefulWidget {
  final int initialIndex;

  const MainNavScreen({Key? key, this.initialIndex = 0}) : super(key: key);

  @override
  State<MainNavScreen> createState() => _MainNavScreenState();
}

class _MainNavScreenState extends State<MainNavScreen> {
  late int _currentIndex;
  Timer? _heartbeatTimer;

  final List<Widget> _screens = const [
    HomeScreen(),
    OrdersScreen(),
    CartScreen(),
    ProfileScreen(),
  ];

  @override
  void initState() {
    super.initState();
    _currentIndex = widget.initialIndex;
    ApiService.resetMaintenanceState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<CartProvider>(context, listen: false).fetchCart();
      _startMaintenanceHeartbeat();
    });
  }

  void _startMaintenanceHeartbeat() {
    _heartbeatTimer?.cancel();
    // Periodically check server maintenance status every 8 seconds
    _heartbeatTimer = Timer.periodic(const Duration(seconds: 8), (_) async {
      try {
        final status = await ApiService.checkSystemStatus();
        if (status['is_maintenance'] == true) {
          ApiService.triggerMaintenanceRedirect(
            title: status['maintenance_title'],
            message: status['maintenance_message'],
          );
        }
      } catch (_) {}
    });
  }

  @override
  void dispose() {
    _heartbeatTimer?.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final cartProvider = Provider.of<CartProvider>(context);

    return Scaffold(
      body: IndexedStack(
        index: _currentIndex,
        children: _screens,
      ),
      bottomNavigationBar: Container(
        decoration: const BoxDecoration(
          color: Colors.white,
          border: Border(top: BorderSide(color: AppTheme.border, width: 1)),
        ),
        child: BottomNavigationBar(
          currentIndex: _currentIndex,
          onTap: (index) => setState(() => _currentIndex = index),
          type: BottomNavigationBarType.fixed,
          backgroundColor: Colors.white,
          selectedItemColor: AppTheme.primary,
          unselectedItemColor: AppTheme.textMuted,
          selectedLabelStyle: const TextStyle(fontSize: 10, fontWeight: FontWeight.w700),
          unselectedLabelStyle: const TextStyle(fontSize: 10, fontWeight: FontWeight.w500),
          elevation: 0,
          items: [
            const BottomNavigationBarItem(
              icon: Icon(Icons.home_outlined),
              activeIcon: Icon(Icons.home_rounded),
              label: 'Beranda',
            ),
            const BottomNavigationBarItem(
              icon: Icon(Icons.receipt_long_outlined),
              activeIcon: Icon(Icons.receipt_long_rounded),
              label: 'Pesanan',
            ),
            BottomNavigationBarItem(
              icon: Badge(
                isLabelVisible: cartProvider.itemCount > 0,
                label: Text(
                  cartProvider.itemCount > 99 ? '99+' : cartProvider.itemCount.toString(),
                  style: const TextStyle(fontSize: 9, fontWeight: FontWeight.w900),
                ),
                backgroundColor: AppTheme.primary,
                child: const Icon(Icons.shopping_cart_outlined),
              ),
              activeIcon: Badge(
                isLabelVisible: cartProvider.itemCount > 0,
                label: Text(
                  cartProvider.itemCount > 99 ? '99+' : cartProvider.itemCount.toString(),
                  style: const TextStyle(fontSize: 9, fontWeight: FontWeight.w900),
                ),
                backgroundColor: AppTheme.primary,
                child: const Icon(Icons.shopping_cart_rounded),
              ),
              label: 'Keranjang',
            ),
            const BottomNavigationBarItem(
              icon: Icon(Icons.person_outline),
              activeIcon: Icon(Icons.person_rounded),
              label: 'Akun Saya',
            ),
          ],
        ),
      ),
    );
  }
}
