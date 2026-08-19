<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class NavigationItem extends Model { protected $fillable=['surface','location','key','label','icon','target','sort_order','enabled','visibility']; protected $casts=['label'=>'array','enabled'=>'boolean','visibility'=>'array']; }
