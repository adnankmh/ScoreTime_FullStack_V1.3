<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PageLayout extends Model { protected $fillable=['surface','page_key','locale','blocks','is_published','revision','updated_by']; protected $casts=['blocks'=>'array','is_published'=>'boolean']; }
