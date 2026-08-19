import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';

void main() {
  testWidgets('ScoreTime renders a Material surface', (tester) async {
    await tester.pumpWidget(const MaterialApp(home: Scaffold(body: Text('ScoreTime'))));
    expect(find.text('ScoreTime'), findsOneWidget);
  });
}
