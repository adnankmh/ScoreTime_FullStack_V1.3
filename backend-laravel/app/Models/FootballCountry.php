<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FootballCountry extends Model {protected $fillable=['name','code','flag_url','is_active','meta'];protected $casts=['is_active'=>'boolean','meta'=>'array'];public function competitions(){return $this->hasMany(Competition::class);}}
