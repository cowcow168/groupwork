<?php
require_once(preg_replace('/Api[\\/\\\]boardComment.php/', '', __FILE__).'library.php');

$board = new Board;

// コメント登録時
if(!empty($_POST['register_board_comment'])){

    // 未入力
    if(empty($_POST['BOARD_COMMENT_TEXT'])){
        $msg = '入力されていません。';

    // 文字数上限超過
    }elseif(getWordCount($_POST['BOARD_COMMENT_TEXT']) > $board::COMMENT_TEXT_MAX){
        $msg = $board::COMMENT_TEXT_MAX.'文字以内で入力してください。';

    }else{

        $reply_to_board_comment_no = (!empty($_POST['reply_to_board_comment_no']) ? $_POST['reply_to_board_comment_no'] : null);

        $board->setBoardComment($_POST['board_topic_no'], $_POST['BOARD_COMMENT_TEXT'], $reply_to_board_comment_no);
        $msg = '投稿に成功しました。';

        // コメント登録に成功した場合、テキストボックスを空にする
        echo '<script>'
            .'document.board_comment.BOARD_COMMENT_TEXT.value = "";'
            .'</script>'
        ;

    }
}

// コメント削除時　　　　　　　　　　　　　　　　　　　　　　　　　　　　TODO:トランザクション処理の追加
if(!empty($_POST['delete_board_comment'])){
    $board->deleteBoardComment($_POST['BOARD_COMMENT_NO']);
    $msg = '削除に成功しました。';
}

// コメント番号押下時に表示される枠内の内容の初期化
$strCommentList ='';
$strOneComment ='';

