<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CompetitionSeason extends Model {protected $fillable=['competition_id','season','starts_on','ends_on','is_current','coverage','last_synced_at'];protected $casts=['starts_on'=>'date','ends_on'=>'date','is_current'=>'boolean','coverage'=>'array','last_synced_at'=>'datetime'];public function competition(){return $this->belongsTo(Competition::class);}}
