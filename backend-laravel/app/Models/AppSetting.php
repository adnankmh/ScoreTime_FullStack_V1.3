<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AppSetting extends Model { protected $fillable=['key','value','is_public']; protected $casts=['value'=>'array','is_public'=>'boolean']; }
