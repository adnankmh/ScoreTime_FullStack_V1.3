<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MenuNode extends Model { protected $fillable=['surface','location','parent_id','key','label','icon','target','sort_order','enabled','visibility']; protected $casts=['label'=>'array','enabled'=>'boolean','visibility'=>'array']; public function children(){return $this->hasMany(self::class,'parent_id')->orderBy('sort_order');} }
