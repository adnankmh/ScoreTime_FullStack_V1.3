<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WhiteLabelProfile extends Model { protected $fillable=['key','name','host','branding','tokens','features','enabled','updated_by']; protected $casts=['branding'=>'array','tokens'=>'array','features'=>'array','enabled'=>'boolean']; }
