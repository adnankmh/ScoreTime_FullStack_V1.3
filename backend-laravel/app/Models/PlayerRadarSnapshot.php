<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PlayerRadarSnapshot extends Model { protected $table='player_radar_snapshots'; protected $guarded=[]; protected $casts=['metrics'=>'array']; }
