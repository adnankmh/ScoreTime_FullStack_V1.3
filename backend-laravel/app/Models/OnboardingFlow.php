<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OnboardingFlow extends Model { protected $table='onboarding_flows'; protected $guarded=[]; protected $casts=['steps'=>'array','published'=>'boolean']; }
