<?php
// library直下のlibrary.phpを読み込むように設定する
require_once(preg_replace('/Api[\\/\\\]chatDirect.php/','',__FILE__).'library.php');
$chat = new Chat;

//ダイレクトチャット画面でグループチャット番号で検索をかけるためにグループチャット番号を取得する
$group_chat_no=filter_input(INPUT_POST,"group_chat_no");
$direct_chat_create=filter_input(INPUT_POST,"direct_chat_create");
$GROUP_CHAT_EXCHANGE_COMMENT_TEXT=filter_input(INPUT_POST,"GROUP_CHAT_EXCHANGE_COMMENT_TEXT");
##########################################
// ダイレクトチャットで必要になる機能
// 送信ボタンを押した時に投稿内容が登録される
// 空で送信ボタンを押すと登録の処理が行われない
// 自分で作成した投稿に関しては、削除することが可能
// 投稿内容に関していいね、お気に入り登録、返信などをすることができる。(自分の投稿や相手の投稿に関わらず、つけれる)
##########################################

if(!is_null($direct_chat_create)){
  print 'button pushed<br>';
  if(!is_null($GROUP_CHAT_EXCHANGE_COMMENT_TEXT) and $GROUP_CHAT_EXCHANGE_COMMENT_TEXT!==""){
    print 'get data<br>';
    $chat->setDirectChatExchange($group_chat_no,$GROUP_CHAT_EXCHANGE_COMMENT_TEXT,$reply_to_dairect_chat_comment_no);
  }else{
    print 'no data<br>';
  }
}else{
  print 'button not pushed<br>';
}

//データベースとの接続
// $con = new Db;
//
// //ダイレクトチャット画面でグループチャット番号で検索をかけるためにグループチャット番号を取得する
// $group_chat_no = (!empty($_POST['group_chat_no']) ? $_POST['group_chat_no']:null);
// //やり取りしている内容を全件取得する
// $direct_chat_comments = $chat->getAllDirectChatExchange($group_chat_no);

//登録した内容でダイレクトチャット一覧を出力
// foreach($direct_chat_comments as $direct_comment) {
//   //この内部にhtmlデータや画像がある時やcsvデータがある時などの設定や吐き出すhtmlなどを指定してあげる。
//   echo $con->selectNo($direct_comment['DIRECT_CHAT_EXCHANGE_CREATE_MEMBER_NO'])['member_name']
//       .' '.date('Y年m月d日 H時i分s秒', strtotime($direct_comment['DIRECT_CHAT_EXCHANGE_INS_TS']))
//       .'</p>'
//       .'<p id="comment_text">'
//       //返信した時の返信に関するリンクをつける
//       .$reply_link
//       .$direct_comment['DIRECT_CHAT_EXCHANGE_COMMENT_TEXT']
//       .'</p>'
//       .'<a href="#reply_to" id="reply_comment" class="reply_to_'.$direct_comment['DIRECT_CHAT_EXCHANGE_DISPLAY_NO'].'" >返信する</a>'
//   ;
// }
