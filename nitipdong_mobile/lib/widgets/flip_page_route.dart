import 'dart:math' as math;
import 'package:flutter/material.dart';

/// A 3D Y-Axis Card Flip Page Route Transition for Auth Screens.
class Flip3DPageRoute<T> extends PageRouteBuilder<T> {
  final Widget widget;
  final bool isReverse;

  Flip3DPageRoute({
    required this.widget,
    this.isReverse = false,
  }) : super(
          pageBuilder: (context, animation, secondaryAnimation) => widget,
          transitionDuration: const Duration(milliseconds: 650),
          reverseTransitionDuration: const Duration(milliseconds: 650),
          transitionsBuilder: (context, animation, secondaryAnimation, child) {
            final curved = CurvedAnimation(
              parent: animation,
              curve: Curves.easeInOutCubic,
            );

            return AnimatedBuilder(
              animation: curved,
              builder: (context, childWidget) {
                final double progress = curved.value;
                // Rotate from +/- PI/2 (or PI) to 0
                final double angle = (isReverse ? -1.0 : 1.0) * (1.0 - progress) * (math.pi);
                // Apply slight depth scaling at mid-point
                final double scale = 1.0 - (math.sin(progress * math.pi) * 0.08);

                final matrix = Matrix4.identity()
                  ..setEntry(3, 2, 0.0012) // 3D Perspective
                  ..scale(scale, scale, 1.0)
                  ..rotateY(angle);

                return Transform(
                  transform: matrix,
                  alignment: Alignment.center,
                  child: progress < 0.5
                      ? Container(color: const Color(0xFF0B1528))
                      : childWidget,
                );
              },
              child: child,
            );
          },
        );
}
