<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; class Achievement extends Model {protected $fillable=['key','name_ar','name_en','description_ar','description_en','icon','points','tier','is_active']; protected function casts():array{return ['is_active'=>'boolean'];}}
