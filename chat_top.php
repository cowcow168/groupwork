<?php
require_once('library/library.php');

$chat = new Chat;

//グループチャットNoがない時は、404ページへ
if(!empty($_GET['group_chat_no']) && empty($chat->getGroupChat($_GET['group_chat_no']))){
  header('Location: 404.php');
}

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

//各ページで削除した際のコメント関係などをセッションに持たせて、処理後は、廃棄させる
if(!empty($_SESSION['topic_msg'])){
  $_POST['topic_msg'] = $_SESSION['topic_msg'];
  unset($_SESSION['topic_msg']);
}

//削除した際のメッセージを出す(管理者は、消せる仕様にする)
// if(!empty($_POST['delete_group_chat'])){
  //掲示板では、ゴミ箱とその他などを出す時に使用しているようなので今回は、不要
  // if($_GET['group_chat_no'] != $chat->getBoardCategoryByName($_GET['group_chat_no'])){
  //掲示板では、自分で作成したものの時の表示を変える為にhtmlでクラスをつけて使用している。
  // foreach($_POST['delete_group_chat_list'] as $group_chat_no){
  //   $chat->updateBoardTopic($group_chat_no, $chat->getBoardCategoryByName($chat::DEFAULT_CATEGORY['trash'])['GROUP_CHAT_NO']);
  //   $_SESSION['topic_msg'] = 'トピックが削除されました。';
  // }
  // }
  //管理者名などは、暫定的に設置しているだけなので変更する(ifの条件の所)
  // if($_SESSION['admin']){
  //   foreach($_POST['delete_group_chat_list'] as $group_chat_no){
  //       $board->deleteBoardTopic($group_chat_no);
  //       $_SESSION['topic_msg'] = 'トピックが削除されました。（管理者権限）';
  //   }
  // }
// }


// 画面表示
Html::header($access_device);
Html::body(__FILE__, $access_device);
