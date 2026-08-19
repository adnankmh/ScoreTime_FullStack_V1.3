<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DataProviderSyncLog extends Model { protected $fillable=['provider','resource','status','records','duration_ms','message','meta','started_at','finished_at']; protected $casts=['meta'=>'array','started_at'=>'datetime','finished_at'=>'datetime']; }
