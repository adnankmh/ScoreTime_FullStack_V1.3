<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DesignSchedule extends Model { protected $fillable=['design_profile_id','name','starts_at','ends_at','enabled','overrides','created_by']; protected $casts=['starts_at'=>'datetime','ends_at'=>'datetime','enabled'=>'boolean','overrides'=>'array']; }
