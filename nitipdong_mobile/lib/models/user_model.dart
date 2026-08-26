class UserModel {
  final int id;
  final String name;
  final String email;
  final String phone;
  final String role;
  final String? avatarUrl;
  final bool biometricEnabled;
  final String biometricType; // 'fingerprint', 'face', 'any'
  final int cartCount;
  final int wishlistCount;
  final int ordersCount;

  UserModel({
    required this.id,
    required this.name,
    required this.email,
    required this.phone,
    required this.role,
    this.avatarUrl,
    this.biometricEnabled = false,
    this.biometricType = 'fingerprint',
    this.cartCount = 0,
    this.wishlistCount = 0,
    this.ordersCount = 0,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      phone: json['phone'] ?? '',
      role: json['role'] ?? 'customer',
      avatarUrl: json['avatar_url'],
      biometricEnabled: json['biometric_enabled'] == true || json['biometric_enabled'] == 1,
      biometricType: json['biometric_type']?.toString() ?? 'fingerprint',
      cartCount: json['cart_count'] ?? 0,
      wishlistCount: json['wishlist_count'] ?? 0,
      ordersCount: json['orders_count'] ?? 0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'phone': phone,
      'role': role,
      'avatar_url': avatarUrl,
      'biometric_enabled': biometricEnabled,
      'biometric_type': biometricType,
    };
  }
}
