<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class NotificationPreference extends Model { protected $fillable=['user_id','entity_type','entity_id','kickoff','goal','lineup','red_card','halftime','fulltime','transfer']; protected function casts():array{return ['kickoff'=>'bool','goal'=>'bool','lineup'=>'bool','red_card'=>'bool','halftime'=>'bool','fulltime'=>'bool','transfer'=>'bool'];} }
