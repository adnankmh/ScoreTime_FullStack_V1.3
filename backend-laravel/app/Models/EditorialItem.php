<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class EditorialItem extends Model { protected $guarded=[]; protected $casts=['source_published_at'=>'datetime','reviewed_at'=>'datetime']; public function source(){return $this->belongsTo(NewsSource::class,'news_source_id');} }
