import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:provider/provider.dart';
import '../../models/product_model.dart';
import '../../services/api_service.dart';
import '../../theme/app_theme.dart';
import '../../providers/cart_provider.dart';
import '../../providers/auth_provider.dart';
import '../auth/login_screen.dart';
import 'store_screen.dart';

class ProductDetailScreen extends StatefulWidget {
  final dynamic productId;

  const ProductDetailScreen({Key? key, required this.productId}) : super(key: key);

  @override
  State<ProductDetailScreen> createState() => _ProductDetailScreenState();
}

class _ProductDetailScreenState extends State<ProductDetailScreen> {
  ProductModel? _product;
  bool _isLoading = true;
  int _currentImageIndex = 0;
  int _quantity = 1;
  final _commentController = TextEditingController();
  final _replyController = TextEditingController();
  int? _activeReplyDiscussionId;
  bool _isPostingComment = false;
  bool _isPostingReply = false;

  @override
  void initState() {
    super.initState();
    _fetchDetail();
  }

  Future<void> _fetchDetail() async {
    setState(() => _isLoading = true);
    final p = await ApiService.getProductDetail(widget.productId);
    setState(() {
      _product = p;
      _isLoading = false;
    });
  }

  String _formatCurrency(double amount) {
    final formatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    return formatter.format(amount);
  }

  String _stripHtml(String htmlString) {
    if (htmlString.isEmpty) return '';
    String result = htmlString.replaceAll(RegExp(r'<[^>]*>', multiLine: true, caseSensitive: false), '');
    result = result.replaceAll('&nbsp;', ' ')
                   .replaceAll('&amp;', '&')
                   .replaceAll('&quot;', '"')
                   .replaceAll('&lt;', '<')
                   .replaceAll('&gt;', '>');
    return result.trim();
  }

