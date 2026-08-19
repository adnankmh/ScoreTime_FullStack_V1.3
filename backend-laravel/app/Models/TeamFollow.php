<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; class TeamFollow extends Model {protected $fillable=['user_id','team_id','news','matches','transfers']; protected function casts():array{return ['news'=>'boolean','matches'=>'boolean','transfers'=>'boolean'];} public function team(){return $this->belongsTo(Team::class);}}
