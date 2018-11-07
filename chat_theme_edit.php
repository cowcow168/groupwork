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

//テーマチャット作成時バリデーションチェック
if(!empty($_POST['chat_theme_create'])){
  //グループタイトルのバリデーション
  if(empty($_POST['GROUP_CHAT_TITLE'])){
    $_POST['err'][] = 'タイトルが入力されていません。';
    //グループタイトル文字数上限超過
  }elseif($chat->getGroupChatByName($_POST['GROUP_CHAT_TITLE'])) {
    $_POST['err'][] = '入力されたタイトルは既に登録済みです。';
  }

  //グループチャット作成登録画面に遷移してきた時
  if($_GET['edit_type'] == 'register'){
    //エラーがなければ登録処理を行う
    if(empty($_POST['err'])){
      $chat->setGroupChat($_POST['GROUP_GHAT_NO'],$_POST['GROUP_CHAT_TITLE'],$_POST['GROUP_CHAT_MEMO'],$_POST['GROUP_CHAT_ATTACHED_FILE']);
      //チャットの一覧ページ(ダイレクトページとテーマチャットが一覧で見れるページ)メッセージは、
            $_SESSION['edit_topic_msg'] = 'トピックが'.$chat->getGroupChatEditType($_GET['edit_type']).'されました';
      header("Location:chat_top");
    }
  }
}

//テーマチャットから抜けるが押された時(削除する時の処理)
if(!empty($_POST['leave_group_chat'])){
  $chat->deleteGroupChat($_POST['GROUP_CHAT_NO']);
}
// 画面表示
Html::header($access_device);
Html::body(__FILE__, $access_device);
