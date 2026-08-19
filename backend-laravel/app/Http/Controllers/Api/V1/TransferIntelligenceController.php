<?php
namespace App\Http\Controllers\Api\V1; use App\Http\Controllers\Controller; use App\Services\TransferIntelligenceService; use Illuminate\Http\Request;
class TransferIntelligenceController extends Controller { public function index(Request $r,TransferIntelligenceService $svc){return response()->json(['data'=>$svc->feed((string)$r->query('status','all'))]);} }
