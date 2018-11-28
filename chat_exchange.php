<?php
require_once('library/library.php');

// セッションチェック
(new Account)->authChk(@$_SESSION['user_name']);

// アカウント更新チェック
Account::sessionChangeChk($_SESSION['user_name'],
                          $_SESSION['permission'],
                          $_SESSION['member_no'],
                          $_SESSION['user_real_name'],
                          $_SESSION['member_belonging'],
                          $_SESSION['belonging_leader'],
                          $_SESSION['member_team_type']);

// 他人のを見る場合、権限チェック
@Account::chkPermission($_REQUEST['member_no'],
                        $_SESSION['member_no'],
                        $_SESSION['permission']);

// ログアウトチェック
(new Account)->logoutChk(@$_REQUEST['logout']);

$chat = new Chat;
//ダイレクトチャットで送信ボタンが押された時
if(!empty($_POST['direct_chat_create'])){
  //データベースの登録を行う
}

// 画面表示
Html::header($access_device);
Html::body(__FILE__, $access_device);
