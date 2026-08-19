<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class FriendActivity extends Model { protected $fillable=['user_id','type','subject_type','subject_id','meta']; protected $casts=['meta'=>'array']; public function user(){return $this->belongsTo(User::class);} }
