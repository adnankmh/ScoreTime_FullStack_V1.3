<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Transfer extends Model {
 protected $fillable=['player_id','from_team_id','to_team_id','transfer_date','type','fee','currency','status','source_url','is_featured','confidence','source_name','headline','last_verified_at'];
 protected $casts=['transfer_date'=>'date','fee'=>'decimal:2','is_featured'=>'boolean','last_verified_at'=>'datetime'];
 public function player(){return $this->belongsTo(Player::class);} public function fromTeam(){return $this->belongsTo(Team::class,'from_team_id');} public function toTeam(){return $this->belongsTo(Team::class,'to_team_id');}
}
