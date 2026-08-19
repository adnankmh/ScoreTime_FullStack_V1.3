<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{CustomPage,MenuNode};
class V09NoCodeSeeder extends Seeder { public function run(): void {
  CustomPage::firstOrCreate(['slug'=>'world-football-center'],['title'=>['ar'=>'مركز كرة القدم العالمي','en'=>'World Football Center'],'surface'=>'both','blocks'=>[['type'=>'hero','title'=>'Hero','enabled'=>true,'config'=>['headline'=>'The world game, in one place.','subheadline'=>'Live matches, stories, tables and transfer intelligence.','image'=>'','cta'=>'Open Match Center']],['type'=>'live_matches','title'=>'Live Matches','enabled'=>true,'config'=>['limit'=>6]],['type'=>'latest_news','title'=>'Latest News','enabled'=>true,'config'=>['limit'=>8]],['type'=>'transfers','title'=>'Transfers','enabled'=>true,'config'=>['limit'=>6]]],'seo'=>['title'=>'World Football Center','description'=>'Live football, news, competitions and transfer intelligence.'],'is_published'=>true,'published_at'=>now()]);
  $items=[['app','bottom','home',['ar'=>'الرئيسية','en'=>'Home'],'home','home',10],['app','bottom','matches',['ar'=>'المباريات','en'=>'Matches'],'sports_soccer','matches',20],['app','bottom','explore',['ar'=>'استكشف','en'=>'Explore'],'search','explore',30],['app','bottom','news',['ar'=>'الأخبار','en'=>'News'],'newspaper','news',40],['app','bottom','more',['ar'=>'المزيد','en'=>'More'],'tune','more',50]];
  foreach($items as $x) MenuNode::firstOrCreate(['surface'=>$x[0],'location'=>$x[1],'key'=>$x[2]],['label'=>$x[3],'icon'=>$x[4],'target'=>$x[5],'sort_order'=>$x[6],'enabled'=>true]);
 }}
