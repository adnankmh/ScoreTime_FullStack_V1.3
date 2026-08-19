import 'package:flutter/material.dart';
import '../../../core/network/football_repository.dart';
import 'match_detail_screen.dart';

class MatchesScreen extends StatefulWidget {
  const MatchesScreen({super.key});
  @override State<MatchesScreen> createState() => _MatchesScreenState();
}

class _MatchesScreenState extends State<MatchesScreen> {
  late Future<List<dynamic>> future;
  @override void initState(){ super.initState(); future=FootballRepository().matches(); }
  Future<void> reload() async { setState(()=>future=FootballRepository().matches()); await future; }
  @override Widget build(BuildContext context)=>Scaffold(
    appBar:AppBar(title:const Text('Match Center'),actions:[IconButton(onPressed:reload,icon:const Icon(Icons.refresh))]),
    body:RefreshIndicator(onRefresh:reload,child:FutureBuilder<List<dynamic>>(
      future:future,builder:(context,s){
        if(s.connectionState==ConnectionState.waiting)return const Center(child:CircularProgressIndicator());
        if(s.hasError)return ListView(children:[const SizedBox(height:180),Center(child:Column(children:[const Icon(Icons.cloud_off,size:48),const SizedBox(height:12),const Text('تعذر تحميل المباريات'),TextButton(onPressed:reload,child:const Text('إعادة المحاولة'))]))]);
        final list=s.data??[]; if(list.isEmpty)return ListView(children:const[SizedBox(height:180),Center(child:Text('لا توجد مباريات في هذا اليوم'))]);
        return ListView.builder(padding:const EdgeInsets.all(14),itemCount:list.length,itemBuilder:(context,i){final m=Map<String,dynamic>.from(list[i]);final h=Map<String,dynamic>.from(m['home_team']??{}),a=Map<String,dynamic>.from(m['away_team']??{});return Card(margin:const EdgeInsets.only(bottom:10),child:InkWell(borderRadius:BorderRadius.circular(18),onTap:()=>Navigator.push(context,MaterialPageRoute(builder:(_)=>MatchDetailScreen(matchId:m['id']))),child:Padding(padding:const EdgeInsets.all(16),child:Column(children:[Row(children:[Expanded(child:Text('${m['competition']?['name_en']??m['competition']?['name_ar']??''}',style:Theme.of(context).textTheme.labelMedium)),_LiveBadge(status:'${m['status']??''}',minute:m['minute'])]),const SizedBox(height:16),Row(children:[Expanded(child:Text('${h['name_en']??h['name_ar']??'Home'}',style:const TextStyle(fontWeight:FontWeight.w800))),Text('${m['home_score']??0}  —  ${m['away_score']??0}',style:Theme.of(context).textTheme.headlineSmall?.copyWith(fontWeight:FontWeight.w900)),Expanded(child:Text('${a['name_en']??a['name_ar']??'Away'}',textAlign:TextAlign.end,style:const TextStyle(fontWeight:FontWeight.w800)))]),const SizedBox(height:10),Row(children:[const Icon(Icons.location_on_outlined,size:16),const SizedBox(width:5),Expanded(child:Text('${m['venue']??'Venue TBD'}',style:Theme.of(context).textTheme.bodySmall)),const Icon(Icons.chevron_right)])]))));});
      }
    ))
  );
}
class _LiveBadge extends StatelessWidget{const _LiveBadge({required this.status,this.minute});final String status;final dynamic minute;@override Widget build(BuildContext c){final live=status.toLowerCase()=='live';return Container(padding:const EdgeInsets.symmetric(horizontal:9,vertical:5),decoration:BoxDecoration(borderRadius:BorderRadius.circular(99),color:live?Theme.of(c).colorScheme.primaryContainer:Theme.of(c).colorScheme.surfaceContainerHighest),child:Text(live?'LIVE ${minute??''}\'':status.toUpperCase(),style:const TextStyle(fontSize:11,fontWeight:FontWeight.w900)));}}
