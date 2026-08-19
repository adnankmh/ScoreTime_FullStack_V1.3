import 'package:flutter/material.dart';
class ScoreTimeV13Hub extends StatelessWidget {
 const ScoreTimeV13Hub({super.key});
 @override Widget build(BuildContext context) {
  const sections=[
   ('Live & Calendar',Icons.calendar_month_rounded),
   ('Competition Brackets',Icons.account_tree_rounded),
   ('Team of the Week',Icons.emoji_events_rounded),
   ('Player Radar',Icons.radar_rounded),
   ('Match Story',Icons.auto_stories_rounded),
   ('TV Guide',Icons.live_tv_rounded),
   ('Smart Alerts',Icons.notifications_active_rounded),
   ('World Football',Icons.public_rounded),
  ];
  return Scaffold(appBar:AppBar(title:const Text('ScoreTime')),
   body:ListView.separated(padding:const EdgeInsets.all(16),itemCount:sections.length,
    separatorBuilder:(_,__)=>const SizedBox(height:10),
    itemBuilder:(context,i)=>Card(child:ListTile(leading:Icon(sections[i].$2),title:Text(sections[i].$1),trailing:const Icon(Icons.chevron_right_rounded)))));
 }
}
