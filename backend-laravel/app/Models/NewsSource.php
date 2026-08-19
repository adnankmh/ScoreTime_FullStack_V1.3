<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class NewsSource extends Model { protected $guarded=[]; protected $casts=['enabled'=>'boolean']; }
