<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class EditorialRevision extends Model { protected $table='editorial_revisions'; protected $guarded=[]; protected $casts=['meta'=>'array']; }
