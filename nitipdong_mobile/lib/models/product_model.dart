class ProductModel {
  final int id;
  final String? uuid;
  final String name;
  final String slug;
  final double price;
  final double finalPrice;
  final bool hasDiscount;
  final int discountPercentage;
  final double rating;
  final double storeRating;
  final int soldCount;
  final String formattedSold;
  final int stock;
  final String imageUrl;
  final List<String> images;
  final String categoryName;
  final String storeName;
  final int storeId;
  final String? storeUuid;
  final String? storePhone;
  final String city;
  final String description;
  final List<DiscussionModel> discussions;
  final List<ReviewModel> reviews;

  ProductModel({
    required this.id,
    this.uuid,
    required this.name,
    required this.slug,
    required this.price,
    required this.finalPrice,
    this.hasDiscount = false,
    this.discountPercentage = 0,
    this.rating = 5.0,
    this.storeRating = 3.5,
    this.soldCount = 0,
    this.formattedSold = '0',
    this.stock = 10,
    required this.imageUrl,
    this.images = const [],
    this.categoryName = 'Produk',
    this.storeName = 'NitipDong',
    this.storeId = 1,
    this.storeUuid,
    this.storePhone,
    this.city = 'Jakarta',
    this.description = '',
    this.discussions = const [],
    this.reviews = const [],
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

    var revList = <ReviewModel>[];
    if (json['reviews'] != null && json['reviews'] is List) {
      revList = (json['reviews'] as List).map((r) => ReviewModel.fromJson(r)).toList();
    }

    final sCount = (json['sold_count'] as num?)?.toInt() ?? 0;
    String fSold = '';
    if (json['formatted_sold_count'] != null && json['formatted_sold_count'].toString().isNotEmpty) {
      fSold = json['formatted_sold_count'].toString();
    } else if (json['formatted_sold'] != null && json['formatted_sold'].toString().isNotEmpty) {
      fSold = json['formatted_sold'].toString();
    } else {
      if (sCount >= 1000) {
        fSold = '${(sCount / 1000).toStringAsFixed(1)} rb+';
      } else {
        fSold = sCount.toString();
      }
    }

    return ProductModel(
      id: json['id'] is int ? json['id'] : (int.tryParse(json['id']?.toString() ?? '0') ?? 0),
      uuid: json['uuid']?.toString(),
      name: json['name'] ?? '',
      slug: json['slug'] ?? '',
      price: (json['price'] as num?)?.toDouble() ?? (json['original_price'] as num?)?.toDouble() ?? 0.0,
      finalPrice: (json['final_price'] as num?)?.toDouble() ?? (json['flash_sale_price'] as num?)?.toDouble() ?? (json['price'] as num?)?.toDouble() ?? 0.0,
      hasDiscount: json['has_discount'] ?? (json['discount_percentage'] != null && (json['discount_percentage'] as num) > 0),
      discountPercentage: json['discount_percentage'] ?? 0,
      rating: (json['rating'] as num?)?.toDouble() ?? 5.0,
      storeRating: (json['store_rating'] as num?)?.toDouble() ?? (json['store']?['rating'] as num?)?.toDouble() ?? 3.5,
      soldCount: sCount,
      formattedSold: fSold,
      stock: json['stock'] ?? 0,
      imageUrl: json['image_url'] ?? '',
      images: imgList,
      categoryName: json['category_name'] ?? (json['category']?['name'] ?? 'Produk'),
      storeName: json['store_name'] ?? (json['store']?['name'] ?? 'NitipDong'),
      storeId: json['store_id'] ?? (json['store']?['id'] ?? 1),
      storeUuid: json['store_uuid']?.toString() ?? json['store']?['uuid']?.toString(),
      storePhone: json['store_phone']?.toString() ?? json['store']?['phone']?.toString(),
      city: json['city'] ?? (json['store']?['city'] ?? 'Jakarta'),
      description: json['description'] ?? '',
      discussions: discList,
      reviews: revList,
    );
  }
}

class ReviewModel {
  final int id;
  final String userName;
  final String? userAvatar;
  final int rating;
  final String comment;
  final String createdAt;
  final String? sellerReply;

  ReviewModel({
    required this.id,
    required this.userName,
    this.userAvatar,
    required this.rating,
    required this.comment,
    required this.createdAt,
    this.sellerReply,
  });

  factory ReviewModel.fromJson(Map<String, dynamic> json) {
    return ReviewModel(
      id: json['id'] is int ? json['id'] : (int.tryParse(json['id']?.toString() ?? '0') ?? 0),
      userName: json['user_name'] ?? 'Pembeli',
      userAvatar: json['user_avatar'],
      rating: json['rating'] is int ? json['rating'] : (int.tryParse(json['rating']?.toString() ?? '5') ?? 5),
      comment: json['comment'] ?? 'Produk sangat bagus sesuai deskripsi dan pengiriman cepat!',
      createdAt: json['created_at'] ?? 'Baru saja',
      sellerReply: json['seller_reply'],
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
