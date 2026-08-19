<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CustomPage extends Model { protected $fillable=['slug','title','surface','blocks','seo','is_published','published_at','updated_by']; protected $casts=['title'=>'array','blocks'=>'array','seo'=>'array','is_published'=>'boolean','published_at'=>'datetime']; }
