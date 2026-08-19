<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UserNotification extends Model { protected $table='notifications'; public $incrementing=false; protected $keyType='string'; protected $fillable=['id','user_id','type','title','body','data','read_at']; protected $casts=['data'=>'array','read_at'=>'datetime']; public function user(){return $this->belongsTo(User::class);} }
