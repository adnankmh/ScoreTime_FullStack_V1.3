<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PlayerSeasonStat extends Model { protected $fillable=['player_id','competition_id','season','appearances','starts','minutes','goals','assists','yellow_cards','red_cards','rating','xg','xa','extra']; protected $casts=['extra'=>'array','rating'=>'decimal:2','xg'=>'decimal:2','xa'=>'decimal:2']; public function player(){return $this->belongsTo(Player::class);} public function competition(){return $this->belongsTo(Competition::class);} }
