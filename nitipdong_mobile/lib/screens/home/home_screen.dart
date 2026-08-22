import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../theme/app_theme.dart';
import '../../providers/auth_provider.dart';
import '../../providers/product_provider.dart';
import '../../widgets/banner_carousel.dart';
import '../../widgets/category_item.dart';
import '../../widgets/flash_sale_section.dart';
import '../../widgets/in_app_update_banner.dart';
import '../../widgets/product_card.dart';
import '../../widgets/server_config_dialog.dart';

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
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final productProvider = Provider.of<ProductProvider>(context);
    final authProvider = Provider.of<AuthProvider>(context);
    final user = authProvider.user;

    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        scrolledUnderElevation: 2,
        titleSpacing: 16,
        title: Row(
          children: [
            // User Avatar or App Brand Mark
            Container(
              width: 36,
              height: 36,
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [AppTheme.primary, AppTheme.primaryDark],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(12),
                boxShadow: [
                  BoxShadow(
                    color: AppTheme.primary.withOpacity(0.3),
                    blurRadius: 8,
                    offset: const Offset(0, 3),
                  ),
                ],
              ),
              child: Center(
                child: Text(
                  user?.name.isNotEmpty == true ? user!.name[0].toUpperCase() : 'N',
                  style: const TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.w900,
                    fontSize: 16,
                  ),
                ),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    user != null ? 'Hai, ${user.name.split(' ').first} 👋' : 'Selamat Datang di NitipDong!',
                    style: const TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.w800,
                      color: AppTheme.textPrimary,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const Row(
                    children: [
                      Icon(Icons.location_on_outlined, size: 11, color: AppTheme.primary),
                      SizedBox(width: 2),
                      Text(
                        'Kirim ke Indonesia • Gratis Ongkir Rp0',
                        style: TextStyle(
                          fontSize: 10,
                          color: AppTheme.textMuted,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
        actions: [
          // Server Config / Dev Tools icon
          IconButton(
            tooltip: 'Pengaturan Server API',
            icon: Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: AppTheme.primaryLight,
                borderRadius: BorderRadius.circular(10),
              ),
              child: const Icon(Icons.dns_rounded, color: AppTheme.primaryDark, size: 16),
            ),
            onPressed: () => ServerConfigDialog.show(context, onSaved: () {
              productProvider.fetchHomeData();
            }),
          ),
          // Notification icon
          IconButton(
            icon: const Icon(Icons.notifications_outlined, color: AppTheme.textPrimary, size: 22),
            onPressed: () {
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(
                  content: Text('Tidak ada notifikasi baru untuk saat ini. ✨'),
                  duration: Duration(seconds: 2),
                  behavior: SnackBarBehavior.floating,
                ),
              );
            },
          ),
          const SizedBox(width: 8),
        ],
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(56),
          child: Padding(
            padding: const EdgeInsets.fromLTRB(16, 0, 16, 10),
            child: Container(
              decoration: BoxDecoration(
                color: AppTheme.background,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: AppTheme.border),
              ),
              child: TextField(
                controller: _searchController,
                onSubmitted: (val) => productProvider.search(val),
                decoration: InputDecoration(
                  hintText: 'Cari produk jastip, branded, atau skincare...',
                  hintStyle: const TextStyle(fontSize: 12, color: AppTheme.textMuted),
                  prefixIcon: const Icon(Icons.search_rounded, color: AppTheme.primary, size: 20),
                  suffixIcon: _searchController.text.isNotEmpty
                      ? IconButton(
                          icon: const Icon(Icons.clear, size: 18, color: AppTheme.textMuted),
                          onPressed: () {
                            _searchController.clear();
                            productProvider.search('');
                            setState(() {});
                          },
                        )
                      : null,
                  contentPadding: const EdgeInsets.symmetric(vertical: 10),
                  border: InputBorder.none,
                  enabledBorder: InputBorder.none,
                  focusedBorder: InputBorder.none,
                ),
                onChanged: (val) => setState(() {}),
              ),
            ),
          ),
        ),
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          await productProvider.fetchHomeData();
        },
        color: AppTheme.primary,
        child: productProvider.isLoading && productProvider.products.isEmpty
            ? const Center(
                child: CircularProgressIndicator(color: AppTheme.primary),
              )
            : SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.only(bottom: 30),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // ══════════════════════════════════════════════════
                    // 1. SMART IN-APP UPDATE BANNER / CARD
                    // ══════════════════════════════════════════════════
                    const InAppUpdateBanner(),

                    // ══════════════════════════════════════════════════
                    // 2. PROMO BANNERS CAROUSEL
                    // ══════════════════════════════════════════════════
                    if (productProvider.banners.isNotEmpty)
                      BannerCarousel(banners: productProvider.banners),

                    const SizedBox(height: 14),

                    // ══════════════════════════════════════════════════
                    // 3. QUICK SERVICES / QUICK ACTIONS ROW
                    // ══════════════════════════════════════════════════
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 16),
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 14),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(color: AppTheme.border),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.02),
                              blurRadius: 10,
                              offset: const Offset(0, 4),
                            ),
                          ],
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceAround,
                          children: [
                            _buildQuickActionItem(
                              icon: Icons.flight_takeoff_rounded,
                              iconColor: const Color(0xFF0284C7),
                              bgColor: const Color(0xFFE0F2FE),
                              label: 'Jastip Global',
                              onTap: () {
                                productProvider.filterByCategory('jastip-luar-negeri');
                              },
                            ),
                            _buildQuickActionItem(
                              icon: Icons.verified_rounded,
                              iconColor: const Color(0xFF059669),
                              bgColor: const Color(0xFFD1FAE5),
                              label: 'Official Store',
                              onTap: () {
                                productProvider.filterByCategory('official-store');
                              },
                            ),
                            _buildQuickActionItem(
                              icon: Icons.local_shipping_rounded,
                              iconColor: const Color(0xFFD97706),
                              bgColor: const Color(0xFFFEF3C7),
                              label: 'Gratis Ongkir',
                              onTap: () {
                                productProvider.filterByCategory('');
                              },
                            ),
                            _buildQuickActionItem(
                              icon: Icons.discount_rounded,
                              iconColor: const Color(0xFFDC2626),
                              bgColor: const Color(0xFFFEE2E2),
                              label: 'Voucher Hemat',
                              onTap: () {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  const SnackBar(
                                    content: Text('Voucher diskon otomatis diterapkan pada halaman Checkout! 🎟️'),
                                    behavior: SnackBarBehavior.floating,
                                  ),
                                );
                              },
                            ),
                          ],
                        ),
                      ),
                    ),

                    const SizedBox(height: 16),

                    // ══════════════════════════════════════════════════
                    // 4. CATEGORIES SECTION
                    // ══════════════════════════════════════════════════
                    if (productProvider.categories.isNotEmpty) ...[
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            const Row(
                              children: [
                                Icon(Icons.grid_view_rounded, size: 16, color: AppTheme.primary),
                                SizedBox(width: 6),
                                Text(
                                  'Kategori Pilihan',
                                  style: TextStyle(
                                    fontSize: 14,
                                    fontWeight: FontWeight.w800,
                                    color: AppTheme.textPrimary,
                                  ),
                                ),
                              ],
                            ),
                            if (productProvider.selectedCategory.isNotEmpty)
                              GestureDetector(
                                onTap: () => productProvider.filterByCategory(''),
                                child: Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                                  decoration: BoxDecoration(
                                    color: AppTheme.primaryLight,
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                  child: const Row(
                                    children: [
                                      Text(
                                        'Reset Filter',
                                        style: TextStyle(fontSize: 11, fontWeight: FontWeight.w700, color: AppTheme.primaryDark),
                                      ),
                                      SizedBox(width: 3),
                                      Icon(Icons.close, size: 12, color: AppTheme.primaryDark),
                                    ],
                                  ),
                                ),
                              ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 10),
                      SizedBox(
                        height: 86,
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

                    const SizedBox(height: 12),

                    // ══════════════════════════════════════════════════
                    // 5. FLASH SALE SECTION
                    // ══════════════════════════════════════════════════
                    if (productProvider.flashSaleItems.isNotEmpty)
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                        child: FlashSaleSection(
                          items: productProvider.flashSaleItems,
                          remainingSeconds: productProvider.flashSaleRemainingSeconds,
                        ),
                      ),

                    const SizedBox(height: 14),

                    // ══════════════════════════════════════════════════
                    // 6. RECOMMENDED PRODUCTS FEED
                    // ══════════════════════════════════════════════════
                    Padding(
                      padding: const EdgeInsets.fromLTRB(16, 8, 16, 10),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Row(
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
                          Text(
                            '${productProvider.products.length} Produk',
                            style: const TextStyle(fontSize: 11, color: AppTheme.textMuted, fontWeight: FontWeight.w600),
                          ),
                        ],
                      ),
                    ),

                    if (productProvider.products.isEmpty)
                      Padding(
                        padding: const EdgeInsets.all(40),
                        child: Center(
                          child: Column(
                            children: [
                              Icon(Icons.inventory_2_outlined, size: 48, color: Colors.grey.shade400),
                              const SizedBox(height: 12),
                              const Text(
                                'Tidak ada produk yang cocok dengan pencarian.',
                                style: TextStyle(color: AppTheme.textMuted, fontSize: 12),
                              ),
                            ],
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
                  ],
                ),
              ),
      ),
    );
  }

  Widget _buildQuickActionItem({
    required IconData icon,
    required Color iconColor,
    required Color bgColor,
    required String label,
    required VoidCallback onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(12),
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 4),
        child: Column(
          children: [
            Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                color: bgColor,
                borderRadius: BorderRadius.circular(14),
              ),
              child: Center(
                child: Icon(icon, color: iconColor, size: 22),
              ),
            ),
            const SizedBox(height: 6),
            Text(
              label,
              style: const TextStyle(
                fontSize: 10,
                fontWeight: FontWeight.w700,
                color: AppTheme.textSecondary,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
