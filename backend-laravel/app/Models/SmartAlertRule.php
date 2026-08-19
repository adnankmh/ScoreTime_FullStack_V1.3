<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SmartAlertRule extends Model { protected $guarded=[]; protected $casts=['push'=>'boolean','in_app'=>'boolean','quiet_hours'=>'array']; }
