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


$board = new Board;

if(!empty($_POST['edit_board_topic'])){

    // タイトルのバリデーションチェック
    // タイトル未入力
    if(empty($_POST['BOARD_TOPIC_TITLE'])){
        $_POST['err'][] = 'タイトルが入力されていません';
    // タイトル文字数上限超過
    }elseif(getWordCount($_POST['BOARD_TOPIC_TITLE']) > $board::TOPIC_TITLE_MAX) {
        $_POST['err'][] = 'タイトルは'.$board::TOPIC_TITLE_MAX . '文字以内で入力してください。';
    // 同名タイトル登録済み
    }elseif ($board->getBoardTopicByName($_POST['BOARD_TOPIC_TITLE'])) {
        $_POST['err'][] = '入力されたタイトルは既に登録済みです。';
    }

    // トピック登録前に選択カテゴリが削除された場合
    if(empty($board->getBoardCategory($_POST['BOARD_CATEGORY_NO']))){
        $_POST['err'][] = '選択したカテゴリが削除されました';
    }

    // 本文未入力
    if(empty($_POST['BOARD_TOPIC_TEXT'])){
        $_POST['err'][] = '本文が入力されていません';
    // 本文文字数上限超過
    }elseif(getWordCount($_POST['BOARD_TOPIC_TEXT']) > $board::TOPIC_TEXT_MAX) {
        $_POST['err'][] = '本文は'.$board::TOPIC_TEXT_MAX . '文字以内で入力してください。';
    }
    if($_GET['edit_type'] == 'register'){
        if(empty($_POST['err'])){
            $board->setBoardTopic($_POST['BOARD_CATEGORY_NO'], $_POST['BOARD_TOPIC_TITLE'], $_POST['BOARD_TOPIC_TEXT']);
            $_SESSION['edit_topic_msg'] = 'トピックが'.$board->getTopicEditType($_GET['edit_type']).'されました';
            header("Location:board_top");
        }
    }
}







// 画面表示
Html::header($access_device);
Html::body(__FILE__, $access_device);
