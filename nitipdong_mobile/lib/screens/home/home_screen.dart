import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../providers/product_provider.dart';
import '../../widgets/banner_carousel.dart';
import '../../widgets/category_item.dart';
import '../../widgets/flash_sale_section.dart';
import '../../widgets/product_card.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({Key? key}) : super(key: key);

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<ProductProvider>(context, listen: false).fetchHomeData();
    });
  }

  @override
  Widget build(BuildContext context) {
    final productProvider = Provider.of<ProductProvider>(context);

    return Scaffold(
      appBar: AppBar(
        titleSpacing: 16,
        title: Row(
          children: [
            // Logo
            Container(
              width: 32,
              height: 32,
              decoration: BoxDecoration(
                color: AppTheme.primaryLight,
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: AppTheme.border),
              ),
              child: const Center(
                child: Icon(Icons.shopping_bag, color: AppTheme.primary, size: 18),
              ),
            ),
            const SizedBox(width: 10),
            RichText(
              text: const TextSpan(
                style: TextStyle(
                  fontSize: 17,
                  fontWeight: FontWeight.w800,
                  color: AppTheme.textPrimary,
                  letterSpacing: -0.5,
                ),
                children: [
                  TextSpan(text: 'Nitip'),
                  TextSpan(
                    text: 'Dong',
                    style: TextStyle(color: AppTheme.primary, fontWeight: FontWeight.w900),
                  ),
                ],
              ),
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.notifications_none_rounded, color: AppTheme.textPrimary),
            onPressed: () {},
          ),
          IconButton(
            icon: const Icon(Icons.chat_bubble_outline_rounded, color: AppTheme.textPrimary),
            onPressed: () {},
          ),
          const SizedBox(width: 8),
        ],
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(56),
          child: Padding(
            padding: const EdgeInsets.fromLTRB(16, 0, 16, 10),
            child: TextField(
              controller: _searchController,
              onSubmitted: (val) => productProvider.search(val),
              decoration: InputDecoration(
                hintText: 'Cari di NitipDong (iPhone, Sepatu, Skincare)...',
                prefixIcon: const Icon(Icons.search, color: AppTheme.textMuted, size: 20),
                suffixIcon: _searchController.text.isNotEmpty
                    ? IconButton(
                        icon: const Icon(Icons.clear, size: 18),
                        onPressed: () {
                          _searchController.clear();
                          productProvider.search('');
                        },
                      )
                    : null,
                contentPadding: const EdgeInsets.symmetric(vertical: 10),
                fillColor: AppTheme.background,
              ),
            ),
          ),
        ),
      ),
      body: RefreshIndicator(
        onRefresh: () => productProvider.fetchHomeData(),
        color: AppTheme.primary,
        child: productProvider.isLoading && productProvider.products.isEmpty
            ? const Center(child: CircularProgressIndicator(color: AppTheme.primary))
            : SingleChildScrollView(
                padding: const EdgeInsets.symmetric(vertical: 12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // 1. Promo Banners Carousel
                    BannerCarousel(banners: productProvider.banners),
                    const SizedBox(height: 14),

                    // 2. Categories
                    if (productProvider.categories.isNotEmpty) ...[
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            const Text(
                              'Kategori Pilihan',
                              style: TextStyle(
                                fontSize: 14,
                                fontWeight: FontWeight.w800,
                                color: AppTheme.textPrimary,
                              ),
                            ),
                            TextButton(
                              onPressed: () => productProvider.filterByCategory(''),
                              child: const Text(
                                'Semua',
                                style: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w700,
                                  color: AppTheme.primary,
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 4),
                      SizedBox(
                        height: 82,
                        child: ListView.builder(
                          scrollDirection: Axis.horizontal,
                          padding: const EdgeInsets.symmetric(horizontal: 12),
                          itemCount: productProvider.categories.length,
                          itemBuilder: (context, index) {
                            final cat = productProvider.categories[index];
                            final isSelected = productProvider.selectedCategory == cat.slug;
                            return CategoryItem(
                              category: cat,
                              isSelected: isSelected,
                              onTap: () {
                                if (isSelected) {
                                  productProvider.filterByCategory('');
                                } else {
                                  productProvider.filterByCategory(cat.slug);
                                }
                              },
                            );
                          },
                        ),
                      ),
                    ],

                    // 3. Flash Sale Section
                    if (productProvider.flashSaleItems.isNotEmpty)
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                        child: FlashSaleSection(
                          items: productProvider.flashSaleItems,
                          remainingSeconds: productProvider.flashSaleRemainingSeconds,
                        ),
                      ),

                    // 4. Product Feed Section Header
                    Padding(
                      padding: const EdgeInsets.fromLTRB(16, 12, 16, 10),
                      child: Row(
                        children: [
                          Container(
                            width: 3.5,
                            height: 16,
                            decoration: BoxDecoration(
                              color: AppTheme.primary,
                              borderRadius: BorderRadius.circular(2),
                            ),
                          ),
                          const SizedBox(width: 8),
                          const Text(
                            'Rekomendasi Produk Pilihan',
                            style: TextStyle(
                              fontSize: 14,
                              fontWeight: FontWeight.w800,
                              color: AppTheme.textPrimary,
                            ),
                          ),
                        ],
                      ),
                    ),

                    // 5. 2-Column Product Grid
                    if (productProvider.products.isEmpty)
                      const Padding(
                        padding: EdgeInsets.all(40),
                        child: Center(
                          child: Text(
                            'Tidak ada produk yang cocok dengan pencarian.',
                            style: TextStyle(color: AppTheme.textMuted, fontSize: 12),
                          ),
                        ),
                      )
                    else
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                        child: GridView.builder(
                          physics: const NeverScrollableScrollPhysics(),
                          shrinkWrap: true,
                          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                            crossAxisCount: 2,
                            crossAxisSpacing: 10,
                            mainAxisSpacing: 10,
                            childAspectRatio: 0.62,
                          ),
                          itemCount: productProvider.products.length,
                          itemBuilder: (context, index) {
                            return ProductCard(product: productProvider.products[index]);
                          },
                        ),
                      ),

                    const SizedBox(height: 20),
                  ],
                ),
              ),
      ),
    );
  }
}
