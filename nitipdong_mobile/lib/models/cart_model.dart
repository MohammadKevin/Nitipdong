class CartItemModel {
  final int id;
  final int productId;
  final String name;
  final String imageUrl;
  final double price;
  final double originalPrice;
  final bool hasDiscount;
  int quantity;
  final String? variant;
  final int stock;
  final double subtotal;
  final String storeName;

  CartItemModel({
    required this.id,
    required this.productId,
    required this.name,
    required this.imageUrl,
    required this.price,
    required this.originalPrice,
    this.hasDiscount = false,
    required this.quantity,
    this.variant,
    required this.stock,
    required this.subtotal,
    required this.storeName,
  });

  factory CartItemModel.fromJson(Map<String, dynamic> json) {
    return CartItemModel(
      id: json['id'] ?? 0,
      productId: json['product_id'] ?? 0,
      name: json['name'] ?? '',
      imageUrl: json['image_url'] ?? '',
      price: (json['price'] as num?)?.toDouble() ?? 0.0,
      originalPrice: (json['original_price'] as num?)?.toDouble() ?? 0.0,
      hasDiscount: json['has_discount'] ?? false,
      quantity: json['quantity'] ?? 1,
      variant: json['variant'],
      stock: json['stock'] ?? 10,
      subtotal: (json['subtotal'] as num?)?.toDouble() ?? 0.0,
      storeName: json['store_name'] ?? 'NitipDong',
    );
  }
}
