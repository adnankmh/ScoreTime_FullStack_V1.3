<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DesignVersion extends Model { protected $fillable=['design_profile_id','version','snapshot','note','created_by']; protected $casts=['snapshot'=>'array']; }
