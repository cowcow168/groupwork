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

// ログアウトチェック
(new Account)->logoutChk(@$_REQUEST['logout']);

// 画面表示
Html::header($access_device);
Html::body('404', $access_device);