class BannerModel {
  final int id;
  final String title;
  final String subtitle;
  final String badge;
  final String imageUrl;
  final String targetUrl;
  final String buttonText;

  BannerModel({
    required this.id,
    required this.title,
    required this.subtitle,
    required this.badge,
    required this.imageUrl,
    required this.targetUrl,
    required this.buttonText,
  });

  factory BannerModel.fromJson(Map<String, dynamic> json) {
    return BannerModel(
      id: json['id'] ?? 0,
      title: json['title'] ?? '',
      subtitle: json['subtitle'] ?? '',
      badge: json['badge'] ?? '',
      imageUrl: json['image_url'] ?? '',
      targetUrl: json['target_url'] ?? '',
      buttonText: json['button_text'] ?? 'Lihat Promo',
    );
  }
}
