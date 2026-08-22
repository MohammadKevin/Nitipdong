import 'package:flutter/material.dart';
import '../services/api_service.dart';
import '../theme/app_theme.dart';

class ServerConfigDialog extends StatefulWidget {
  final VoidCallback? onSaved;

  const ServerConfigDialog({Key? key, this.onSaved}) : super(key: key);

  static void show(BuildContext context, {VoidCallback? onSaved}) {
    showDialog(
      context: context,
      builder: (context) => ServerConfigDialog(onSaved: onSaved),
    );
  }

  @override
  State<ServerConfigDialog> createState() => _ServerConfigDialogState();
}

class _ServerConfigDialogState extends State<ServerConfigDialog> {
  final _urlController = TextEditingController();
  bool _isTesting = false;
  String? _testResult;
  bool _isSuccess = false;

  @override
  void initState() {
    super.initState();
    _urlController.text = ApiService.baseUrl;
    _loadCurrentUrl();
  }

  Future<void> _loadCurrentUrl() async {
    final url = await ApiService.getBaseUrl();
    setState(() {
      _urlController.text = url;
    });
  }

  Future<void> _testConnection() async {
    setState(() {
      _isTesting = true;
      _testResult = null;
    });

    final inputUrl = _urlController.text.trim();
    await ApiService.setBaseUrl(inputUrl);

    final status = await ApiService.checkSystemStatus();
    final products = await ApiService.getProducts(limit: 1);

    setState(() {
      _isTesting = false;
      if (status['success'] == true || products.isNotEmpty) {
        _isSuccess = true;
        _testResult = '🟢 Berhasil terhubung ke server Laravel! Database & API aktif.';
      } else {
        _isSuccess = false;
        _testResult = '🔴 Tidak dapat terhubung. Pastikan laptop & HP terhubung ke Wi-Fi yang sama dan Laravel server sedang aktif.';
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      title: const Row(
        children: [
          Icon(Icons.dns_rounded, color: AppTheme.primary),
          SizedBox(width: 10),
          Text('Pengaturan Server API', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
        ],
      ),
      content: SingleChildScrollView(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Masukkan IP Laptop atau Domain Website tempat backend Laravel berjalan:',
              style: TextStyle(fontSize: 12, color: AppTheme.textSecondary),
            ),
            const SizedBox(height: 14),
            TextField(
              controller: _urlController,
              decoration: InputDecoration(
                labelText: 'URL API Backend',
                hintText: 'http://192.168.x.x:8000/api/v1',
                prefixIcon: const Icon(Icons.link, size: 20),
                contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
              ),
              style: const TextStyle(fontSize: 13),
            ),
            const SizedBox(height: 10),

            // Quick Preset Buttons
            Wrap(
              spacing: 6,
              children: [
                ActionChip(
                  label: const Text('Wi-Fi Laptop', style: TextStyle(fontSize: 11)),
                  onPressed: () {
                    setState(() => _urlController.text = 'http://10.217.145.88:8000/api/v1');
                  },
                ),
                ActionChip(
                  label: const Text('Emulator', style: TextStyle(fontSize: 11)),
                  onPressed: () {
                    setState(() => _urlController.text = 'http://10.0.2.2:8000/api/v1');
                  },
                ),
              ],
            ),
            const SizedBox(height: 14),

            // Test Result Box
            if (_testResult != null)
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: _isSuccess ? Colors.green.shade50 : Colors.red.shade50,
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: _isSuccess ? Colors.green.shade300 : Colors.red.shade300),
                ),
                child: Text(
                  _testResult!,
                  style: TextStyle(
                    fontSize: 11,
                    color: _isSuccess ? Colors.green.shade800 : Colors.red.shade800,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
          ],
        ),
      ),
      actions: [
        OutlinedButton(
          onPressed: _isTesting ? null : _testConnection,
          child: _isTesting
              ? const SizedBox(width: 14, height: 14, child: CircularProgressIndicator(strokeWidth: 2))
              : const Text('Tes Koneksi', style: TextStyle(fontSize: 12)),
        ),
        ElevatedButton(
          onPressed: () async {
            await ApiService.setBaseUrl(_urlController.text.trim());
            if (widget.onSaved != null) widget.onSaved!();
            if (mounted) Navigator.pop(context);
          },
          child: const Text('Simpan & Pakai', style: TextStyle(fontSize: 12)),
        ),
      ],
    );
  }
}
