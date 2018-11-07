<?php
if(!empty($_POST)){
  require_once('../../library/library.php');
}
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
$account = new Account;

//グループチャット一覧などを表示させる
foreach($chat->getAllGroupChat() as $group_chat){
  echo '<div id="group_chat_'.$group_chat['GROUP_CHAT_NO'].'">'
      .'<a href="chat_top?group_chat_no='.$group_chat['GROUP_CHAT_NO'].'">'
      .$group_chat['GROUP_CHAT_TITLE'].
      '</a>';

      // グループチャット(テーマチャット)のトピック数
      // .'<span>'
      // .'('.$group_chat['BOARD_TOPIC_NUM'].')'
      // .'</span>';
      // TODO グループチャットでは、自分で作成した掲示板などと違ってテーマチャットの所で削除する仕様の為、編集、削除ボタンをtop画面で表示しなくて良いかも?
      //ログインユーザーに更新権限がある場合、編集・削除ボタンの表示
      if($account->chkUpdatePermission($group_chat['GROUP_CHAT_CREATE_MEMBER_NO'], $group_chat['GROUP_CHAT_NO'])){
          echo '<input type="hidden" class="display_update_group_chat_'.$group_chat['GROUP_CHAT_NO'].'" id="GROUP_CHAT_TITLE" value="'.$group_chat['GROUP_CHAT_TITLE'].'">'
          .'<input type="button" class="display_update_group_chat_'.$group_chat['GROUP_CHAT_NO'].'" id="display_update_group_chat" value="編集">'

          .'<input type="hidden" class="delete_group_chat_'.$group_chat['GROUP_CHAT_NO'].'" id="GROUP_CHAT_NO" value="'.$group_chat['GROUP_CHAT_NO'].'">'
          .'<input type="button" class="delete_group_chat_'.$group_chat['GROUP_CHAT_NO'].'" id="delete_group_chat" value="削除">'
        ;
      }
      echo '</div>';
}
