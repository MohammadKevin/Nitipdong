import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../services/api_service.dart';

class CategoryManagementScreen extends StatefulWidget {
  const CategoryManagementScreen({Key? key}) : super(key: key);

  @override
  State<CategoryManagementScreen> createState() => _CategoryManagementScreenState();
}

class _CategoryManagementScreenState extends State<CategoryManagementScreen> {
  bool _isLoading = true;
  List<dynamic> _categories = [];
  final TextEditingController _categoryNameController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _loadCategories();
  }

  @override
  void dispose() {
    _categoryNameController.dispose();
    super.dispose();
  }

  Future<void> _loadCategories() async {
    setState(() => _isLoading = true);
    final categories = await ApiService.getAdminCategories();
    if (mounted) {
      setState(() {
        _categories = categories;
        _isLoading = false;
      });
    }
  }

  void _showCategoryDialog({Map<String, dynamic>? category}) {
    final isEdit = category != null;
    _categoryNameController.text = isEdit ? category['name'] ?? '' : '';

    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Text(isEdit ? 'Edit Kategori' : 'Tambah Kategori Baru', style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold)),
        content: TextField(
          controller: _categoryNameController,
          decoration: InputDecoration(
            labelText: 'Nama Kategori',
            hintText: 'Contoh: Makanan & Minuman',
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
          ),
        ),
        actions: [
          TextButton(
            child: const Text('Batal', style: TextStyle(color: AppTheme.textSecondary)),
            onPressed: () => Navigator.pop(ctx),
          ),
          ElevatedButton(
            child: Text(isEdit ? 'Simpan' : 'Tambah', style: const TextStyle(color: Colors.white)),
            onPressed: () async {
              final name = _categoryNameController.text.trim();
              if (name.isEmpty) return;

              Navigator.pop(ctx);
              setState(() => _isLoading = true);

              final res = isEdit
                  ? await ApiService.updateCategory(category['id'], name)
                  : await ApiService.createCategory(name);

              if (mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text(res['message']),
                    backgroundColor: res['success'] == true ? AppTheme.success : Colors.red,
                    behavior: SnackBarBehavior.floating,
                  ),
                );
                _loadCategories();
              }
            },
          ),
        ],
      ),
    );
  }

  Future<void> _handleDelete(int id, String name) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Hapus Kategori?', style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold)),
        content: Text("Apakah Anda yakin ingin menghapus kategori '$name'? Tindakan ini tidak dapat dibatalkan."),
        actions: [
          TextButton(
            child: const Text('Batal'),
            onPressed: () => Navigator.pop(ctx, false),
          ),
          TextButton(
            child: const Text('Hapus', style: TextStyle(color: Colors.red)),
            onPressed: () => Navigator.pop(ctx, true),
          ),
        ],
      ),
    );

    if (confirm == true) {
      setState(() => _isLoading = true);
      final res = await ApiService.deleteCategory(id);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(res['message']),
            backgroundColor: res['success'] == true ? AppTheme.success : Colors.red,
            behavior: SnackBarBehavior.floating,
          ),
        );
        _loadCategories();
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey.shade50,
      appBar: AppBar(
        title: const Text('Manajemen Kategori'),
        actions: [
          IconButton(
            icon: const Icon(Icons.add_rounded),
            onPressed: () => _showCategoryDialog(),
          ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _categories.isEmpty
              ? Center(
                  child: Padding(
                    padding: const EdgeInsets.all(32),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Container(
                          padding: const EdgeInsets.all(20),
                          decoration: BoxDecoration(color: Colors.purple.shade50, shape: BoxShape.circle),
                          child: const Icon(Icons.grid_view_rounded, size: 56, color: Colors.purple),
                        ),
                        const SizedBox(height: 16),
                        const Text(
                          'Kategori Kosong',
                          style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppTheme.textPrimary),
                        ),
                        const SizedBox(height: 4),
                        const Text(
                          'Belum ada kategori yang ditambahkan ke sistem.',
                          style: TextStyle(fontSize: 12, color: AppTheme.textSecondary),
                        ),
                      ],
                    ),
                  ),
                )
              : RefreshIndicator(
                  onRefresh: _loadCategories,
                  child: ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: _categories.length,
                    itemBuilder: (context, index) {
                      final category = _categories[index];
                      final prodCount = category['products_count'] ?? 0;

                      return Container(
                        margin: const EdgeInsets.only(bottom: 12),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(14),
                          border: Border.all(color: Colors.grey.shade200),
                        ),
                        child: ListTile(
                          contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
                          leading: Container(
                            padding: const EdgeInsets.all(8),
                            decoration: BoxDecoration(color: Colors.purple.shade50, shape: BoxShape.circle),
                            child: const Icon(Icons.grid_view_rounded, color: Colors.purple, size: 22),
                          ),
                          title: Text(
                            category['name'] ?? 'Kategori',
                            style: const TextStyle(fontSize: 13.5, fontWeight: FontWeight.bold, color: AppTheme.textPrimary),
                          ),
                          subtitle: Text(
                            'Slug: ${category['slug'] ?? '-'}  |  $prodCount Produk',
                            style: const TextStyle(fontSize: 11, color: AppTheme.textSecondary),
                          ),
                          trailing: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              IconButton(
                                icon: const Icon(Icons.edit_outlined, color: Colors.blue, size: 20),
                                onPressed: () => _showCategoryDialog(category: category),
                              ),
                              IconButton(
                                icon: const Icon(Icons.delete_outline_rounded, color: Colors.red, size: 20),
                                onPressed: () => _handleDelete(category['id'], category['name'] ?? 'Kategori'),
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                ),
    );
  }
}
