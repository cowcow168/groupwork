<?php
$batch = 1;
require_once('/var/www/html/xxx-report.xyz/library/const.php');
require_once('/var/www/html/xxx-report.xyz/library/Mailer/Mailer.php');
require_once('/var/www/html/xxx-report.xyz/library/Model/Db.php');

$dbh = new PDO(MYSQL_DSN, MYSQL_DBUSER, MYSQL_DBPASS, [
    PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
]);

//社員をセレクト
$sql = 'SELECT member_id FROM member WHERE member_no < 99999990 AND member_status = 1 and member_team_type < 100';
$stmt = $dbh->query($sql);
$member = $stmt->fetchAll(PDO::FETCH_ASSOC);

$dbh->beginTransaction();

$i = 1;
$count = count($member);

foreach($member as $k => $v){
	$to .= $v['member_id'].'@xxxcorp.jp';
	if($i != $count){
		$to .= ',';
	}
	$i++;
}

//社長と石丸さんは例外でメール送るようにする
$to .= ',s.ebata@xxxcorp.jp,y.ishimaru@xxxcorp.jp';

$t = date('N', strtotime(date('Y-m-'.date('t'))));
if($t == 5 || $t == 6 || $t == 7){
	$result = 'して';
}else{
	$result = 'しないで';
}

$header  = 'Return-Path:no-reply@xxxcorp.jp'.N;
$header .= 'Error-To:k.harada@xxxcorp.jp'.N;
$header .= 'Reply-To:no-reply@xxxcorp.jp'.N;
$header .= 'From:no-reply@xxx-report.xyz'.N;

mb_language('Japanese');
mb_internal_encoding('UTF-8');
$str  = 'EBAの皆さん'.NNN;
$str .= 'お疲れ様です。週報システム自動送信メールです。'.NN;
$str .= date('Y年m月').'の月報時提出ボタンについてお知らせします。'.N;
$str .= '今月の月報提出時の提出ボタンは・・・'.NNN;
$str .= '押下「'.$result.'」ください！'.NNN;
$str .= '以上、宜しくお願い致します。';

if(mb_send_mail($to, '【EBA週報システム】'.date('Y年m月').'の月報提出ボタンについて', $str, $header)){
	return true;
}
