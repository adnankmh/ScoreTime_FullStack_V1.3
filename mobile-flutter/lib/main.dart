import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'core/i18n/app_strings.dart';
import 'core/design/remote_design.dart';
import 'core/theme/theme_controller.dart';
import 'features/home/presentation/home_screen.dart';
import 'features/matches/presentation/matches_screen.dart';
import 'features/news/presentation/news_screen.dart';
import 'features/explore/presentation/explore_screen.dart';
import 'features/settings/presentation/settings_screen.dart';
import 'features/custom_pages/presentation/dynamic_page_screen.dart';
import 'features/world/presentation/global_football_screen.dart';

void main()=>runApp(const ProviderScope(child:FootballGlobalApp()));
class FootballGlobalApp extends ConsumerWidget{
 const FootballGlobalApp({super.key});
 @override Widget build(BuildContext context,WidgetRef ref){final s=ref.watch(themeControllerProvider);final rd=ref.watch(remoteDesignProvider).asData?.value??RemoteDesign.fallback();return MaterialApp(debugShowCheckedModeBanner:false,title:rd.productName,locale:Locale(s.locale),supportedLocales:AppStrings.supported.map(Locale.new),localizationsDelegates:const[GlobalMaterialLocalizations.delegate,GlobalWidgetsLocalizations.delegate,GlobalCupertinoLocalizations.delegate],theme:AppThemeFactory.build(s.theme,s.fontScale,rd.tokens),home:const AppShell());}
}
class AppShell extends ConsumerStatefulWidget{const AppShell({super.key});@override ConsumerState<AppShell> createState()=>_AppShellState();}
class _AppShellState extends ConsumerState<AppShell>{
 int index=0;
 static const corePages=<String,Widget>{'home':HomeScreen(),'matches':MatchesScreen(),'explore':ExploreScreen(),'news':NewsScreen(),'more':SettingsScreen(),'world':GlobalFootballScreen()};
 IconData iconFor(String? name)=>switch(name){'home'=>Icons.home_outlined,'sports_soccer'=>Icons.sports_soccer_outlined,'newspaper'=>Icons.newspaper_outlined,'search'=>Icons.search,'tune'=>Icons.tune,'star'=>Icons.star_border,'calendar'=>Icons.calendar_month_outlined,'public'=>Icons.public,'language'=>Icons.language,_=>Icons.circle_outlined};
 Widget pageFor(String target){if(corePages.containsKey(target))return corePages[target]!;if(target.startsWith('page:'))return DynamicPageScreen(slug:target.substring(5));return const ExploreScreen();}
 @override Widget build(BuildContext context){final rd=ref.watch(remoteDesignProvider).asData?.value??RemoteDesign.fallback();final remote=rd.navigation.where((e)=>e is Map&&e['location']=='bottom'&&(corePages.containsKey('${e['target']}')||'${e['target']}'.startsWith('page:'))).map((e)=>Map<String,dynamic>.from(e)).take(5).toList();final nav=remote.isNotEmpty?remote:<Map<String,dynamic>>[{'target':'home','label':'Home','icon':'home'},{'target':'matches','label':'Matches','icon':'sports_soccer'},{'target':'explore','label':'Explore','icon':'search'},{'target':'news','label':'News','icon':'newspaper'},{'target':'more','label':'More','icon':'tune'}];if(index>=nav.length)index=0;return Scaffold(body:IndexedStack(index:index,children:nav.map((n)=>pageFor('${n['target']}')).toList()),bottomNavigationBar:NavigationBar(selectedIndex:index,onDestinationSelected:(v)=>setState(()=>index=v),destinations:nav.map((n)=>NavigationDestination(icon:Icon(iconFor('${n['icon']}')),label:'${n['label']}')).toList()));}
}
