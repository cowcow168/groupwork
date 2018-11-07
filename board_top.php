<?php
require_once('library/library.php');

$board = new Board;

if(!empty($_GET['board_category_no']) && empty($board->getBoardCategory($_GET['board_category_no']))){
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

if(!empty($_SESSION['topic_msg'])){
    $_POST['topic_msg'] = $_SESSION['topic_msg'];
    unset($_SESSION['topic_msg']);
}

if(!empty($_POST['delete_board_topic'])){
    if($_GET['board_category_no'] != $board->getBoardCategoryByName($board::DEFAULT_CATEGORY['trash'])['BOARD_CATEGORY_NO']){
        foreach($_POST['delete_board_topic_list'] as $board_topic_no){
            $board->updateBoardTopic($board_topic_no, $board->getBoardCategoryByName($board::DEFAULT_CATEGORY['trash'])['BOARD_CATEGORY_NO']);
            $_SESSION['topic_msg'] = 'トピックが削除されました。';
        }
    }else{
        foreach($_POST['delete_board_topic_list'] as $board_topic_no){
            $board->deleteBoardTopic($board_topic_no);
            $_SESSION['topic_msg'] = 'トピックが削除されました。（管理者権限）';
        }
    }
}

// 画面表示
Html::header($access_device);
Html::body(__FILE__, $access_device);
