<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DesignProfile extends Model { protected $fillable=['name','scope','is_active','tokens','branding','features','updated_by']; protected $casts=['is_active'=>'boolean','tokens'=>'array','branding'=>'array','features'=>'array']; public function versions(){return $this->hasMany(DesignVersion::class);} }
