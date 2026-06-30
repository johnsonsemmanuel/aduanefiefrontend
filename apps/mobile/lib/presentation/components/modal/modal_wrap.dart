import 'package:flutter/material.dart';
import 'package:aduanefie/presentation/theme/color_set.dart';
import 'package:aduanefie/presentation/theme/theme_wrapper.dart';

class ModalWrap extends StatelessWidget {
  final Widget Function(CustomColorSet colors) child;

  const ModalWrap({super.key, required this.child});

  @override
  Widget build(BuildContext context) {
    return ThemeWrapper(
      builder: (colors, controller) {
        return child(colors);
      },
    );
  }
}
