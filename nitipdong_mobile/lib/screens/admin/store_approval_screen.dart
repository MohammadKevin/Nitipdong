import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../services/api_service.dart';

class StoreApprovalScreen extends StatefulWidget {
  const StoreApprovalScreen({Key? key}) : super(key: key);

  @override
  State<StoreApprovalScreen> createState() => _StoreApprovalScreenState();
}

class _StoreApprovalScreenState extends State<StoreApprovalScreen> {
  bool _isLoading = true;
  List<dynamic> _pendingStores = [];

  @override
  void initState() {
    super.initState();
    _loadPendingStores();
  }

  Future<void> _loadPendingStores() async {
    setState(() => _isLoading = true);
    final stores = await ApiService.getAdminPendingStores();
    if (mounted) {
      setState(() {
        _pendingStores = stores;
        _isLoading = false;
      });
    }
  }

  Future<void> _handleApprove(int id, String name) async {
    setState(() => _isLoading = true);
    final res = await ApiService.approveStore(id);
    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(res['success'] == true ? '🎉 Toko $name berhasil disetujui!' : 'Gagal menyetujui toko: ${res['message']}'),
          backgroundColor: res['success'] == true ? AppTheme.success : Colors.red,
          behavior: SnackBarBehavior.floating,
        ),
      );
      _loadPendingStores();
    }
  }

  Future<void> _handleReject(int id, String name) async {
    setState(() => _isLoading = true);
    final res = await ApiService.rejectStore(id);
    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(res['success'] == true ? '❌ Pengajuan toko $name berhasil ditolak.' : 'Gagal menolak toko: ${res['message']}'),
          backgroundColor: res['success'] == true ? Colors.orange : Colors.red,
          behavior: SnackBarBehavior.floating,
        ),
      );
      _loadPendingStores();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: const Text('Registrasi Toko Pending'),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _pendingStores.isEmpty
              ? Center(
                  child: Padding(
                    padding: const EdgeInsets.all(32),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Container(
                          padding: const EdgeInsets.all(20),
                          decoration: BoxDecoration(color: Colors.orange.shade50, shape: BoxShape.circle),
                          child: const Icon(Icons.storefront_rounded, size: 56, color: Colors.orange),
                        ),
                        const SizedBox(height: 16),
                        const Text(
                          'Tidak Ada Pengajuan Toko',
                          style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppTheme.textPrimary),
                        ),
                        const SizedBox(height: 4),
                        const Text(
                          'Semua pengajuan pendaftaran toko baru telah selesai diproses.',
                          style: TextStyle(fontSize: 12, color: AppTheme.textSecondary),
                          textAlign: TextAlign.center,
                        ),
                      ],
                    ),
                  ),
                )
              : RefreshIndicator(
                  onRefresh: _loadPendingStores,
                  child: ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: _pendingStores.length,
                    itemBuilder: (context, index) {
                      final store = _pendingStores[index];
                      final ownerName = store['user'] != null ? store['user']['name'] : 'Pemilik';
                      final ownerEmail = store['user'] != null ? store['user']['email'] : '-';

                      return Container(
                        margin: const EdgeInsets.only(bottom: 16),
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(color: Colors.grey.shade200),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.01),
                              blurRadius: 10,
                              offset: const Offset(0, 4),
                            )
                          ],
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              children: [
                                Container(
                                  padding: const EdgeInsets.all(10),
                                  decoration: BoxDecoration(color: AppTheme.primaryLight, borderRadius: BorderRadius.circular(12)),
                                  child: const Icon(Icons.storefront_rounded, color: AppTheme.primary, size: 24),
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        store['name'] ?? 'Nama Toko',
                                        style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: AppTheme.textPrimary),
                                      ),
                                      const SizedBox(height: 2),
                                      Text(
                                        'Oleh: $ownerName ($ownerEmail)',
                                        style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 12),
                            const Divider(height: 1),
                            const SizedBox(height: 12),

                            // Store Details
                            _buildInfoRow(Icons.description_outlined, 'Deskripsi', store['description'] ?? 'Tidak ada deskripsi.'),
                            _buildInfoRow(Icons.location_on_outlined, 'Alamat', store['address'] ?? 'Tidak ada alamat.'),
                            _buildInfoRow(Icons.phone_outlined, 'Kontak', store['phone'] ?? '-'),

                            const SizedBox(height: 16),
                            Row(
                              children: [
                                Expanded(
                                  child: OutlinedButton.icon(
                                    icon: const Icon(Icons.close_rounded, size: 16, color: Colors.orange),
                                    label: const Text('Tolak', style: TextStyle(fontSize: 12, color: Colors.orange, fontWeight: FontWeight.bold)),
                                    style: OutlinedButton.styleFrom(
                                      side: const BorderSide(color: Colors.orange),
                                      padding: const EdgeInsets.symmetric(vertical: 12),
                                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                    ),
                                    onPressed: () => _handleReject(store['id'], store['name'] ?? 'Toko'),
                                  ),
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: ElevatedButton.icon(
                                    icon: const Icon(Icons.check_rounded, size: 16, color: Colors.white),
                                    label: const Text('Setujui', style: TextStyle(fontSize: 12, color: Colors.white, fontWeight: FontWeight.bold)),
                                    style: ElevatedButton.styleFrom(
                                      backgroundColor: AppTheme.success,
                                      padding: const EdgeInsets.symmetric(vertical: 12),
                                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                    ),
                                    onPressed: () => _handleApprove(store['id'], store['name'] ?? 'Toko'),
                                  ),
                                ),
                              ],
                            )
                          ],
                        ),
                      );
                    },
                  ),
                ),
    );
  }

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 6),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, size: 14, color: AppTheme.textMuted),
          const SizedBox(width: 6),
          Text(
            '$label: ',
            style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppTheme.textSecondary),
          ),
          Expanded(
            child: Text(
              value,
              style: const TextStyle(fontSize: 11, color: AppTheme.textPrimary),
            ),
          )
        ],
      ),
    );
  }
}
