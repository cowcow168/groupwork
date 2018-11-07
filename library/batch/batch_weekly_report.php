<?php
$batch = 1;
require_once('/var/www/html/xxx-report.xyz/library/const.php');
require_once('/var/www/html/xxx-report.xyz/library/Model/Db.php');

//月曜～木曜の午前9時2分の段階で提出されていない場合はメール送信
$to = '未提出者';
$add = 'From:weekly_report@xxxcorp.jp';
$title = '【週報提出】';

//自動返信メール本文
$message = '未提出者';
$message .= ' さん'."\n\n";
$message .= 'お疲れさまです。'."\n\n";
$message .= '週報の提出が過ぎてます。'."\n";
$message .= 'お忙しいとは思いますが、'."\n";
$message .= '至急ご提出いただけますようお願い致します。'."\n\n";
$message .= '週報の提出は毎週月曜日午前9時まで';
$message .= ' にご提出いただけますようお願い致します。'."\n\n";
$message .= "\n\n";
$message .= '※当メールは送信専用メールアドレスから送信しております。'."\n";
$message .= '※当メールが行き違いました場合は何卒ご容赦頂ますようお願い申し上げます。'."\n";
$message .= '※当メールにお心当たりのない場合は、誠に恐れ入りますが、破棄していただけますようお願い申し上げます。'."\n";
$message .= "\n\n";
$message .= '*********************************************'."\n\n";
$message .= 'EBA株式会社'."\n";
$message .= 'http://xxxcorp.jp/'."\n\n";
$message .= '*********************************************'."\n";

//メール設定
mb_language("ja");
mb_internal_encoding("UTF-8");

//自動返信メール送信設定
mb_send_mail($to,$title,$message,$add);
