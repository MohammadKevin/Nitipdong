import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../providers/auth_provider.dart';
import '../main_nav_screen.dart';

class OtpVerificationScreen extends StatefulWidget {
  final String identifier; // Email or Phone
  final String? otpPreview;
  final bool isFromSplash;

  const OtpVerificationScreen({
    Key? key,
    required this.identifier,
    this.otpPreview,
    this.isFromSplash = false,
  }) : super(key: key);

  @override
  State<OtpVerificationScreen> createState() => _OtpVerificationScreenState();
}

class _OtpVerificationScreenState extends State<OtpVerificationScreen> {
  static const int _otpLength = 6;
  final List<TextEditingController> _controllers = List.generate(_otpLength, (_) => TextEditingController());
  final List<FocusNode> _focusNodes = List.generate(_otpLength, (_) => FocusNode());

  int _resendCooldown = 60;
  Timer? _timer;
  int _failedAttempts = 0;
  bool _isVerifying = false;
  String? _inlineError;

  @override
  void initState() {
    super.initState();
    _startCooldownTimer();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _focusNodes[0].requestFocus();
    });
  }

  @override
  void dispose() {
    _timer?.cancel();
    for (var c in _controllers) {
      c.dispose();
    }
    for (var f in _focusNodes) {
      f.dispose();
    }
    super.dispose();
  }

  void _startCooldownTimer([int seconds = 60]) {
    _timer?.cancel();
    setState(() => _resendCooldown = seconds);
    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (_resendCooldown > 0) {
        setState(() => _resendCooldown--);
      } else {
        timer.cancel();
      }
    });
  }

  String get _currentOtp => _controllers.map((c) => c.text).join();

  void _onDigitChanged(int index, String value) {
    setState(() => _inlineError = null);

    // Handle paste of full 6 digits
    if (value.length > 1) {
      _handlePaste(value);
      return;
    }

    if (value.isNotEmpty) {
      if (index < _otpLength - 1) {
        _focusNodes[index + 1].requestFocus();
      } else {
        _focusNodes[index].unfocus();
        if (_currentOtp.length == _otpLength) {
          _submitOtp();
        }
      }
    }
  }

  void _handlePaste(String text) {
    final cleaned = text.replaceAll(RegExp(r'\D'), '');
    if (cleaned.isEmpty) return;

    for (int i = 0; i < _otpLength; i++) {
      if (i < cleaned.length) {
        _controllers[i].text = cleaned[i];
      }
    }

    if (cleaned.length >= _otpLength) {
      _focusNodes[_otpLength - 1].unfocus();
      _submitOtp();
    } else {
      _focusNodes[cleaned.length].requestFocus();
    }
    setState(() {});
  }

  Future<void> _submitOtp() async {
    final otp = _currentOtp;
    if (otp.length < _otpLength) {
      setState(() => _inlineError = 'Harap lengkapi 6 digit kode OTP.');
      return;
    }

    if (_failedAttempts >= 5) {
      setState(() => _inlineError = 'Terlalu banyak percobaan salah. Silakan kirim ulang kode OTP.');
      return;
    }

    setState(() {
      _isVerifying = true;
      _inlineError = null;
    });

    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    final success = await authProvider.verifyOtp(widget.identifier, otp);

    if (!mounted) return;
    setState(() => _isVerifying = false);

    if (success) {
      // Success feedback snackbar
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Verifikasi Berhasil! Selamat datang di NitipDong 🎉'),
          backgroundColor: AppTheme.success,
          behavior: SnackBarBehavior.floating,
        ),
      );

      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(builder: (_) => const MainNavScreen()),
        (route) => false,
      );
    } else {
      setState(() {
        _failedAttempts++;
        _inlineError = authProvider.errorMessage ?? 'Kode OTP salah. Periksa kembali SMS/Email Anda.';
      });
      // Clear inputs and refocus
      for (var c in _controllers) {
        c.clear();
      }
      _focusNodes[0].requestFocus();
    }
  }

  Future<void> _handleResend() async {
    if (_resendCooldown > 0) return;

    setState(() {
      _inlineError = null;
      _failedAttempts = 0;
    });

    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    final res = await authProvider.resendOtp(widget.identifier);

    if (!mounted) return;

    if (res['success'] == true) {
      _startCooldownTimer(res['cooldown_seconds'] ?? 60);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(res['message'] ?? 'Kode OTP baru telah dikirimkan! 📨'),
          behavior: SnackBarBehavior.floating,
        ),
      );
    } else {
      setState(() {
        _inlineError = res['message'] ?? 'Gagal mengirim ulang OTP.';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('Verifikasi Akun'),
        centerTitle: true,
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              const SizedBox(height: 10),

              // Top Icon Badge
              Container(
                width: 72,
                height: 72,
                decoration: BoxDecoration(
                  color: AppTheme.primaryLight,
                  shape: BoxShape.circle,
                  border: Border.all(color: AppTheme.border, width: 2),
                ),
                child: const Center(
                  child: Icon(
                    Icons.mark_email_read_rounded,
                    color: AppTheme.primary,
                    size: 36,
                  ),
                ),
              ),
              const SizedBox(height: 20),

              // Title
              const Text(
                'Masukkan Kode OTP',
                style: TextStyle(
                  fontSize: 22,
                  fontWeight: FontWeight.w800,
                  color: AppTheme.textPrimary,
                  letterSpacing: -0.5,
                ),
              ),
              const SizedBox(height: 8),

              // Subtitle with highlighted identifier
              RichText(
                textAlign: TextAlign.center,
                text: TextSpan(
                  style: const TextStyle(fontSize: 13, color: AppTheme.textSecondary, height: 1.4),
                  children: [
                    const TextSpan(text: 'Kami telah mengirimkan 6 digit kode verifikasi ke\n'),
                    TextSpan(
                      text: widget.identifier,
                      style: const TextStyle(
                        fontWeight: FontWeight.w700,
                        color: AppTheme.textPrimary,
                      ),
                    ),
                  ],
                ),
              ),

              if (widget.otpPreview != null) ...[
                const SizedBox(height: 12),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                  decoration: BoxDecoration(
                    color: Colors.amber.shade50,
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(color: Colors.amber.shade300),
                  ),
                  child: Text(
                    'Demo OTP: ${widget.otpPreview}',
                    style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: Colors.amber.shade900),
                  ),
                ),
              ],

              const SizedBox(height: 32),

              // 6-Digit OTP Box Grid
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: List.generate(_otpLength, (index) {
                  final hasValue = _controllers[index].text.isNotEmpty;
                  return SizedBox(
                    width: 46,
                    height: 56,
                    child: RawKeyboardListener(
                      focusNode: FocusNode(),
                      onKey: (event) {
                        if (event is RawKeyDownEvent &&
                            event.logicalKey == LogicalKeyboardKey.backspace &&
                            _controllers[index].text.isEmpty &&
                            index > 0) {
                          _focusNodes[index - 1].requestFocus();
                        }
                      },
                      child: TextField(
                        controller: _controllers[index],
                        focusNode: _focusNodes[index],
                        textAlign: TextAlign.center,
                        keyboardType: TextInputType.number,
                        maxLength: 1,
                        style: const TextStyle(
                          fontSize: 20,
                          fontWeight: FontWeight.w800,
                          color: AppTheme.textPrimary,
                        ),
                        inputFormatters: [
                          FilteringTextInputFormatter.digitsOnly,
                        ],
                        decoration: InputDecoration(
                          counterText: '',
                          contentPadding: EdgeInsets.zero,
                          filled: true,
                          fillColor: hasValue ? AppTheme.primaryLight : Colors.white,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(14),
                            borderSide: BorderSide(
                              color: _inlineError != null ? Colors.red.shade400 : AppTheme.border,
                              width: 1.5,
                            ),
                          ),
                          focusedBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(14),
                            borderSide: const BorderSide(
                              color: AppTheme.primary,
                              width: 2,
                            ),
                          ),
                        ),
                        onChanged: (val) => _onDigitChanged(index, val),
                      ),
                    ),
                  );
                }),
              ),

              const SizedBox(height: 16),

              // Inline Error State
              if (_inlineError != null) ...[
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                  decoration: BoxDecoration(
                    color: Colors.red.shade50,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: Colors.red.shade200),
                  ),
                  child: Row(
                    children: [
                      const Icon(Icons.error_outline_rounded, color: Colors.red, size: 18),
                      const SizedBox(width: 8),
                      Expanded(
                        child: Text(
                          _inlineError!,
                          style: const TextStyle(color: Colors.red, fontSize: 12, fontWeight: FontWeight.w600),
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 20),
              ] else
                const SizedBox(height: 20),

              // Verify Button
              SizedBox(
                width: double.infinity,
                height: 50,
                child: ElevatedButton(
                  onPressed: _isVerifying ? null : _submitOtp,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.primary,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                  ),
                  child: _isVerifying
                      ? const SizedBox(
                          width: 22,
                          height: 22,
                          child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                        )
                      : const Text(
                          'Verifikasi & Lanjutkan',
                          style: TextStyle(fontWeight: FontWeight.w800, fontSize: 14),
                        ),
                ),
              ),

              const SizedBox(height: 24),

              // Resend OTP & Cooldown Timer
              Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Text(
                    'Tidak menerima kode?',
                    style: TextStyle(fontSize: 13, color: AppTheme.textSecondary),
                  ),
                  const SizedBox(width: 4),
                  if (_resendCooldown > 0)
                    Text(
                      'Kirim ulang dalam ${_resendCooldown}s',
                      style: const TextStyle(
                        fontSize: 13,
                        fontWeight: FontWeight.w700,
                        color: AppTheme.textMuted,
                      ),
                    )
                  else
                    TextButton(
                      onPressed: _handleResend,
                      style: TextButton.styleFrom(
                        padding: const EdgeInsets.symmetric(horizontal: 6),
                        minimumSize: Size.zero,
                        tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                      ),
                      child: const Text(
                        'Kirim Ulang',
                        style: TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.w800,
                          color: AppTheme.primary,
                        ),
                      ),
                    ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
