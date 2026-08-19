<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class AdminSeeder extends Seeder{public function run():void{$password=trim((string)env('ADMIN_PASSWORD',''));if($password===''){if(app()->environment('testing')){$password=Str::password(32);}else{$this->command?->warn('ADMIN_PASSWORD is empty; administrator creation was skipped safely.');return;}}User::updateOrCreate(['username'=>env('ADMIN_PRIMARY_USERNAME','Adnan')],['name'=>'Adnan','email'=>env('ADMIN_EMAIL','adnan@local.test'),'password'=>$password,'is_admin'=>true,'is_active'=>true,'locale'=>'en','theme'=>'stadium','font_scale'=>1.0]);}}
