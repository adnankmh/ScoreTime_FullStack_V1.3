<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
class AdminSeeder extends Seeder{public function run():void{User::updateOrCreate(['username'=>'Adnan'],['name'=>'Adnan','email'=>env('ADMIN_EMAIL','adnan@local.test'),'password'=>env('ADMIN_PASSWORD','Adnan123'),'is_admin'=>true,'is_active'=>true,'locale'=>'ar','theme'=>'stadium','font_scale'=>1.0]);}}
