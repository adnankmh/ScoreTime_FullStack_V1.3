<?php
namespace App\Services;
use App\Models\PushDevice; use GuzzleHttp\Client; use Illuminate\Support\Facades\Cache;
class FcmPushService {
 public function configured(): bool { return is_file((string)config('services.firebase.credentials')); }
 public function sendToUser(int $userId,string $title,string $body,array $data=[]): array {
  if(!$this->configured())return ['sent'=>0,'skipped'=>true,'reason'=>'firebase_credentials_missing'];
  $tokens=PushDevice::where('user_id',$userId)->where('enabled',true)->pluck('token'); $sent=0; $errors=[]; foreach($tokens as $token){try{$this->send($token,$title,$body,$data);$sent++;}catch(\Throwable $e){$errors[]=$e->getMessage();}}
  return compact('sent','errors');
 }
 private function send(string $token,string $title,string $body,array $data): void {
  $cred=json_decode(file_get_contents(config('services.firebase.credentials')),true,512,JSON_THROW_ON_ERROR); $access=$this->accessToken($cred); $client=new Client(['timeout'=>8]);
  $client->post('https://fcm.googleapis.com/v1/projects/'.$cred['project_id'].'/messages:send',['headers'=>['Authorization'=>'Bearer '.$access,'Content-Type'=>'application/json'],'json'=>['message'=>['token'=>$token,'notification'=>compact('title','body'),'data'=>collect($data)->map(fn($v)=>(string)$v)->all()]]]);
 }
 private function accessToken(array $cred): string { return Cache::remember('fcm.access_token',3300,function()use($cred){$now=time();$header=$this->b64(json_encode(['alg'=>'RS256','typ'=>'JWT']));$claims=$this->b64(json_encode(['iss'=>$cred['client_email'],'scope'=>'https://www.googleapis.com/auth/firebase.messaging','aud'=>'https://oauth2.googleapis.com/token','iat'=>$now,'exp'=>$now+3600]));$unsigned=$header.'.'.$claims;openssl_sign($unsigned,$sig,$cred['private_key'],OPENSSL_ALGO_SHA256);$jwt=$unsigned.'.'.$this->b64($sig);$c=new Client(['timeout'=>8]);$r=$c->post('https://oauth2.googleapis.com/token',['form_params'=>['grant_type'=>'urn:ietf:params:oauth:grant-type:jwt-bearer','assertion'=>$jwt]]);return json_decode((string)$r->getBody(),true,512,JSON_THROW_ON_ERROR)['access_token'];}); }
 private function b64(string $v): string { return rtrim(strtr(base64_encode($v),'+/','-_'),'='); }
}
