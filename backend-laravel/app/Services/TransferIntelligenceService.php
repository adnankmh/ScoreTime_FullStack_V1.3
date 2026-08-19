<?php
namespace App\Services; use App\Models\Transfer;
class TransferIntelligenceService { public function feed(string $status='all'): array { $q=Transfer::with(['player','fromTeam','toTeam'])->latest('transfer_date'); if($status!=='all')$q->where('status',$status); return ['items'=>$q->limit(60)->get(),'filters'=>['all','rumor','negotiating','confirmed','collapsed']]; } }
