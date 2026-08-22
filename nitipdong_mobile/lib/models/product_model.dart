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
  final int storeId;
  final String city;
  final String description;
  final List<DiscussionModel> discussions;

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
    this.storeId = 1,
    this.city = 'Jakarta',
    this.description = '',
    this.discussions = const [],
  });

  factory ProductModel.fromJson(Map<String, dynamic> json) {
    List<String> imgList = [];
    if (json['images'] != null && json['images'] is List) {
      imgList = List<String>.from(json['images']);
    }

    var discList = <DiscussionModel>[];
    if (json['discussions'] != null && json['discussions'] is List) {
      discList = (json['discussions'] as List).map((d) => DiscussionModel.fromJson(d)).toList();
    }

    return ProductModel(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      slug: json['slug'] ?? '',
      price: (json['price'] as num?)?.toDouble() ?? (json['original_price'] as num?)?.toDouble() ?? 0.0,
      finalPrice: (json['final_price'] as num?)?.toDouble() ?? (json['flash_sale_price'] as num?)?.toDouble() ?? (json['price'] as num?)?.toDouble() ?? 0.0,
      hasDiscount: json['has_discount'] ?? (json['discount_percentage'] != null && (json['discount_percentage'] as num) > 0),
      discountPercentage: json['discount_percentage'] ?? 0,
      rating: (json['rating'] as num?)?.toDouble() ?? 5.0,
      soldCount: json['sold_count'] ?? 0,
      formattedSold: json['formatted_sold'] ?? (json['sold_count']?.toString() ?? '0'),
      stock: json['stock'] ?? 0,
      imageUrl: json['image_url'] ?? '',
      images: imgList,
      categoryName: json['category_name'] ?? (json['category']?['name'] ?? 'Produk'),
      storeName: json['store_name'] ?? (json['store']?['name'] ?? 'NitipDong'),
      storeId: json['store_id'] ?? (json['store']?['id'] ?? 1),
      city: json['city'] ?? (json['store']?['city'] ?? 'Jakarta'),
      description: json['description'] ?? '',
      discussions: discList,
    );
  }
}

class DiscussionModel {
  final int id;
  final String body;
  final String userName;
  final bool isSeller;
  final String createdAt;
  final List<DiscussionReplyModel> replies;

  DiscussionModel({
    required this.id,
    required this.body,
    required this.userName,
    required this.isSeller,
    required this.createdAt,
    required this.replies,
  });

  factory DiscussionModel.fromJson(Map<String, dynamic> json) {
    var repliesList = <DiscussionReplyModel>[];
    if (json['replies'] != null && json['replies'] is List) {
      repliesList = (json['replies'] as List).map((r) => DiscussionReplyModel.fromJson(r)).toList();
    }

    return DiscussionModel(
      id: json['id'] ?? 0,
      body: json['body'] ?? '',
      userName: json['user_name'] ?? '',
      isSeller: json['is_seller'] ?? false,
      createdAt: json['created_at'] ?? '',
      replies: repliesList,
    );
  }
}

class DiscussionReplyModel {
  final int id;
  final String body;
  final String userName;
  final bool isSeller;
  final String createdAt;

  DiscussionReplyModel({
    required this.id,
    required this.body,
    required this.userName,
    required this.isSeller,
    required this.createdAt,
  });

  factory DiscussionReplyModel.fromJson(Map<String, dynamic> json) {
    return DiscussionReplyModel(
      id: json['id'] ?? 0,
      body: json['body'] ?? '',
      userName: json['user_name'] ?? '',
      isSeller: json['is_seller'] ?? false,
      createdAt: json['created_at'] ?? '',
    );
  }
}
