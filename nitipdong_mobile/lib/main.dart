import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import 'theme/app_theme.dart';
import 'services/api_service.dart';
import 'providers/auth_provider.dart';
import 'providers/product_provider.dart';
import 'providers/cart_provider.dart';
import 'screens/splash_screen.dart';
import 'screens/maintenance_screen.dart';

// Global Navigation Key for instant maintenance redirect and dialogs
final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  SystemChrome.setSystemUIOverlayStyle(
    const SystemUiOverlayStyle(
      statusBarColor: Colors.transparent,
      statusBarIconBrightness: Brightness.dark,
    ),
  );

  // Global Maintenance Interceptor & Auto-Logout Handler
  ApiService.onMaintenanceDetected = (title, message) {
    // 1. Auto logout from AuthProvider session
    final context = navigatorKey.currentContext;
    if (context != null) {
      try {
        Provider.of<AuthProvider>(context, listen: false).logout();
      } catch (_) {}
    }

    // 2. Redirect immediately to MaintenanceScreen and remove all past routes
    navigatorKey.currentState?.pushAndRemoveUntil(
      MaterialPageRoute(
        builder: (_) => MaintenanceScreen(
          title: title,
          message: message,
        ),
      ),
      (route) => false,
    );
  };

  runApp(const NitipDongMobileApp());
}

class NitipDongMobileApp extends StatelessWidget {
  const NitipDongMobileApp({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()),
        ChangeNotifierProvider(create: (_) => ProductProvider()),
        ChangeNotifierProvider(create: (_) => CartProvider()),
      ],
      child: MaterialApp(
        navigatorKey: navigatorKey,
        title: 'NitipDong Mobile',
        debugShowCheckedModeBanner: false,
        theme: AppTheme.lightTheme,
        home: const SplashScreen(),
      ),
    );
  }
}
