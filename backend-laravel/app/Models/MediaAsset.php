<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MediaAsset extends Model { protected $fillable=['uploaded_by','disk','path','mime_type','size','alt_text','credit','license','meta']; protected $casts=['meta'=>'array']; public function uploader(){return $this->belongsTo(User::class,'uploaded_by');} }
