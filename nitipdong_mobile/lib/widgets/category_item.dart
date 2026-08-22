import 'package:flutter/material.dart';
import '../models/category_model.dart';
import '../theme/app_theme.dart';

class CategoryItem extends StatelessWidget {
  final CategoryModel category;
  final bool isSelected;
  final VoidCallback onTap;

  const CategoryItem({
    Key? key,
    required this.category,
    this.isSelected = false,
    required this.onTap,
  }) : super(key: key);

  IconData _getIconData(String iconName) {
    if (iconName.contains('laptop') || iconName.contains('desktop')) return Icons.laptop_mac;
    if (iconName.contains('utensils') || iconName.contains('food')) return Icons.restaurant;
    if (iconName.contains('shirt') || iconName.contains('tshirt')) return Icons.checkroom;
    if (iconName.contains('spa') || iconName.contains('beauty')) return Icons.spa;
    if (iconName.contains('couch') || iconName.contains('home')) return Icons.chair;
    if (iconName.contains('car') || iconName.contains('motorcycle')) return Icons.two_wheeler;
    return Icons.shopping_bag_outlined;
  }

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 68,
        margin: const EdgeInsets.symmetric(horizontal: 4),
        child: Column(
          children: [
            Container(
              width: 50,
              height: 50,
              decoration: BoxDecoration(
                color: isSelected ? AppTheme.primary : AppTheme.primaryLight,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(
                  color: isSelected ? AppTheme.primary : AppTheme.border,
                ),
              ),
              child: Icon(
                _getIconData(category.icon),
                color: isSelected ? Colors.white : AppTheme.primaryDark,
                size: 22,
              ),
            ),
            const SizedBox(height: 6),
            Text(
              category.name,
              textAlign: TextAlign.center,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: TextStyle(
                fontSize: 10,
                fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
                color: isSelected ? AppTheme.primaryDark : AppTheme.textSecondary,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
