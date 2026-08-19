import 'package:flutter/material.dart';
class ScoreTimeV12Hub extends StatelessWidget {
  const ScoreTimeV12Hub({super.key});
  @override Widget build(BuildContext context) {
    final items = const [
      ('TV Guide', Icons.live_tv_rounded),
      ('Smart Alerts', Icons.notifications_active_rounded),
      ('Match Calendar', Icons.calendar_month_rounded),
      ('Player Radar', Icons.radar_rounded),
      ('Team of the Week', Icons.workspace_premium_rounded),
      ('World Football', Icons.public_rounded),
    ];
    return Scaffold(appBar: AppBar(title: const Text('ScoreTime')),
      body: ListView.separated(padding: const EdgeInsets.all(16), itemCount: items.length,
        separatorBuilder: (_,__)=>const SizedBox(height:10),
        itemBuilder:(context,i)=>Card(child:ListTile(leading:Icon(items[i].$2),title:Text(items[i].$1),trailing:const Icon(Icons.chevron_right_rounded)))));
  }
}
