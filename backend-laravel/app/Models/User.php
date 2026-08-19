<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable {
 use HasApiTokens,HasFactory,Notifiable;
 protected $fillable=['name','username','email','password','two_factor_secret','two_factor_recovery_codes','two_factor_confirmed_at','avatar_url','timezone','is_admin','locale','theme','font_scale','is_active','plan','premium_until','bio','cover_url','favorite_team_ids','profile_public'];
 protected $hidden=['password','remember_token','two_factor_secret','two_factor_recovery_codes'];
 protected function casts():array{return ['email_verified_at'=>'datetime','password'=>'hashed','is_admin'=>'boolean','is_active'=>'boolean','font_scale'=>'float','two_factor_confirmed_at'=>'datetime','two_factor_recovery_codes'=>'array','premium_until'=>'datetime','profile_public'=>'boolean'];}
 public function level(){return $this->hasOne(UserLevel::class);}
 public function challengesCreated(){return $this->hasMany(UserChallenge::class,'creator_id');}
 public function challengesReceived(){return $this->hasMany(UserChallenge::class,'opponent_id');}

    public function smartAlertRules(){ return $this->hasMany(\App\Models\SmartAlertRule::class); }
}
