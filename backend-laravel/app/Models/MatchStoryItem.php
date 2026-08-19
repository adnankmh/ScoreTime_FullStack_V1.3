<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MatchStoryItem extends Model { protected $table='match_story_items'; protected $guarded=[]; protected $casts=['payload'=>'array']; }
