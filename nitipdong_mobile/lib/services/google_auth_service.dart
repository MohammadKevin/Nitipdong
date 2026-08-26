import 'package:flutter/material.dart';
import 'package:google_sign_in/google_sign_in.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';
import '../screens/main_nav_screen.dart';

class GoogleAuthService {
  static final GoogleSignIn _googleSignIn = GoogleSignIn(
    scopes: [
      'email',
      'profile',
    ],
    // Web / Backend OAuth Client ID from Google Cloud Console
    serverClientId: '588650724388-u4d3h7e788q770hs7r6c250fg3k3p5n0.apps.googleusercontent.com',
  );

  /// Performs full Google Sign-In flow and links with NitipDong backend API.
  static Future<bool> signInWithGoogle(BuildContext context) async {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);

    try {
      // Disconnect previous session if any to allow choosing account
      try {
        await _googleSignIn.signOut();
      } catch (_) {}

      // Prompt user to select Google Account
      final GoogleSignInAccount? googleUser = await _googleSignIn.signIn();

      if (googleUser == null) {
        // User cancelled the sign-in dialog
        return false;
      }

      final String email = googleUser.email;
      final String name = googleUser.displayName?.isNotEmpty == true
          ? googleUser.displayName!
          : email.split('@').first;
      final String googleId = googleUser.id;
      final String? photoUrl = googleUser.photoUrl;

      // Submit to NitipDong Backend API
      final bool success = await authProvider.loginWithGoogle(
        email: email,
        name: name,
        googleId: googleId,
        avatar: photoUrl,
      );

      if (!context.mounted) return success;

      if (success) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Selamat datang, $name! Berhasil masuk dengan Akun Google 🚀'),
            backgroundColor: const Color(0xFF059669),
            behavior: SnackBarBehavior.floating,
          ),
        );

        Navigator.pushAndRemoveUntil(
          context,
          PageRouteBuilder(
            pageBuilder: (_, __, ___) => const MainNavScreen(),
            transitionsBuilder: (_, a, __, c) => FadeTransition(opacity: a, child: c),
            transitionDuration: const Duration(milliseconds: 350),
          ),
          (route) => false,
        );
        return true;
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(authProvider.errorMessage ?? 'Gagal masuk dengan Google. Silakan coba lagi.'),
            backgroundColor: Colors.red,
            behavior: SnackBarBehavior.floating,
          ),
        );
        return false;
      }
    } catch (e) {
      if (!context.mounted) return false;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Kendala saat menghubungkan Google: $e'),
          backgroundColor: Colors.red,
          behavior: SnackBarBehavior.floating,
        ),
      );
      return false;
    }
  }

  /// Sign out from Google Session
  static Future<void> signOut() async {
    try {
      await _googleSignIn.signOut();
    } catch (_) {}
  }
}
