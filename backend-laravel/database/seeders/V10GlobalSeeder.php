<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{AppSetting,NavigationItem,MenuNode};
class V10GlobalSeeder extends Seeder {public function run():void{
 NavigationItem::updateOrCreate(['surface'=>'app','location'=>'bottom','key'=>'world'],['label'=>['ar'=>'العالم','en'=>'World','fr'=>'Monde','es'=>'Mundo','tr'=>'Dünya'],'icon'=>'public','target'=>'world','sort_order'=>2,'enabled'=>true,'visibility'=>[]]);
 MenuNode::updateOrCreate(['surface'=>'web','key'=>'world'],['location'=>'primary','label'=>['ar'=>'كرة القدم العالمية','en'=>'World Football','fr'=>'Football mondial','es'=>'Fútbol mundial','tr'=>'Dünya futbolu'],'icon'=>'public','target'=>'world','sort_order'=>3,'enabled'=>true,'visibility'=>[]]);
 foreach(['catalog_version'=>'1.0','global_data_mode'=>'provider','admin_mfa_recommended'=>'true','security_headers'=>'enabled'] as $key=>$value)AppSetting::updateOrCreate(['key'=>$key],['value'=>$value,'is_public'=>$key!=='admin_mfa_recommended']);
}}
