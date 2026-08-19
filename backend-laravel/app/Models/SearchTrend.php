<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class SearchTrend extends Model{protected $fillable=['query','score','last_searched_at'];protected $casts=['last_searched_at'=>'datetime'];}
