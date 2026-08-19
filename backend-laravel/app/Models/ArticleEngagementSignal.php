<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class ArticleEngagementSignal extends Model { protected $fillable=['user_id','article_id','event','weight']; protected $casts=['weight'=>'float']; public function user(){return $this->belongsTo(User::class);} public function article(){return $this->belongsTo(Article::class);} }
