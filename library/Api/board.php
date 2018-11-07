<?php
if(!empty($_POST)){
    require_once('../../library/library.php');
}

$board = new Board;

// カテゴリ登録時
if(!empty($_POST['register_board_category'])){

    // 未入力
    if(empty($_POST['BOARD_CATEGORY_NAME'])){
        $msg = '入力されていません。';

    // 文字数上限超過
    }elseif(getWordCount($_POST['BOARD_CATEGORY_NAME']) > $board::CATEGORY_NAME_MAX){
        $msg = $board::CATEGORY_NAME_MAX.'文字以内で入力してください。';
    }else{

        // 同名カテゴリ登録済み
        if($board->getBoardCategoryByName($_POST['BOARD_CATEGORY_NAME'])){
            $msg = '既に登録済みです。';

            // カテゴリ名登録　　　　　　　　　　　　　　　　　　　　　　　　TODO:トランザクション処理の追加
        }else {
            $board->setBoardCategory($_POST['BOARD_CATEGORY_NAME']);
            $msg = '登録に成功しました。';

            // カテゴリ登録に成功した場合、テキストボックスを空にする
            echo '<script>'
                .'document.board_category.BOARD_CATEGORY_NAME.value = "";'
                .'</script>'
            ;

        }
    }
}

// カテゴリ編集時
if(!empty($_POST['update_board_category'])){

    // 未入力
    if(empty($_POST['BOARD_CATEGORY_NAME'])){
        $msg = '入力されていません。';

    // 文字数上限超過
    }elseif(getWordCount($_POST['BOARD_CATEGORY_NAME']) > $board::CATEGORY_NAME_MAX){
        $msg = $board::CATEGORY_NAME_MAX.'文字以内で入力してください。';

    }else{

        // 同名カテゴリ編集済み
        if($board->getBoardCategoryByName($_POST['BOARD_CATEGORY_NAME'])){
            $msg = '変更前と同じカテゴリ名です。';

            if($board->getBoardCategoryByName($_POST['BOARD_CATEGORY_NAME'])['BOARD_CATEGORY_NO'] != $_POST['BOARD_CATEGORY_NO']){
                $msg = '既に登録済みです。';
            }

            // カテゴリ名編集　　　　　　　　　　　　　　　　　　　　　　　　TODO:トランザクション処理の追加
        }else {
            $board->updateBoardCategory($_POST['BOARD_CATEGORY_NO'], $_POST['BOARD_CATEGORY_NAME']);
            $msg = '編集に成功しました。';

        }
    }
}

// カテゴリ削除時　　　　　　　　　　　　　　　　　　　　　　　　　　　　TODO:トランザクション処理の追加
if(!empty($_POST['delete_board_category'])){
    $board->deleteBoardCategory($_POST['BOARD_CATEGORY_NO']);
    $msg = '削除に成功しました。';
}

// メッセージ
if(!empty($msg)){
    echo '<p>'.$msg.'</p>';
}

$account = new Account;

// カテゴリ一覧表示
foreach($board->getAllBoardCategory() as $board_category){
    echo '<div id="category_'.$board_category['BOARD_CATEGORY_NO'].'">'
        .'<a href="board_top?board_category_no='.$board_category['BOARD_CATEGORY_NO'].'">'
        .$board_category['BOARD_CATEGORY_NAME'].
        '</a>'

        // カテゴリ内トピック数
        .'<span>'
        .'('.$board_category['BOARD_TOPIC_NUM'].')'
        .'</span>';

    // ログインユーザーに更新権限がある場合、編集・削除ボタンの表示
    if($account->chkUpdatePermission($board_category['BOARD_CATEGORY_CREATE_MEMBER_NO'], $board_category['BOARD_CATEGORY_NO'])){
        echo '<input type="hidden" class="display_update_board_category_'.$board_category['BOARD_CATEGORY_NO'].'" id="BOARD_CATEGORY_NAME" value="'.$board_category['BOARD_CATEGORY_NAME'].'">'
            .'<input type="button" class="display_update_board_category_'.$board_category['BOARD_CATEGORY_NO'].'" id="display_update_board_category" value="編集">'

            .'<input type="hidden" class="delete_board_category_'.$board_category['BOARD_CATEGORY_NO'].'" id="BOARD_CATEGORY_NO" value="'.$board_category['BOARD_CATEGORY_NO'].'">'
            .'<input type="button" class="delete_board_category_'.$board_category['BOARD_CATEGORY_NO'].'" id="delete_board_category" value="削除">'
        ;
    }

    echo '</div>';
}

