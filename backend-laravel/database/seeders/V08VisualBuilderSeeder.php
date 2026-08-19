<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{DesignProfile,NavigationItem,PageLayout};
class V08VisualBuilderSeeder extends Seeder {
 public function run():void {
  $d=DesignProfile::updateOrCreate(['name'=>'Global Elite'],['scope'=>'global','is_active'=>true,'tokens'=>['accent'=>'#55e6a5','accent2'=>'#48a7ff','background'=>'#061019','surface'=>'#0d1b28','surface2'=>'#122434','text'=>'#eef7f3','muted'=>'#8fa8b8','radius'=>20,'cardStyle'=>'soft','headerStyle'=>'glass','density'=>'comfortable','fontFamily'=>'system'],'branding'=>['productName'=>'ScoreTime','shortName'=>'FG','tagline'=>'Live football. Your way.','logoText'=>'11','showPoweredBy'=>false],'features'=>['breakingNews'=>true,'liveTicker'=>true,'predictions'=>true,'fanRoom'=>true,'transfers'=>true,'premium'=>true,'ads'=>true,'social'=>true]]);
  foreach([
   ['web','home','*',[['type'=>'hero','enabled'=>true,'title'=>'Live Football. Reimagined.'],['type'=>'live_matches','enabled'=>true,'limit'=>6],['type'=>'breaking_news','enabled'=>true,'limit'=>5],['type'=>'competitions','enabled'=>true,'limit'=>10],['type'=>'latest_news','enabled'=>true,'limit'=>9]]],
   ['app','home','*',[['type'=>'live_header','enabled'=>true],['type'=>'favorite_matches','enabled'=>true,'limit'=>5],['type'=>'live_matches','enabled'=>true,'limit'=>8],['type'=>'breaking_news','enabled'=>true,'limit'=>5],['type'=>'latest_news','enabled'=>true,'limit'=>10]]]
  ] as [$surface,$key,$locale,$blocks]) PageLayout::updateOrCreate(compact('surface')+['page_key'=>$key,'locale'=>$locale],['blocks'=>$blocks,'is_published'=>true,'revision'=>1]);
  $items=[
   ['web','primary','home',['ar'=>'الرئيسية','en'=>'Home','tr'=>'Ana Sayfa','fr'=>'Accueil','es'=>'Inicio'],'home','/'],
   ['web','primary','matches',['ar'=>'المباريات','en'=>'Matches','tr'=>'Maçlar','fr'=>'Matchs','es'=>'Partidos'],'sports_soccer','/matches'],
   ['web','primary','news',['ar'=>'الأخبار','en'=>'News','tr'=>'Haberler','fr'=>'Actualités','es'=>'Noticias'],'newspaper','/news'],
   ['web','primary','transfers',['ar'=>'الانتقالات','en'=>'Transfers','tr'=>'Transferler','fr'=>'Transferts','es'=>'Fichajes'],'swap_horiz','/transfers'],
   ['app','bottom','home',['ar'=>'الرئيسية','en'=>'Home','tr'=>'Ana Sayfa','fr'=>'Accueil','es'=>'Inicio'],'home','home'],
   ['app','bottom','matches',['ar'=>'المباريات','en'=>'Matches','tr'=>'Maçlar','fr'=>'Matchs','es'=>'Partidos'],'sports_soccer','matches'],
   ['app','bottom','explore',['ar'=>'استكشف','en'=>'Explore','tr'=>'Keşfet','fr'=>'Explorer','es'=>'Explorar'],'search','explore'],
   ['app','bottom','news',['ar'=>'الأخبار','en'=>'News','tr'=>'Haberler','fr'=>'Actualités','es'=>'Noticias'],'newspaper','news'],
   ['app','bottom','more',['ar'=>'المزيد','en'=>'More','tr'=>'Daha','fr'=>'Plus','es'=>'Más'],'tune','more'],
  ];
  foreach($items as $i=>$x) NavigationItem::updateOrCreate(['surface'=>$x[0],'location'=>$x[1],'key'=>$x[2]],['label'=>$x[3],'icon'=>$x[4],'target'=>$x[5],'sort_order'=>$i,'enabled'=>true]);
 }
}
