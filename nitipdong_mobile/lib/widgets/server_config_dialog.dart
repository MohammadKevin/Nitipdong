import 'package:flutter/material.dart';
import '../services/api_service.dart';
import '../theme/app_theme.dart';
import '../screens/maintenance_screen.dart';

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
    final products = await ApiService.getProducts();

    setState(() {
      _isTesting = false;
      if (status['success'] == true || products.isNotEmpty) {
        _isSuccess = true;
        _testResult = '🟢 Berhasil terhubung ke server! Database & API aktif.';
      } else {
        _isSuccess = false;
        _testResult = '🔴 Tidak dapat terhubung. Pastikan laptop/server aktif dan URL sudah benar.';
      }
    });
  }

  void _previewMaintenanceScreen() {
    Navigator.pop(context);
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => const MaintenanceScreen(
          title: 'Mode Pemeliharaan & Pengembangan 🛠️ (Mode Demo)',
          message: 'Ini adalah tampilan simulasi layar pemeliharaan untuk menguji respon aplikasi dan desain UI saat server dalam mode maintenance.',
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      title: const Row(
        children: [
          Icon(Icons.developer_mode_rounded, color: AppTheme.primary),
          SizedBox(width: 10),
          Text('Pengaturan & Demo Tools', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
        ],
      ),
      content: SingleChildScrollView(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Arahkan API backend ke Server Live atau Laptop Local Anda:',
              style: TextStyle(fontSize: 12, color: AppTheme.textSecondary),
            ),
            const SizedBox(height: 14),
            TextField(
              controller: _urlController,
              decoration: InputDecoration(
                labelText: 'URL API Backend',
                hintText: 'https://budayakita.com/api/v1',
                prefixIcon: const Icon(Icons.link, size: 20),
                contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
              ),
              style: const TextStyle(fontSize: 12),
            ),
            const SizedBox(height: 10),

            // Quick Preset Buttons
            Wrap(
              spacing: 6,
              children: [
                ActionChip(
                  avatar: const Icon(Icons.cloud_done_rounded, size: 14, color: AppTheme.primary),
                  label: const Text('Production Live', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700)),
                  onPressed: () {
                    setState(() => _urlController.text = 'https://budayakita.com/api/v1');
                  },
                ),
                ActionChip(
                  avatar: const Icon(Icons.laptop_chromebook_rounded, size: 14),
                  label: const Text('Localhost Laptop', style: TextStyle(fontSize: 11)),
                  onPressed: () {
                    setState(() => _urlController.text = 'http://10.217.145.88:8000/api/v1');
                  },
                ),
              ],
            ),
            const SizedBox(height: 14),

            // Demo Tools Section
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: AppTheme.primaryLight,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: AppTheme.border),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Row(
                    children: [
                      Icon(Icons.science_rounded, size: 16, color: AppTheme.primaryDark),
                      SizedBox(width: 6),
                      Text(
                        'Simulasi & Demo UI',
                        style: TextStyle(fontSize: 12, fontWeight: FontWeight.w800, color: AppTheme.primaryDark),
                      ),
                    ],
                  ),
                  const SizedBox(height: 6),
                  const Text(
                    'Uji tampilan mode maintenance tanpa harus mematikan server asli:',
                    style: TextStyle(fontSize: 11, color: AppTheme.textSecondary),
                  ),
                  const SizedBox(height: 10),
                  SizedBox(
                    width: double.infinity,
                    child: OutlinedButton.icon(
                      onPressed: _previewMaintenanceScreen,
                      style: OutlinedButton.styleFrom(
                        backgroundColor: Colors.white,
                        side: const BorderSide(color: AppTheme.accentOrange),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                      ),
                      icon: const Icon(Icons.construction_rounded, size: 16, color: AppTheme.accentOrange),
                      label: const Text(
                        'Lihat Tampilan Maintenance',
                        style: TextStyle(fontSize: 11, fontWeight: FontWeight.w800, color: AppTheme.accentOrange),
                      ),
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 12),

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
