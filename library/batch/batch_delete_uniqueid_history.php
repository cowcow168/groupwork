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
$target = date('Y-m-d', strtotime(date('Y-m-d', strtotime('-2 day'))));

$sql = 'SELECT member_no, member_name, COUNT(member_no)AS delete_count FROM('
		. 'SELECT member_no FROM unique_id_history '
		. ' WHERE unique_id_history_ins_ts < ' . Db::dbstring($target.' 00:00:00.000000')
	. ')AS sub LEFT JOIN('
		. ' SELECT member_no, member_name FROM member '
	. ')AS member USING(member_no) '
	. ' GROUP BY member_no'
;
$stmt = $dbh->query($sql);
if($member = $stmt->fetchAll(PDO::FETCH_ASSOC)){
	foreach($member as $k => $v){
		$query .= 'member_no: '.$v['member_no'] . ' ' . $v['member_name'] . ' カウント' . $v['delete_count']."\n";
	}
	$dbh->beginTransaction();

	try {
		$sql1 = 'DELETE FROM unique_id_history '
			. ' WHERE unique_id_history_ins_ts < ' . Db::dbstring($target.' 00:00:00.000000');
		if($dbh->query($sql1)){
			$a = 'delete from unique_id_history OK'."\n";
		}else{
			$a = 'delete from unique_id_history NG'."\n";
		}
	} catch(Exception $e){
		$a = $e;
		$dbh->rollback();
	}

	$dbh->commit();
}else{
	$query = '削除対象者はいませんでした。'."\n";
	$a = '';
}
(new Mailer())->chkBatchMail('k.harada@xxxcorp.jp', '【バッチ実行結果】'.date('Y年m月d日', strtotime('-2 day')).'分　週報システム　ユニークIDヒストリー削除', $sql."\n\n".$query.$a);