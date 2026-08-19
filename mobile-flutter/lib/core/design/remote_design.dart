import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../network/api_client.dart';

class RemoteDesign {
  final String productName;
  final String logoText;
  final String tagline;
  final Map<String, dynamic> tokens;
  final Map<String, dynamic> features;
  final List<dynamic> navigation;
  final Map<String, dynamic> layouts;
  final List<dynamic> customPages;
  final List<dynamic> menuTree;
  final Map<String, dynamic> experiments;
  const RemoteDesign({required this.productName, required this.logoText, required this.tagline, required this.tokens, required this.features, required this.navigation, required this.layouts, required this.customPages, required this.menuTree, required this.experiments});

  factory RemoteDesign.fallback() => const RemoteDesign(
        productName: 'ScoreTime',
        logoText: 'ST',
        tagline: 'Every moment counts.',
        tokens: {'accent': '#0B8CFF', 'accent2': '#18D7FF', 'background': '#020716', 'surface': '#08152B'},
        features: {}, navigation: [], layouts: {}, customPages: [], menuTree: [], experiments: {},
      );

  factory RemoteDesign.fromJson(Map<String, dynamic> json) {
    final design = Map<String, dynamic>.from(json['design'] ?? {});
    final branding = Map<String, dynamic>.from(design['branding'] ?? {});
    return RemoteDesign(
      productName: '${branding['productName'] ?? 'ScoreTime'}',
      logoText: '${branding['logoText'] ?? 'ST'}',
      tagline: '${branding['tagline'] ?? 'Every moment counts.'}',
      tokens: Map<String, dynamic>.from(design['tokens'] ?? {}),
      features: Map<String, dynamic>.from(design['features'] ?? {}),
      navigation: List<dynamic>.from(json['navigation'] ?? []),
      layouts: Map<String, dynamic>.from(json['layouts'] ?? {}),
      customPages: List<dynamic>.from(json['customPages'] ?? []),
      menuTree: List<dynamic>.from(json['menuTree'] ?? []),
      experiments: Map<String, dynamic>.from(json['experiments'] ?? {}),
    );
  }

  Color? color(String key) {
    final raw = tokens[key];
    if (raw is! String || !RegExp(r'^#[0-9A-Fa-f]{6}$').hasMatch(raw)) return null;
    return Color(int.parse('FF${raw.substring(1)}', radix: 16));
  }
}

final remoteDesignProvider = FutureProvider<RemoteDesign>((ref) async {
  try {
    final r = await ApiClient().dio.get('/design/bootstrap', queryParameters: {'surface': 'app'});
    return RemoteDesign.fromJson(Map<String, dynamic>.from(r.data['data']));
  } catch (_) {
    return RemoteDesign.fallback();
  }
});
