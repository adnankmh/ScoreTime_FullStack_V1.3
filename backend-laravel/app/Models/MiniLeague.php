<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MiniLeague extends Model { protected $fillable=['owner_id','name','join_code','is_public','season']; protected $casts=['is_public'=>'boolean']; public function owner(){return $this->belongsTo(User::class,'owner_id');} public function members(){return $this->belongsToMany(User::class,'mini_league_members')->withPivot('points')->withTimestamps()->orderByPivot('points','desc');} }
