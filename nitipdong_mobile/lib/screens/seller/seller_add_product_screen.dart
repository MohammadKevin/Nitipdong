import 'package:flutter/material.dart';
import '../../theme/app_theme.dart';
import '../../services/api_service.dart';

class SellerAddProductScreen extends StatefulWidget {
  const SellerAddProductScreen({Key? key}) : super(key: key);

  @override
  State<SellerAddProductScreen> createState() => _SellerAddProductScreenState();
}

class _SellerAddProductScreenState extends State<SellerAddProductScreen> {
  final _formKey = GlobalKey<FormState>();

  final TextEditingController _nameController = TextEditingController();
  final TextEditingController _priceController = TextEditingController();
  final TextEditingController _stockController = TextEditingController(text: '10');
  final TextEditingController _descController = TextEditingController();
  final TextEditingController _imageUrlController = TextEditingController(
    text: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&auto=format&fit=crop&q=80',
  );

  bool _isLoading = false;

  final List<String> _sampleImages = [
    'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&auto=format&fit=crop&q=80', // Headphone
    'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80', // Watch
    'https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=600&auto=format&fit=crop&q=80', // Smartwatch
    'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=600&auto=format&fit=crop&q=80', // Headset
    'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600&auto=format&fit=crop&q=80', // Shoes
    'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=600&auto=format&fit=crop&q=80', // Sneakers
  ];

  @override
  void dispose() {
    _nameController.dispose();
    _priceController.dispose();
    _stockController.dispose();
    _descController.dispose();
    _imageUrlController.dispose();
    super.dispose();
  }

  Future<void> _submitProduct() async {
    if (!_formKey.currentState!.validate()) return;

    final price = double.tryParse(_priceController.text.replaceAll(RegExp(r'[^0-9]'), '')) ?? 0;
    final stock = int.tryParse(_stockController.text.trim()) ?? 1;

    setState(() => _isLoading = true);

    final res = await ApiService.addSellerProduct(
      name: _nameController.text.trim(),
      price: price,
      stock: stock,
      description: _descController.text.trim(),
      imageUrl: _imageUrlController.text.trim(),
    );

    setState(() => _isLoading = false);

    if (res['success'] == true && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Produk berhasil ditambahkan! 🎉'), backgroundColor: Colors.green),
      );
      Navigator.pop(context, true);
    } else {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(res['message'] ?? 'Gagal menambahkan produk.'), backgroundColor: Colors.red),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      appBar: AppBar(
        title: const Text('Tambah Produk Baru 📦', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800)),
        backgroundColor: AppTheme.accentNavy,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text('Informasi Dasar Produk', style: TextStyle(fontSize: 13.5, fontWeight: FontWeight.w800, color: AppTheme.textPrimary)),
              const SizedBox(height: 12),

              // Nama Produk
              _buildField(
                controller: _nameController,
                label: 'Nama Produk',
                hint: 'Contoh: Wireless Noise-Cancelling Headphones Pro',
                icon: Icons.shopping_bag_outlined,
                validator: (val) => val == null || val.isEmpty ? 'Nama produk wajib diisi' : null,
              ),
              const SizedBox(height: 12),

              // Harga & Stok
              Row(
                children: [
                  Expanded(
                    flex: 3,
                    child: _buildField(
                      controller: _priceController,
                      label: 'Harga (Rp)',
                      hint: '150000',
                      icon: Icons.monetization_on_outlined,
                      keyboardType: TextInputType.number,
                      validator: (val) => val == null || val.isEmpty ? 'Harga wajib' : null,
                    ),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    flex: 2,
                    child: _buildField(
                      controller: _stockController,
                      label: 'Stok',
                      hint: '10',
                      icon: Icons.inventory_2_outlined,
                      keyboardType: TextInputType.number,
                      validator: (val) => val == null || val.isEmpty ? 'Stok wajib' : null,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),

              // Deskripsi
              _buildField(
                controller: _descController,
                label: 'Deskripsi Produk',
                hint: 'Jelaskan keunggulan produk, spesifikasi, dan kondisi barang...',
                icon: Icons.description_outlined,
                maxLines: 3,
                validator: (val) => val == null || val.isEmpty ? 'Deskripsi wajib diisi' : null,
              ),
              const SizedBox(height: 20),

              // Image Selector
              const Text('Foto Produk', style: TextStyle(fontSize: 13.5, fontWeight: FontWeight.w800, color: AppTheme.textPrimary)),
              const SizedBox(height: 8),

              // Preview Image
              Container(
                height: 160,
                width: double.infinity,
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: AppTheme.border),
                ),
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(14),
                  child: Image.network(
                    _imageUrlController.text,
                    fit: BoxFit.cover,
                    errorBuilder: (_, __, ___) => const Center(child: Icon(Icons.image_not_supported_rounded, color: Colors.grey)),
                  ),
                ),
              ),
              const SizedBox(height: 10),

              const Text('Pilih Contoh Foto Cepat:', style: TextStyle(fontSize: 11.5, color: AppTheme.textSecondary, fontWeight: FontWeight.w600)),
              const SizedBox(height: 6),
              SizedBox(
                height: 54,
                child: ListView.builder(
                  scrollDirection: Axis.horizontal,
                  itemCount: _sampleImages.length,
                  itemBuilder: (ctx, idx) {
                    final img = _sampleImages[idx];
                    final isSelected = _imageUrlController.text == img;
                    return GestureDetector(
                      onTap: () => setState(() => _imageUrlController.text = img),
                      child: Container(
                        margin: const EdgeInsets.only(right: 8),
                        width: 54,
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(10),
                          border: Border.all(color: isSelected ? AppTheme.primary : Colors.grey.shade300, width: isSelected ? 2 : 1),
                        ),
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(8),
                          child: Image.network(img, fit: BoxFit.cover),
                        ),
                      ),
                    );
                  },
                ),
              ),
              const SizedBox(height: 24),

              // Submit Button
              SizedBox(
                width: double.infinity,
                height: 50,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.primary,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                  ),
                  onPressed: _isLoading ? null : _submitProduct,
                  child: _isLoading
                      ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2))
                      : const Text('Simpan & Terbitkan Produk 🚀', style: TextStyle(fontSize: 13.5, fontWeight: FontWeight.w800, color: Colors.white)),
                ),
              ),
              const SizedBox(height: 20),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildField({
    required TextEditingController controller,
    required String label,
    required IconData icon,
    String? hint,
    int maxLines = 1,
    TextInputType keyboardType = TextInputType.text,
    String? Function(String?)? validator,
  }) {
    return TextFormField(
      controller: controller,
      keyboardType: keyboardType,
      maxLines: maxLines,
      validator: validator,
      style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
      decoration: InputDecoration(
        labelText: label,
        hintText: hint,
        prefixIcon: Icon(icon, color: AppTheme.primary, size: 20),
        filled: true,
        fillColor: Colors.white,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppTheme.border),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppTheme.border),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: AppTheme.primary, width: 1.5),
        ),
      ),
    );
  }
}
