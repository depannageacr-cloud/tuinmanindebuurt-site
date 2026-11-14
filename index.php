<?php
declare(strict_types=1);
// Deploy this directory on the existing OVH PHP hosting, outside the static Site.
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
$allowed = ['https://tuinmanindebuurt.be', 'https://www.tuinmanindebuurt.be', 'https://tuinmanindebuurt-dvs.werkruimte-v-5038.chatgpt.site'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
function respond(int $status, array $body): void {
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
if (!in_array($origin, $allowed, true)) respond(403, ['ok'=>false, 'error'=>'Gebruik het offerteformulier op tuinmanindebuurt.be.']);
header('Access-Control-Allow-Origin: '.$origin);
header('Vary: Origin');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond(405, ['ok'=>false, 'error'=>'Alleen offerteaanvragen worden aanvaard.']);
if ((int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 10000) respond(413, ['ok'=>false, 'error'=>'Uw aanvraag is te lang.']);
$raw = file_get_contents('php://input', false, null, 0, 10001);
if (strlen($raw) > 10000) respond(413, ['ok'=>false, 'error'=>'Uw aanvraag is te lang.']);
$data = json_decode($raw, true);
if (!is_array($data)) respond(400, ['ok'=>false, 'error'=>'Controleer uw aanvraag en probeer opnieuw.']);
function field(array $data, string $key, int $max): string {
    $value = $data[$key] ?? '';
    if (!is_string($value) || strlen($value) > $max || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $value)) respond(422, ['ok'=>false, 'error'=>'Controleer de ingevulde gegevens.']);
    return trim($value);
}
$name=field($data,'naam',400); $place=field($data,'plaats',400);
$service=field($data,'dienst',160); $phone=field($data,'telefoon',120);
$email=field($data,'email',150); $period=field($data,'periode',480);
$message=field($data,'bericht',6000); $trap=field($data,'website',500);
$id=field($data,'request_id',40);
if ($trap !== '') respond(422, ['ok'=>false, 'error'=>'De aanvraag kon niet worden verstuurd. Bel ons gerust.']);
if (!$name || !$place || strlen($message)<20 || (!$phone && !$email) || !preg_match('/^[a-f0-9-]{36}$/D', $id)) respond(422, ['ok'=>false,'error'=>'Vul uw naam, gemeente, tuinproject en een telefoonnummer of e-mailadres in.']);
if (($email && !filter_var($email,FILTER_VALIDATE_EMAIL)) || preg_match('/[\r\n]/', $email.$phone.$name.$place.$period)) respond(422, ['ok'=>false,'error'=>'Controleer uw contactgegevens.']);
$services=['Tuinonderhoud','Hagen scheren','Bomen snoeien','Bomen vellen en kappen','Tuinaanleg','Tuinrenovatie','Gras maaien en gazononderhoud','Tuinopkuis','Terrassen aanleggen','Opritten en tuinpaden','Kleine grondwerken','Andere tuinwerken'];
if (!in_array($service,$services,true)) respond(422,['ok'=>false,'error'=>'Kies een tuinwerk uit de lijst.']);
// Keep only anonymous counters and request hashes, never the form contents.
$file=@fopen(sys_get_temp_dir().'/tuinman-offerte-'.hash('sha256',__DIR__).'.json','c+');
if (!$file || !flock($file, LOCK_EX)) respond(503,['ok'=>false,'error'=>'Verzenden is tijdelijk niet beschikbaar. Bel of WhatsApp ons.']);
$state=json_decode(stream_get_contents($file),true) ?: ['day'=>date('Y-m-d'),'total'=>0,'visitors'=>[],'sent'=>[]];
if (($state['day'] ?? '')!==date('Y-m-d')) $state=['day'=>date('Y-m-d'),'total'=>0,'visitors'=>[],'sent'=>[]];
$requestKey=hash('sha256',$id); $ipKey=hash('sha256',($_SERVER['REMOTE_ADDR'] ?? 'unknown').__DIR__);
if (isset($state['sent'][$requestKey])) respond(200,['ok'=>true,'reference'=>$state['sent'][$requestKey]]);
$now=time(); $history=array_values(array_filter($state['visitors'][$ipKey] ?? [],function($time)use($now){return $time>$now-3600;}));
if ($state['total']>=200 || count($history)>=5 || ($history && max($history)>$now-60)) {
    header('Retry-After: 60'); respond(429,['ok'=>false,'error'=>'Wacht even voor u opnieuw verstuurt, of neem rechtstreeks contact op.']);
}
$reference='TIB-'.date('Ymd').'-'.strtoupper(substr($requestKey,0,8));
$body="Offerteaanvraag via tuinmanindebuurt.be\nReferentie: $reference\n\nNaam: $name\nGemeente/deelgemeente: $place\nDienst: $service\nTelefoon: ".($phone ?: 'Niet opgegeven')."\nE-mail: ".($email ?: 'Niet opgegeven')."\nGewenste periode: ".($period ?: 'In overleg')."\n\nMijn tuinproject:\n$message\n";
$subject='=?UTF-8?B?'.base64_encode('Offerteaanvraag tuinwerken — '.$place.' ['.$reference.']').'?=';
$headers=['From: Tuinman in de buurt <offerte@tuinmanindebuurt.be>','MIME-Version: 1.0','Content-Type: text/plain; charset=UTF-8','Content-Transfer-Encoding: 8bit','X-Mailer: Tuinman-offerte'];
if ($email) $headers[]='Reply-To: '.$email;
$accepted=@mail('diovanswalm@outlook.be',$subject,$body,implode("\r\n",$headers));
if (!$accepted) respond(502,['ok'=>false,'error'=>'De mailserver kon uw aanvraag niet aannemen. Uw aanvraag is niet verstuurd. Bel of WhatsApp ons.']);
$history[]=$now; $state['visitors'][$ipKey]=$history; $state['total']++;
$state['sent'][$requestKey]=$reference;
rewind($file); ftruncate($file,0); fwrite($file,json_encode($state)); fflush($file); flock($file,LOCK_UN); fclose($file);
respond(200,['ok'=>true,'reference'=>$reference]);