if(empty($_POST['display_reply_comment_list'])){
    // メッセージ
    if(!empty($msg)){
        echo '<script>'
        .'$("#board_comment_msg").html("'
        .$msg
        .'");'
        .'</script>';
    }


    $con = new Db;
    $account = new Account;

    // もっと見るリンク押下回数（jQueryから取得）
    if(empty($_SESSION['page_num'])){
        $_SESSION['page_num'] = 0;
    }

    if(!empty($_POST['display_more'])){
        $_SESSION['page_num']++;
    }

    if(empty($_SESSION['register_num'])){
        $_SESSION['register_num'] = 0;
    }

    // Ajax実行時にGetパラメータが消えることの対策
    $board_topic_no = (!empty($_POST['board_topic_no']) ? $_POST['board_topic_no'] : $_GET['board_topic_no']);

    // 表示するコメント一覧の取得
    // （既に表示されてるコメント一覧の後ろ（厳密にはもっと見るリンクの直前）に追加表示させていく）
    if(empty($_POST['register_board_comment'])) {
        $board_comments = $board->getAllBoardComment($board_topic_no, $_SESSION['page_num'], false, false, $_SESSION['register_num']);
    }

    if(!empty($_POST['register_board_comment'])) {
        $board_comments = $board->getAllBoardComment($board_topic_no, $_SESSION['page_num'], false, true, $_SESSION['register_num']);
        $_SESSION['register_num']++;
    }


        // 初期表示のみもっと見るリンクを表示させるためのフラグ
        $displayMoreFlg = false;

        // 最後まで表示させた時にもっと見るリンクを表示させなくするためのフラグ
        $lastFlg = false;

        // コメント一覧表示
        foreach($board_comments as $board_comment){

            if(empty($_POST['register_board_comment']) && $_SESSION['page_num'] == 0){
                $displayMoreFlg = true;
            }

            if($board_comment['BOARD_COMMENT_DISPLAY_NO'] == 1){
                $lastFlg = true;
            }

            $reply_link = '';

            if(!empty($board_comment['REPLY_TO_BOARD_COMMENT_NO'])){
                $reply_link = '<p>'
                    .'<a href="javascript:void(0);"'
                    .' id="display_reply_comment_list" name="'.$board->getBoardComment($board_comment['PARENT_REPLY_TO_BOARD_COMMENT_NO'])['BOARD_COMMENT_NO'].'_'.$board->getBoardComment($board_comment['REPLY_TO_BOARD_COMMENT_NO'])['BOARD_COMMENT_NO'].'_'.$board->getBoardComment($board_comment['PARENT_REPLY_TO_BOARD_COMMENT_NO'])['BOARD_TOPIC_NO'].'">'
                    .'>>'.$board->getBoardComment($board_comment['REPLY_TO_BOARD_COMMENT_NO'])['BOARD_COMMENT_DISPLAY_NO']
                    .'</a>'
                    .'</p>'
                ;
            }

            echo '<div id="comment_'.$board_comment['BOARD_COMMENT_DISPLAY_NO'].'">'
                .'<p>'
                .$board_comment['BOARD_COMMENT_DISPLAY_NO'].':';

            if($board_comment['BOARD_COMMENT_STATUS'] == 0) {
                echo '</p><span id="delete_comment">このコメントは削除されました</span></div><br>';
                continue;
            }
            echo $con->selectNo($board_comment['BOARD_COMMENT_CREATE_MEMBER_NO'])['member_name']
                .' '.date('Y年m月d日 H時i分s秒', strtotime($board_comment['BOARD_COMMENT_INS_TS']))
                .'</p>'
                .'<p id="comment_text">'
                .$reply_link
                .$board_comment['BOARD_COMMENT_TEXT']
                .'</p>'
                .'<a href="#reply_to" id="reply_comment" class="reply_to_'.$board_comment['BOARD_COMMENT_NO'].'_'.$board_comment['BOARD_COMMENT_DISPLAY_NO'].'" >返信する</a>'
            ;

            // ログインユーザーに更新権限がある場合、削除ボタンの表示
            if($account->chkUpdatePermission($board_comment['BOARD_COMMENT_CREATE_MEMBER_NO'], null)){
                echo '<a href="#delete" id="delete_board_comment" class="delete_comment_'.$board_comment['BOARD_COMMENT_NO'].'_'.$board_comment['BOARD_TOPIC_NO'].'" >削除する</a>'
                ;
            }
            echo '<br>';


        }

        if($displayMoreFlg){
            echo '<a href="javascript:void(0);" id="display_more" name="'.$board_topic_no.'">もっと見る</a>';
        }

        if($lastFlg){
            echo '<script>$("#display_more").html("");</script>';
        }

        if(!empty($board_comment)){
            echo '<script>'
                .'$("#BOARD_TOPIC_UPDATE_MEMBER_NO").html('
                .'"'.$con->selectNo($board->getBoardTopic($board_comment['BOARD_TOPIC_NO'])['BOARD_TOPIC_UPDATE_MEMBER_NO'])['member_name'].'"'
                .');'
                .'$("#BOARD_TOPIC_UPD_TS").html('
                .'"'.date('Y年m月d日 H時i分s秒', strtotime($board->getBoardTopic($board_comment['BOARD_TOPIC_NO'])['BOARD_TOPIC_UPD_TS'])).'"'
                .');'
                .'$("#reply_to").html('
                .'""'
                .');'
                .'</script>';
        }




}else{
    $reply_board_comments = $board->getAllBoardCommentByReply($_POST['PARENT_REPLY_TO_BOARD_COMMENT_NO']);
    $con = new Db;
    foreach($reply_board_comments as $reply_board_comment){
        $html =
            '<p>'
            .$reply_board_comment['BOARD_COMMENT_DISPLAY_NO'].':';

        if(!empty($reply_board_comment['REPLY_TO_BOARD_COMMENT_NO'])){
            $reply_num = '<p>'
                .'>>'.$board->getBoardComment($reply_board_comment['REPLY_TO_BOARD_COMMENT_NO'])['BOARD_COMMENT_DISPLAY_NO']
                .'</p>'
            ;
        }

        $text = $reply_board_comment['BOARD_COMMENT_TEXT'];

        if($reply_board_comment['BOARD_COMMENT_STATUS'] == 0) {
            $text = '</p>このコメントは削除されました</div><br>';
        }

        $html .= $con->selectNo($reply_board_comment['BOARD_COMMENT_CREATE_MEMBER_NO'])['member_name']
            .' '.date('Y年m月d日 H時i分s秒', strtotime($reply_board_comment['BOARD_COMMENT_INS_TS']))
            .'</p>'
            .'<p>'
            .$reply_num
            .$text
            .'</p>'
        ;

        $strCommentList .= $html;

        if($reply_board_comment['BOARD_COMMENT_NO'] == $_POST['REPLY_TO_BOARD_COMMENT_NO']){
            $strOneComment = $html;
        }
    }


}



    echo '<script>'
        .'$("#reply_comment_list").html("'
        .$strCommentList
        .'");'
        .'$("#one_reply_comment").html("'
        .$strOneComment
        .'");'
        .'</script>';
    echo '<style>
    #reply_comment_window{
        position: fixed;

        top: 40%;

        left: 40%;

        width: 800px;

        

        margin: -100px 0px 0px -100px;
        background-color: #aaff63;
        color: #262266;
        
        overflow: hidden;
        

        z-index: 2;
    }
    
    #scroll{
        overflow: scroll;
    }

</style>
<div id="reply_comment_window" style="display:none;">
    <a href="javascript:void(0);" id="reply_comment_window_cancel">×</a>
    <a href="javascript:void(0);" id="display_comment_list">返信コメント全件表示</a>
    <a href="javascript:void(0);" id="display_one_comment">返信コメント表示</a>
    <div id ="scroll">
        <div id="reply_comment_list" style="display:none; height: 400px;">             
        </div>
        <div id="one_reply_comment" style="display:block; height: 80px;">          
        </div>
    </div>
</div>';





