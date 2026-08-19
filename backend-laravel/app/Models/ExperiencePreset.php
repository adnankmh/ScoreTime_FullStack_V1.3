<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ExperiencePreset extends Model { protected $guarded=[]; protected $casts=['schema'=>'array','published'=>'boolean']; }
