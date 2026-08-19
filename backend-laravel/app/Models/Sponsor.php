<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Sponsor extends Model { protected $fillable=['name','logo_url','website_url','is_active','starts_at','ends_at']; protected $casts=['is_active'=>'boolean','starts_at'=>'datetime','ends_at'=>'datetime']; }
