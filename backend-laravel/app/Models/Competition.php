<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Competition extends Model {
    protected $fillable=['provider_id','football_country_id','name_ar','name_en','slug','country','logo_url','season','is_featured','sort_order','type','is_international','coverage','last_synced_at'];
    protected $casts=['is_featured'=>'boolean','is_international'=>'boolean','coverage'=>'array','last_synced_at'=>'datetime'];
    public function matches():HasMany{return $this->hasMany(FootballMatch::class);}
    public function countryRelation(){return $this->belongsTo(FootballCountry::class,'football_country_id');}
    public function seasons(){return $this->hasMany(CompetitionSeason::class);}
    public function teams(){return $this->belongsToMany(Team::class)->withPivot('season')->withTimestamps();}
}
