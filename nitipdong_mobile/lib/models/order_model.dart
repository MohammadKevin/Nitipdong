class OrderItemModel {
  final int id;
  final String name;
  final String imageUrl;
  final double price;
  final int quantity;
  final String? variant;
  final double subtotal;
  final String storeName;

  OrderItemModel({
    required this.id,
    required this.name,
    required this.imageUrl,
    required this.price,
    required this.quantity,
    this.variant,
    required this.subtotal,
    required this.storeName,
  });

  factory OrderItemModel.fromJson(Map<String, dynamic> json) {
    return OrderItemModel(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      imageUrl: json['image_url'] ?? '',
      price: (json['price'] as num?)?.toDouble() ?? 0.0,
      quantity: json['quantity'] ?? 1,
      variant: json['variant'],
      subtotal: (json['subtotal'] as num?)?.toDouble() ?? 0.0,
      storeName: json['store_name'] ?? 'NitipDong',
    );
  }
}

class OrderModel {
  final int id;
  final String orderNumber;
  final double totalAmount;
  final String status;
  final String statusLabel;
  final String paymentStatus;
  final String createdAt;
  final int itemsCount;
  final List<OrderItemModel> items;

  OrderModel({
    required this.id,
    required this.orderNumber,
    required this.totalAmount,
    required this.status,
    required this.statusLabel,
    required this.paymentStatus,
    required this.createdAt,
    this.itemsCount = 0,
    this.items = const [],
  });

  factory OrderModel.fromJson(Map<String, dynamic> json) {
    List<OrderItemModel> itemList = [];
    if (json['items'] != null && json['items'] is List) {
      itemList = (json['items'] as List).map((i) => OrderItemModel.fromJson(i)).toList();
    }

    return OrderModel(
      id: json['id'] ?? 0,
      orderNumber: json['order_number'] ?? ('ORD-' + json['id'].toString()),
      totalAmount: (json['total_amount'] as num?)?.toDouble() ?? 0.0,
      status: json['status'] ?? 'pending',
      statusLabel: json['status_label'] ?? 'Menunggu',
      paymentStatus: json['payment_status'] ?? 'pending',
      createdAt: json['created_at'] ?? '',
      itemsCount: json['items_count'] ?? itemList.length,
      items: itemList,
    );
  }
}
