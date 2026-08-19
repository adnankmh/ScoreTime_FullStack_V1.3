<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class UserLevel extends Model{protected $fillable=['user_id','xp','level','title','prediction_streak'];public function user(){return $this->belongsTo(User::class);}}
