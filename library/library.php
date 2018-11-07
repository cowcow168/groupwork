<?php
session_start();

//メンテ情報をnav上部に表示
if(date('Y-m-d H:i:s') < '2018-05-04 10:00:00'){
    global $mente_mes;
    $mente_mes = '5月4日午前10時から終了未定でメンテナンスです。';
}

//原田スマホかEBA研修室からのアクセスならこの中のコードを検証できる（本番使用時は10行目のifは活性化しない）
//if(($_SERVER['REMOTE_ADDR'] == '126.99.244.220' || $_SERVER['REMOTE_ADDR'] == '126.99.244.220') && $_SERVER['PHP_AUTH_USER'] == 'k.harada'){

    //時限式メンテナンスシステム
    if(date('Y-m-d H:i:s') >= '2018-05-04 10:00:00' && date('Y-m-d H:i:s') < '2018-05-04 12:30:00'){
        //xxのauthじゃない
        if($_SERVER['PHP_AUTH_USER'] != 'x.xxxxx'
         //||
        //研修のIPじゃない
        //$_SERVER['REMOTE_ADDR'] != 'xxx.xx.xxx.xxx'
           //||
        //営業部じゃない
        //$_SERVER['REMOTE_ADDR'] != 'xxx.xx.xxx.xxx'
            //||
        //xxスマホじゃない
        //$_SERVER['REMOTE_ADDR'] != 'xxx.xx.xxx.xxx'
        ){
            require_once('maintenance20180504.php');
            exit;
        }else{
            //研修室かxxスマホなら
            require_once('maintenance20180504.php');
            exit;
        }
    }
//}

$env = '';
if(preg_match('/^192\.168\./', $_SERVER['HTTP_HOST'])){
    $env = 'local';
} else {
    $env = 'production';
}

if($env == 'local'){
// エラー、警告を表示するようにする
    ini_set('display_errors', true);
//error_reporting(E_ALL);
} else {
    if($_SESSION['member_no'] == 4){
        ini_set('display_errors', true);
    }else{
        ini_set('display_errors', true);
    }
}

//アクセス媒体の分類
if((strpos($_SERVER['HTTP_USER_AGENT'], 'Android') != false && strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') != false)
    ||
    (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') != false || strpos($_SERVER['HTTP_USER_AGENT'], 'Windows Phone') != false)
){
    $access_device = 'sp';
}else{
    $access_device = 'pc';
}

//テストサーバかつ個人ディレクトリだったら自分の名前を取る
if(preg_match('/xxxcorp\.xyz/', $exp = explode('/', $_SERVER['SCRIPT_FILENAME'])[4])){
    $hoge = end(explode('_', explode('/', $_SERVER['SCRIPT_FILENAME'])[5]));
    if($hoge != 'eba-groupwork'){
        $_SERVER['DOCUMENT_ROOT'] .= 'eba-groupwork_'. $hoge.'/';
    }else{
        $_SERVER['DOCUMENT_ROOT'] .= 'eba-groupwork/';
    }
}

// 定数読み込み
require_once($_SERVER['DOCUMENT_ROOT'].'library/const.php');

// 時刻初期化
date_default_timezone_set('Asia/Tokyo');

// Mailer
require_once(LIBRARY.'Model/Mailer.php');

// Html
require_once(LIBRARY.'Html/Html.php');

// Model
require_once(LIBRARY.'Model/Db.php');

// Account
require_once(LIBRARY.'Account/Account.php');

// Api
require_once(LIBRARY.'Api/Api.php');

// ログ書き込み
function logger($msg, $flg=false)
{
    if(is_array($msg)){
        $msg = var_export($msg,true);
    }
    system('sudo chmod 777 '.LOG_FILE);
    if(!file_exists(LOG_FILE)){
        system('sudo touch '.LOG_FILE);
        system('sudo chmod 777 '.LOG_FILE);
    }
    error_log('['.(new DateTime)->format('Y-m-d H:i:s').'] '.$msg.PHP_EOL, 3, LOG_FILE);
    if(!empty($flg)){
        Mailer::sendErrMail(
            'EBA株式会社　週報システムで以下の障害が発生しました。'.NN
            .$msg
        );
    }
    system('sudo chmod 644 '.LOG_FILE);
}

// ログ書き込み
function timeCardLogger($msg, $flg=false)
{
    if(is_array($msg)){
        $msg = var_export($msg,true);
    }
    chmod('/var/www/html/xxx-report.xyz/log/timecard_logger.log', 0777);
    error_log('['.(new DateTime)->format('Y-m-d H:i:s').'] '.$msg.PHP_EOL, 3, LOG_FILE);
    if(!empty($flg)){
        Mailer::sendErrMail(
            'EBA株式会社　週報システムのタイムカード作成に障害が発生しました。'.NN
            .$msg
        );
    }
    chmod('/var/www/html/xxx-report.xyz/log/timecard_logger.log', 0644);
}

// デバッグ用関数
function dbg($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function debug_num() {
    $bt = debug_backtrace();
    $file = $bt[0]['file'];
    $line = $bt[0]['line'];
    print_r("$file $line\n", true);
}

// DB登録用　日付をミリ秒6桁まで取得
function getNow(){
    return date('Y-m-d H:i:s.').explode('.', sprintf('%6F', microtime(true)))[1];
}

// 文字数取得（改行除き、スペース含む）
function getWordCount($str){
    return mb_strlen(trim(mb_convert_kana($str, 's')));
}
