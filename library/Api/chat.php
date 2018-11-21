<?php
//library直下のlibrary.phpを読み込むように設定する
require_once(preg_replace('/Api[\\/\\\]chat.php/','',__FILE__).'library.php');
$chat = new Chat;
#
#下記は、後で(サイボウズと比較して)実装する
#

// グループチャット登録時
// if(!empty($_POST['register_group_chat'])){
//
//     // 未入力
//     if(empty($_POST['GROUP_CHAT_TITLE'])){
//         $msg = '入力されていません。';
//     }else{
//         // 同名グループチャット登録済み
//         if($chat->getBoardCategoryByName($_POST['GROUP_CHAT_TITLE'])){
//             $msg = '既に登録済みです。';
//
//             // グループチャット名登録　　　　　　　　　　　　　　　　　　　　　　　　TODO:トランザクション処理の追加
//         }else {
//             $chat->setBoardCategory($_POST['GROUP_CHAT_TITLE']);
//             $msg = '登録に成功しました。';
//
//             // グループチャット登録に成功した場合、テキストボックスを空にする
//             echo '<script>'
//                 .'document.group_chat.GROUP_CHAT_TITLE.value = "";'
//                 .'</script>'
//             ;
//
//         }
//     }
// }
//
// // グループチャット編集時
// if(!empty($_POST['update_group_chat'])){
//
//     // 未入力
//     if(empty($_POST['GROUP_CHAT_TITLE'])){
//         $msg = '入力されていません。';
//
//     }else{
//
//         // 同名グループチャット編集済み
//         if($chat->getGroupChatByName($_POST['GROUP_CHAT_TITLE'])){
//             $msg = '変更前と同じグループチャット名です。';
//
//             if($chat->getGroupChatByName($_POST['GROUP_CHAT_TITLE'])['GROUP_CHAT_NO'] != $_POST['GROUP_CHAT_NO']){
//                 $msg = '既に登録済みです。';
//             }
//
//             // グループチャット名編集　　　　　　　　　　　　　　　　　　　　　　　　TODO:トランザクション処理の追加
//         }else {
//             $chat->updateGroupChat($_POST['GROUP_CHAT_NO'], $_POST['GROUP_CHAT_TITLE']);
//             $msg = '編集に成功しました。';
//
//         }
//     }
// }

// グループチャット削除時　　　　　　　　　　　　　　　　　　　　　　　　　　　　TODO:トランザクション処理の追加
if(!empty($_POST['leave_group_chat'])){
    $chat->deleteGroupChat($_POST['GROUP_CHAT_NO']);
    // $msg = '削除に成功しました。';
}
