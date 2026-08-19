<?php
namespace App\Services;
class TotpService {
 public function generateSecret(int $bytes=20): string { return $this->base32Encode(random_bytes($bytes)); }
 public function verify(string $secret,string $code,int $window=1,int $period=30,int $digits=6): bool { $counter=intdiv(time(),$period); for($i=-$window;$i<=$window;$i++){ if(hash_equals($this->code($secret,$counter+$i,$digits),(string)$code)) return true; } return false; }
 public function code(string $secret,int $counter,int $digits=6): string { $key=$this->base32Decode($secret); $bin=pack('N*',0).pack('N*',$counter); $hash=hash_hmac('sha1',$bin,$key,true); $offset=ord(substr($hash,-1)) & 0x0f; $value=((ord($hash[$offset]) & 0x7f)<<24)|((ord($hash[$offset+1]) & 0xff)<<16)|((ord($hash[$offset+2]) & 0xff)<<8)|(ord($hash[$offset+3]) & 0xff); return str_pad((string)($value%(10**$digits)),$digits,'0',STR_PAD_LEFT); }
 public function otpauthUri(string $issuer,string $account,string $secret): string { $label=rawurlencode($issuer.':'.$account); return 'otpauth://totp/'.$label.'?secret='.$secret.'&issuer='.rawurlencode($issuer).'&algorithm=SHA1&digits=6&period=30'; }
 public function recoveryCodes(int $count=8): array { return array_map(fn()=>strtoupper(bin2hex(random_bytes(5))),range(1,$count)); }
 private function base32Encode(string $data): string { $alphabet='ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';$bits='';foreach(str_split($data) as $c)$bits.=str_pad(decbin(ord($c)),8,'0',STR_PAD_LEFT);$out='';foreach(str_split($bits,5) as $chunk){$out.=$alphabet[bindec(str_pad($chunk,5,'0'))];}return $out; }
 private function base32Decode(string $data): string { $alphabet='ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';$bits='';foreach(str_split(strtoupper(preg_replace('/[^A-Z2-7]/','',$data))) as $c){$p=strpos($alphabet,$c);if($p===false)continue;$bits.=str_pad(decbin($p),5,'0',STR_PAD_LEFT);} $out='';foreach(str_split($bits,8) as $chunk){if(strlen($chunk)===8)$out.=chr(bindec($chunk));}return $out; }
}
