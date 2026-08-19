<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DesignExperiment extends Model { protected $fillable=['key','name','surface','traffic_percent','variant_a','variant_b','enabled','starts_at','ends_at','created_by']; protected $casts=['variant_a'=>'array','variant_b'=>'array','enabled'=>'boolean','starts_at'=>'datetime','ends_at'=>'datetime']; }
