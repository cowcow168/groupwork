<?php
$batch = 1;
require_once('/var/www/html/xxx-report.xyz/library/const.php');
require_once('/var/www/html/xxx-report.xyz/library/Mailer/Mailer.php');
require_once('/var/www/html/xxx-report.xyz/library/Model/Db.php');

$dbh = new PDO(MYSQL_DSN, MYSQL_DBUSER, MYSQL_DBPASS, [
    PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
]);

//6日になったら無条件で修正不可能にする
$target = date('Y-m-01', strtotime(date('Y-m-01', strtotime('-1 month'))));

$dbh->beginTransaction();

$sql1 = 'UPDATE weekly_report SET '
	. ' weekly_report_input_status = 0 '
	. ' WHERE weekly_report_year_month = ' . Db::dbstring($target);
if($dbh->query($sql1)){
	$a = 'weekly_report OK'."\n";
}

$sql2 = 'UPDATE time_card SET '
	. ' time_card_input_status = 0 '
	. ' WHERE time_card_year_month = ' . Db::dbstring($target);
if($dbh->query($sql2)){
	$b = 'time_card OK'."\n";
}

$sql3 = 'UPDATE pay_off SET '
	. ' pay_off_input_status = 0 '
	. ' WHERE pay_off_year_month = ' . Db::dbstring($target);
if($dbh->query($sql3)){
	$c = 'pay_off OK'."\n";
}

$dbh->commit();

(new Mailer())->chkBatchMail('k.harada@xxxcorp.jp', '【バッチ実行結果】'.date('Y年m月', strtotime('-1 month')).'分　週報システム　保存制御', $a.$b.$c);