  Future<void> _submitComment() async {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    if (!authProvider.isAuthenticated) {
      Navigator.push(context, MaterialPageRoute(builder: (context) => const LoginScreen()));
      return;
    }
    if (_commentController.text.trim().isEmpty) return;

    setState(() => _isPostingComment = true);
    final result = await ApiService.postDiscussion(_product!.id, _commentController.text.trim());
    setState(() {
      _isPostingComment = false;
      if (result['success'] == true) {
        _commentController.clear();
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Pertanyaan berhasil dikirim!'), backgroundColor: AppTheme.success),
        );
        _fetchDetail();
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(result['message'] ?? 'Gagal mengirim pertanyaan'), backgroundColor: Colors.red),
        );
      }
    });
  }

  Future<void> _submitReply(int discussionId) async {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    if (!authProvider.isAuthenticated) {
      Navigator.push(context, MaterialPageRoute(builder: (context) => const LoginScreen()));
      return;
    }
    if (_replyController.text.trim().isEmpty) return;

    setState(() => _isPostingReply = true);
    final result = await ApiService.postReply(_product!.id, discussionId, _replyController.text.trim());
    setState(() {
      _isPostingReply = false;
      if (result['success'] == true) {
        _replyController.clear();
        _activeReplyDiscussionId = null;
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Balasan berhasil dikirim!'), backgroundColor: AppTheme.success),
        );
        _fetchDetail();
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(result['message'] ?? 'Gagal mengirim balasan'), backgroundColor: Colors.red),
        );
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final cartProvider = Provider.of<CartProvider>(context);
    final authProvider = Provider.of<AuthProvider>(context);

    if (_isLoading) {
      return Scaffold(
        appBar: AppBar(),
        body: const Center(child: CircularProgressIndicator(color: AppTheme.primary)),
      );
    }

    if (_product == null) {
      return Scaffold(
        appBar: AppBar(),
        body: const Center(child: Text('Produk tidak ditemukan.')),
      );
    }

    final p = _product!;
    final images = p.images.isNotEmpty ? p.images : [p.imageUrl];

    return Scaffold(
      appBar: AppBar(
        actions: [
          IconButton(
            icon: const Icon(Icons.share_outlined),
            onPressed: () {},
          ),
          IconButton(
            icon: const Icon(Icons.favorite_border_rounded),
            onPressed: () {},
          ),
        ],
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // 1. Image Carousel Gallery
            Stack(
              children: [
                SizedBox(
                  height: 340,
                  child: PageView.builder(
                    itemCount: images.length,
                    onPageChanged: (i) => setState(() => _currentImageIndex = i),
                    itemBuilder: (context, index) {
                      return CachedNetworkImage(
                        imageUrl: images[index],
                        fit: BoxFit.cover,
                        width: double.infinity,
                        placeholder: (context, url) => Container(color: Colors.grey.shade100),
                        errorWidget: (context, url, error) => Container(color: Colors.grey.shade100),
                      );
                    },
                  ),
                ),
                if (images.length > 1)
                  Positioned(
                    bottom: 12,
                    right: 16,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: Colors.black54,
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Text(
                        '${_currentImageIndex + 1}/${images.length}',
                        style: const TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.w700),
                      ),
                    ),
                  ),
              ],
            ),

            // 2. Price & Title
            Container(
              color: Colors.white,
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      Text(
                        _formatCurrency(p.finalPrice),
                        style: const TextStyle(
                          fontSize: 22,
                          fontWeight: FontWeight.w900,
                          color: AppTheme.primaryDark,
                        ),
                      ),
                      if (p.hasDiscount) ...[
                        const SizedBox(width: 8),
                        Text(
                          _formatCurrency(p.price),
                          style: const TextStyle(
                            fontSize: 13,
                            color: AppTheme.textMuted,
                            decoration: TextDecoration.lineThrough,
                          ),
                        ),
                        const SizedBox(width: 6),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 2),
                          decoration: BoxDecoration(
                            color: AppTheme.accentOrange,
                            borderRadius: BorderRadius.circular(4),
                          ),
                          child: Text(
                            '-${p.discountPercentage}%',
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 10,
                              fontWeight: FontWeight.w800,
                            ),
                          ),
                        ),
                      ],
                    ],
                  ),
                  const SizedBox(height: 8),

                  Text(
                    p.name,
                    style: const TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w700,
                      color: AppTheme.textPrimary,
                      height: 1.3,
                    ),
                  ),
                  const SizedBox(height: 12),

                  // Rating & Sales
                  Row(
                    children: [
                      const Icon(Icons.star_rounded, size: 16, color: Colors.amber),
                      const SizedBox(width: 4),
                      Text(
                        p.rating.toStringAsFixed(1),
                        style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700),
                      ),
                      const SizedBox(width: 12),
                      const Text('•', style: TextStyle(color: AppTheme.border)),
                      const SizedBox(width: 12),
                      Text(
                        '${p.formattedSold} terjual',
                        style: const TextStyle(fontSize: 12, color: AppTheme.textSecondary),
                      ),
                      const Spacer(),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                        decoration: BoxDecoration(
                          color: Colors.green.shade50,
                          borderRadius: BorderRadius.circular(6),
                          border: Border.all(color: Colors.green.shade200),
                        ),
                        child: Row(
                          children: [
                            Icon(Icons.local_shipping_outlined, size: 12, color: Colors.green.shade700),
                            const SizedBox(width: 4),
                            Text(
                              'Bebas Ongkir',
                              style: TextStyle(
                                fontSize: 10,
                                fontWeight: FontWeight.w700,
                                color: Colors.green.shade700,
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

            const SizedBox(height: 10),

            // 3. Store Profile
            Container(
              color: Colors.white,
              padding: const EdgeInsets.all(16),
              child: Row(
                children: [
                  Container(
                    width: 42,
                    height: 42,
                    decoration: BoxDecoration(
                      color: AppTheme.primaryLight,
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(color: AppTheme.border),
                    ),
                    child: const Center(
                      child: Icon(Icons.storefront_rounded, color: AppTheme.primary),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          p.storeName,
                          style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700),
                        ),
                        Text(
                          p.city,
                          style: const TextStyle(fontSize: 11, color: AppTheme.textMuted),
                        ),
                      ],
                    ),
                  ),
                  OutlinedButton(
                    onPressed: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => StoreScreen(
                            storeId: p.storeId,
                            storeName: p.storeName,
                            city: p.city,
                          ),
                        ),
                      );
                    },
                    style: OutlinedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                      side: const BorderSide(color: AppTheme.primary),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                    ),
                    child: const Text(
                      'Kunjungi Toko',
                      style: TextStyle(color: AppTheme.primary, fontSize: 11, fontWeight: FontWeight.w700),
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 10),

            // 4. Description
            Container(
              width: double.infinity,
              color: Colors.white,
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Deskripsi Produk',
                    style: TextStyle(fontSize: 14, fontWeight: FontWeight.w700),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    p.description.isNotEmpty
                        ? _stripHtml(p.description)
                        : 'Produk original berkualitas tinggi terjamin di NitipDong dengan garansi pengembalian 100%.',
                    style: const TextStyle(fontSize: 12, color: AppTheme.textSecondary, height: 1.5),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 10),

            // 5. Discussion Q&A Section
            Container(
              width: double.infinity,
              color: Colors.white,
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      const Text(
                        'Diskusi & Tanya Jawab',
                        style: TextStyle(fontSize: 14, fontWeight: FontWeight.w700),
                      ),
                      const SizedBox(width: 8),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                        decoration: BoxDecoration(
                          color: AppTheme.primaryLight,
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: Text(
                          '${p.discussions.length}',
                          style: const TextStyle(
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                            color: AppTheme.primaryDark,
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),

                  // Ask Question Input
                  Row(
                    children: [
                      Expanded(
                        child: TextField(
                          controller: _commentController,
                          decoration: const InputDecoration(
                            hintText: 'Tanyakan sesuatu tentang produk ini...',
                            contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                          ),
                          style: const TextStyle(fontSize: 12),
                        ),
                      ),
                      const SizedBox(width: 8),
                      ElevatedButton(
                        onPressed: _isPostingComment ? null : _submitComment,
                        style: ElevatedButton.styleFrom(
                          minimumSize: const Size(0, 40),
                          padding: const EdgeInsets.symmetric(horizontal: 16),
                        ),
                        child: _isPostingComment
                            ? const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                            : const Text('Kirim', style: TextStyle(fontSize: 12)),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),

                  // Discussions List
                  p.discussions.isEmpty
                      ? const Padding(
                          padding: EdgeInsets.symmetric(vertical: 20),
                          child: Center(
                            child: Text(
                              'Belum ada diskusi. Jadilah yang pertama bertanya!',
                              style: TextStyle(color: AppTheme.textMuted, fontSize: 11),
                            ),
                          ),
                        )
                      : ListView.builder(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          itemCount: p.discussions.length,
                          itemBuilder: (context, dIndex) {
                            final disc = p.discussions[dIndex];
                            final isReplyingThis = _activeReplyDiscussionId == disc.id;

                            return Container(
                              margin: const EdgeInsets.only(bottom: 16),
                              padding: const EdgeInsets.all(12),
                              decoration: BoxDecoration(
                                color: Colors.grey.shade50,
                                borderRadius: BorderRadius.circular(10),
                                border: Border.all(color: Colors.grey.shade200),
                              ),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  // Question Header
                                  Row(
                                    children: [
                                      Text(
                                        disc.userName,
                                        style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 11),
                                      ),
                                      if (disc.isSeller) ...[
                                        const SizedBox(width: 6),
                                        Container(
                                          padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 1),
                                          decoration: BoxDecoration(
                                            color: AppTheme.primary,
                                            borderRadius: BorderRadius.circular(4),
                                          ),
                                          child: const Text('Penjual', style: TextStyle(color: Colors.white, fontSize: 8, fontWeight: FontWeight.bold)),
                                        ),
                                      ],
                                      const Spacer(),
                                      Text(
                                        disc.createdAt,
                                        style: const TextStyle(color: AppTheme.textMuted, fontSize: 9),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 6),
                                  Text(
                                    disc.body,
                                    style: const TextStyle(fontSize: 12, height: 1.4, color: AppTheme.textPrimary),
                                  ),
                                  const SizedBox(height: 6),

                                  // Reply action link
                                  GestureDetector(
                                    onTap: () {
                                      setState(() {
                                        if (isReplyingThis) {
                                          _activeReplyDiscussionId = null;
                                        } else {
                                          _activeReplyDiscussionId = disc.id;
                                        }
                                      });
                                    },
                                    child: Row(
                                      children: [
                                        Icon(Icons.reply, size: 12, color: isReplyingThis ? Colors.red : AppTheme.primary),
                                        const SizedBox(width: 4),
                                        Text(
                                          isReplyingThis ? 'Batal' : 'Balas',
                                          style: TextStyle(
                                            color: isReplyingThis ? Colors.red : AppTheme.primary,
                                            fontSize: 10,
                                            fontWeight: FontWeight.bold,
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),

                                  // Inline Reply Input
                                  if (isReplyingThis) ...[
                                    const SizedBox(height: 8),
                                    Row(
                                      children: [
                                        Expanded(
                                          child: TextField(
                                            controller: _replyController,
                                            decoration: const InputDecoration(
                                              hintText: 'Tulis balasan...',
                                              contentPadding: EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                                            ),
                                            style: const TextStyle(fontSize: 11),
                                          ),
                                        ),
                                        const SizedBox(width: 6),
                                        ElevatedButton(
                                          onPressed: _isPostingReply ? null : () => _submitReply(disc.id),
                                          style: ElevatedButton.styleFrom(
                                            minimumSize: const Size(0, 34),
                                            padding: const EdgeInsets.symmetric(horizontal: 12),
                                          ),
                                          child: _isPostingReply
                                              ? const SizedBox(width: 12, height: 12, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                                              : const Text('Balas', style: TextStyle(fontSize: 11)),
                                        ),
                                      ],
                                    ),
                                  ],

                                  // Replies list
                                  if (disc.replies.isNotEmpty) ...[
                                    const SizedBox(height: 10),
                                    const Divider(height: 1),
                                    const SizedBox(height: 10),
                                    ListView.builder(
                                      shrinkWrap: true,
                                      physics: const NeverScrollableScrollPhysics(),
                                      itemCount: disc.replies.length,
                                      itemBuilder: (context, rIndex) {
                                        final rep = disc.replies[rIndex];
                                        return Container(
                                          margin: const EdgeInsets.only(bottom: 10, left: 10),
                                          child: Column(
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              Row(
                                                children: [
                                                  Text(
                                                    rep.userName,
                                                    style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 11, color: AppTheme.textPrimary),
                                                  ),
                                                  if (rep.isSeller) ...[
                                                    const SizedBox(width: 6),
                                                    Container(
                                                      padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 1),
                                                      decoration: BoxDecoration(
                                                        color: AppTheme.primary,
                                                        borderRadius: BorderRadius.circular(4),
                                                      ),
                                                      child: const Text('Penjual', style: TextStyle(color: Colors.white, fontSize: 8, fontWeight: FontWeight.bold)),
                                                    ),
                                                  ],
                                                  const Spacer(),
                                                  Text(
                                                    rep.createdAt,
                                                    style: const TextStyle(color: AppTheme.textMuted, fontSize: 9),
                                                  ),
                                                ],
                                              ),
                                              const SizedBox(height: 4),
                                              Text(
                                                rep.body,
                                                style: const TextStyle(fontSize: 11.5, height: 1.3, color: AppTheme.textSecondary),
                                              ),
                                            ],
                                          ),
                                        );
                                      },
                                    ),
                                  ],
                                ],
                              ),
                            );
                          },
                        ),
                ],
              ),
            ),

            const SizedBox(height: 100),
          ],
        ),
      ),
      bottomSheet: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        decoration: const BoxDecoration(
          color: Colors.white,
          border: Border(top: BorderSide(color: AppTheme.border)),
          boxShadow: [
            BoxShadow(color: Colors.black12, blurRadius: 10, offset: Offset(0, -2)),
          ],
        ),
        child: SafeArea(
          child: Row(
            children: [
              // Chat Toko
              IconButton.outlined(
                icon: const Icon(Icons.chat_bubble_outline, color: AppTheme.primaryDark),
                style: OutlinedButton.styleFrom(
                  side: const BorderSide(color: AppTheme.border),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                ),
                onPressed: () {},
              ),
              const SizedBox(width: 10),

              // Add to Cart
              Expanded(
                child: OutlinedButton.icon(
                  icon: const Icon(Icons.shopping_cart_outlined, size: 18),
                  label: const Text('+ Keranjang', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
                  style: OutlinedButton.styleFrom(
                    foregroundColor: AppTheme.primary,
                    side: const BorderSide(color: AppTheme.primary),
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  onPressed: () async {
                    if (!authProvider.isAuthenticated) {
                      Navigator.push(context, MaterialPageRoute(builder: (context) => const LoginScreen()));
                      return;
                    }

                    final success = await cartProvider.addToCart(p.id, _quantity);
                    if (mounted) {
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(
                          content: Text(success ? 'Berhasil ditambahkan ke keranjang!' : 'Gagal menambahkan'),
                          backgroundColor: success ? AppTheme.primaryDark : Colors.red,
                        ),
                      );
                    }
                  },
                ),
              ),
              const SizedBox(width: 10),

              // Buy Now
              Expanded(
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.primary,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  onPressed: () async {
                    if (!authProvider.isAuthenticated) {
                      Navigator.push(context, MaterialPageRoute(builder: (context) => const LoginScreen()));
                      return;
                    }

                    await cartProvider.addToCart(p.id, _quantity);
                    if (mounted) {
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(content: Text('Menuju proses checkout...')),
                      );
                    }
                  },
                  child: const Text('Beli Sekarang', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w800)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
