<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{DesignProfile,NavigationItem,PageLayout};
class V08VisualBuilderSeeder extends Seeder {
 public function run():void {
  $d=DesignProfile::updateOrCreate(['name'=>'Global Elite'],['scope'=>'global','is_active'=>true,'tokens'=>['accent'=>'#0B8CFF','accent2'=>'#18D7FF','background'=>'#020716','surface'=>'#08152B','surface2'=>'#0B1C39','text'=>'#F6F8FF','muted'=>'#91A2BE','radius'=>24,'cardStyle'=>'premium','headerStyle'=>'glass','density'=>'comfortable','fontFamily'=>'system'],'branding'=>['productName'=>'ScoreTime','shortName'=>'ST','tagline'=>'Every moment counts.','logoText'=>'ST','showPoweredBy'=>false],'features'=>['breakingNews'=>true,'liveTicker'=>true,'predictions'=>true,'fanRoom'=>true,'transfers'=>true,'premium'=>true,'ads'=>true,'social'=>true]]);
  foreach([
   ['web','home','*',[['type'=>'hero','enabled'=>true,'title'=>'Football moves fast. ScoreTime moves faster.'],['type'=>'live_matches','enabled'=>true,'limit'=>6],['type'=>'breaking_news','enabled'=>true,'limit'=>5],['type'=>'competitions','enabled'=>true,'limit'=>10],['type'=>'latest_news','enabled'=>true,'limit'=>9]]],
   ['app','home','*',[['type'=>'live_header','enabled'=>true],['type'=>'favorite_matches','enabled'=>true,'limit'=>5],['type'=>'live_matches','enabled'=>true,'limit'=>8],['type'=>'breaking_news','enabled'=>true,'limit'=>5],['type'=>'latest_news','enabled'=>true,'limit'=>10]]]
  ] as [$surface,$key,$locale,$blocks]) PageLayout::updateOrCreate(compact('surface')+['page_key'=>$key,'locale'=>$locale],['blocks'=>$blocks,'is_published'=>true,'revision'=>1]);
  $items=[
   ['web','primary','home',['ar'=>'الرئيسية','en'=>'Home','tr'=>'Ana Sayfa','fr'=>'Accueil','es'=>'Inicio','de'=>'Start'],'home','/'],
   ['web','primary','matches',['ar'=>'المباريات','en'=>'Matches','tr'=>'Maçlar','fr'=>'Matchs','es'=>'Partidos','de'=>'Spiele'],'sports_soccer','/matches'],
   ['web','primary','news',['ar'=>'الأخبار','en'=>'News','tr'=>'Haberler','fr'=>'Actualités','es'=>'Noticias','de'=>'News'],'newspaper','/news'],
   ['web','primary','transfers',['ar'=>'الانتقالات','en'=>'Transfers','tr'=>'Transferler','fr'=>'Transferts','es'=>'Fichajes','de'=>'Transfers'],'swap_horiz','/transfers'],
   ['app','bottom','home',['ar'=>'الرئيسية','en'=>'Home','tr'=>'Ana Sayfa','fr'=>'Accueil','es'=>'Inicio','de'=>'Start'],'home','home'],
   ['app','bottom','matches',['ar'=>'المباريات','en'=>'Matches','tr'=>'Maçlar','fr'=>'Matchs','es'=>'Partidos','de'=>'Spiele'],'sports_soccer','matches'],
   ['app','bottom','explore',['ar'=>'استكشف','en'=>'Explore','tr'=>'Keşfet','fr'=>'Explorer','es'=>'Explorar','de'=>'Entdecken'],'search','explore'],
   ['app','bottom','news',['ar'=>'الأخبار','en'=>'News','tr'=>'Haberler','fr'=>'Actualités','es'=>'Noticias','de'=>'News'],'newspaper','news'],
   ['app','bottom','more',['ar'=>'المزيد','en'=>'More','tr'=>'Daha','fr'=>'Plus','es'=>'Más','de'=>'Mehr'],'tune','more'],
  ];
  foreach($items as $i=>$x) NavigationItem::updateOrCreate(['surface'=>$x[0],'location'=>$x[1],'key'=>$x[2]],['label'=>$x[3],'icon'=>$x[4],'target'=>$x[5],'sort_order'=>$i,'enabled'=>true]);
 }
}
