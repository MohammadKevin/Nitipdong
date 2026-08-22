class ProductModel {
  final int id;
  final String name;
  final String slug;
  final double price;
  final double finalPrice;
  final bool hasDiscount;
  final int discountPercentage;
  final double rating;
  final int soldCount;
  final String formattedSold;
  final int stock;
  final String imageUrl;
  final List<String> images;
  final String categoryName;
  final String storeName;
  final String city;
  final String description;

  ProductModel({
    required this.id,
    required this.name,
    required this.slug,
    required this.price,
    required this.finalPrice,
    this.hasDiscount = false,
    this.discountPercentage = 0,
    this.rating = 5.0,
    this.soldCount = 0,
    this.formattedSold = '0',
    this.stock = 10,
    required this.imageUrl,
    this.images = const [],
    this.categoryName = 'Produk',
    this.storeName = 'NitipDong',
    this.city = 'Jakarta',
    this.description = '',
  });

  factory ProductModel.fromJson(Map<String, dynamic> json) {
    List<String> imgList = [];
    if (json['images'] != null && json['images'] is List) {
      imgList = List<String>.from(json['images']);
    }

    return ProductModel(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      slug: json['slug'] ?? '',
      price: (json['price'] as num?)?.toDouble() ?? 0.0,
      finalPrice: (json['final_price'] as num?)?.toDouble() ?? (json['price'] as num?)?.toDouble() ?? 0.0,
      hasDiscount: json['has_discount'] ?? false,
      discountPercentage: json['discount_percentage'] ?? 0,
      rating: (json['rating'] as num?)?.toDouble() ?? 5.0,
      soldCount: json['sold_count'] ?? 0,
      formattedSold: json['formatted_sold'] ?? (json['sold_count']?.toString() ?? '0'),
      stock: json['stock'] ?? 0,
      imageUrl: json['image_url'] ?? '',
      images: imgList,
      categoryName: json['category_name'] ?? (json['category']?['name'] ?? 'Produk'),
      storeName: json['store_name'] ?? (json['store']?['name'] ?? 'NitipDong'),
      city: json['city'] ?? (json['store']?['city'] ?? 'Jakarta'),
      description: json['description'] ?? '',
    );
  }
}
