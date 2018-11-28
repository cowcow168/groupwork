<?php
// library直下のlibrary.phpを読み込むように設定する
require_once(preg_replace('/Api[\\/\\\]chatDirect.php/','',__FILE__).'library.php');
// require_once(dirname(__FILE__).'../../library.php');

//2018/08/15
/**
* Class DirectChat
*
*ダイレクトチャットの表示などのクラス
*
*/
class ChatDirect extends Db
{

  public function commnet_list($chat_direct_comments){
    //データベースとの接続
    $con = new Db;
    //ログインユーザーの表示
    $account = new Account;
    //登録した内容でダイレクトチャット一覧を出力
    foreach($chat_direct_comments as $direct_comment) {
      echo '<div class="chatRoomCommentBlock">'
          .'<div class="createMemberIcon">'
          .'<img src="" class="icon" />'
          // 自分の名前が表示される
          .'<div class="name">'
          .'<div class="name">'
          // .'<a href="/mpConfiguration/profile/view" class="profileLb iconLink" id="profile_1:884608" title="'.$con->selectNo($direct_comment['DIRECT_CHAT_EXCHANGE_CREATE_MEMBER_NO'])['member_name'].'">'
          .'<span class="name">'
          .$con->selectNo($direct_comment['DIRECT_CHAT_EXCHANGE_CREATE_MEMBER_NO'])['member_name']
          .'</span>'
          .'</a>'
          .'</div>'
          .'</div>'
          .'<div class="commentItemList">'
          .'<div data-id="17601719" data-number="28" data-createDateInLong="1528346837000" class="commentItem  ">'
          .'<div class="commentContents ">'
          .'<div class="commentBody selfclear">'
          .'<div class="commentBodyLeft">'
          .'<div class="formatContents">'
      ;
      //コメントが入る場所。サイボウズでは、pタグは入らないみたいです。
      echo $direct_comment['DIRECT_CHAT_EXCHANGE_COMMENT_TEXT']
          .'</div>'
          .'</div>'
      ;
      //自分で投稿した投稿の時は、下記が表示される
      if($account->chkUpdatePermission($direct_comment['DIRECT_CHAT_EXCHANGE_CREATE_MEMBER_NO'],null)){
        echo '<div class="commentBodyRight">'
          .'<div class="commentDelete operation hiddenOperation" style="display:none">'
          .'<a href="#deleteComment_fid=17601719" class="iconLink delete" title="削除する">'
          .'<img src="https://cybozulive.com/static/49f5cdb4ca/images/delete_white14.png" class="icon" alt="削除する" />'
          .'</a>'
          .'</div>'
          .'</div>'
        ;
      }
      //この内部にhtmlデータや画像がある時やcsvデータがある時などの設定や吐き出すhtmlなどを指定してあげる。
      echo '</div>'
          .'<div class="commentFooter operationParts selfclear">'
          .'<div class="operation">'
          .'<a href="javascript:void(0);" data-url="/tip/commentUrl?url=https%3A%2F%2Fcybozulive.com%2FmpChat%2Fview%3FchatRoomId%3D2%253A15547680%26fid%3D17601719" class="commentUrlTip" title="固定リンク">'
          .'<span>No.</span>'
          // 表示番号を出力する
          .'<span class="number">'.$direct_comment['DIRECT_CHAT_EXCHANGE_DISPLAY_NO'].'</span>'
          .'<span class="createDate">'.$direct_comment['DIRECT_CHAT_EXCHANGE_INS_TS'].'</span>'
          .'</a>'
        ;
      //いいねと件数を表示する
      echo '<span class="good">'
          .'<span data-url="/common/good/goodAjax" data-mode="SIMPLE" data-use-comet="TRUE">'
          .'<a href="javascript:void(0);" class="iconLink goodLinkOn" id="cbaGood-MP_CHAT-2:15547680-17601719-on" data-url="/comet/common/good/goodTurnJsonDirect">'
          .'<img src="https://cybozulive.com/static/49f5cdb4ca/images/good14.png" alt="" class="icon" />'
          .'<span>いいね！</span>'
          .'</a>'
          .'</span>'
          .'<span class="goodMemberCountParts" data-url="/common/good/goodMemberListAjax" style="display:none">'
          .'<a href="javascript:void(0);" class="goodMemberCount goodMemberListTip" data-url="/common/good/goodMemberListTip?application=MP_CHAT&amp;targetId=2%3A15547680&amp;subTargetId=17601719">'
          .date('Y年m月d日 H時i分s秒', strtotime($direct_comment['DIRECT_CHAT_EXCHANGE_INS_TS']))
          .'</a>'
          .'<span>件</span>'
          .'</span>'
          .'</span>'
        ;
      //返信とお気に入りボタンなどを表示
      echo '<a href="javascript:void(0);" id="chatRoomComment_28" class="replyChatRoomComment iconLink">'
          .'<img src="https://cybozulive.com/static/49f5cdb4ca/images/reply14.png" alt="" class="icon" />'
          .'<span>返信</span>'
          .'</a>'
          .'<a href="javascript:void(0);" data-url="/common/star/starTurnJsonDirect" id="cbaStar-MP_CHAT--2:15547680-17601719" class="star iconLink" data-displayLabel="true">'
          .'<img src="https://cybozulive.com/static/49f5cdb4ca/images/star_off14.png" class="icon" alt="お気に入りに登録する" data-star="off" />'
          .'<span class="starLabel">お気に入り</span>'
          .'</a>'
          .'</div>'
          .'</div>'
          .'</div>'
          .'</div>'
          .'</div>'
          .'</div>'
        ;


      // echo $con->selectNo($direct_comment['DIRECT_CHAT_EXCHANGE_CREATE_MEMBER_NO'])['member_name']
      //     .' '.date('Y年m月d日 H時i分s秒', strtotime($direct_comment['DIRECT_CHAT_EXCHANGE_INS_TS']))
      //     .'</p>'
      //     .'<p id="comment_text">'
      //     //返信した時の返信に関するリンクをつける
      //     .$reply_link
      //     .$direct_comment['DIRECT_CHAT_EXCHANGE_COMMENT_TEXT']
      //     .'</p>'
      //     .'<a href="#reply_to" id="reply_comment" class="reply_to_'.$direct_comment['DIRECT_CHAT_EXCHANGE_DISPLAY_NO'].'" >返信する</a>'
      // ;
      //ログインユーザーに更新権限がある場合、削除ボタンの表示
      if($account->chkUpdatePermission($direct_comment['DIRECT_CHAT_EXCHANGE_CREATE_MEMBER_NO'],null)){
        echo '<a href="#delete" name="delete_chat_direct_comment" class="delete_comment_'.$direct_comment['DIRECT_CHAT_EXCHANGE_NO'].'" >削除する</a>'
        ;
      }
    }
  }

  public function latest_commnet_list($chat_direct_comment){
    //データベースとの接続
    $con = new Db;
    //ログインユーザーの表示
    $account = new Account;
    //登録した内容でダイレクトチャット一覧を出力
      echo '<div class="chatRoomCommentBlock">'
          .'<div class="createMemberIcon">'
          .'<img src="" class="icon" />'
          // 自分の名前が表示される
          .'<div class="name">'
          .'<div class="name">'
          // .'<a href="/mpConfiguration/profile/view" class="profileLb iconLink" id="profile_1:884608" title="'.$con->selectNo($direct_comment['DIRECT_CHAT_EXCHANGE_CREATE_MEMBER_NO'])['member_name'].'">'
          .'<span class="name">'
          .$con->selectNo($chat_direct_comment['DIRECT_CHAT_EXCHANGE_CREATE_MEMBER_NO'])['member_name']
          .'</span>'
          .'</a>'
          .'</div>'
          .'</div>'
          .'<div class="commentItemList">'
          .'<div data-id="17601719" data-number="28" data-createDateInLong="1528346837000" class="commentItem  ">'
          .'<div class="commentContents ">'
          .'<div class="commentBody selfclear">'
          .'<div class="commentBodyLeft">'
          .'<div class="formatContents">'
      ;
      //コメントが入る場所。サイボウズでは、pタグは入らないみたいです。
      echo $chat_direct_comment['DIRECT_CHAT_EXCHANGE_COMMENT_TEXT']
          .'</div>'
          .'</div>'
      ;
      //自分で投稿した投稿の時は、下記が表示される
      if($account->chkUpdatePermission($chat_direct_comment['DIRECT_CHAT_EXCHANGE_CREATE_MEMBER_NO'],null)){
        echo '<div class="commentBodyRight">'
          .'<div class="commentDelete operation hiddenOperation" style="display:none">'
          .'<a href="#deleteComment_fid=17601719" class="iconLink delete" title="削除する">'
          .'<img src="https://cybozulive.com/static/49f5cdb4ca/images/delete_white14.png" class="icon" alt="削除する" />'
          .'</a>'
          .'</div>'
          .'</div>'
        ;
      }
      //この内部にhtmlデータや画像がある時やcsvデータがある時などの設定や吐き出すhtmlなどを指定してあげる。
      echo '</div>'
          .'<div class="commentFooter operationParts selfclear">'
          .'<div class="operation">'
          .'<a href="javascript:void(0);" data-url="/tip/commentUrl?url=https%3A%2F%2Fcybozulive.com%2FmpChat%2Fview%3FchatRoomId%3D2%253A15547680%26fid%3D17601719" class="commentUrlTip" title="固定リンク">'
          .'<span>No.</span>'
          // 表示番号を出力する
          .'<span class="number">'.$chat_direct_comment['DIRECT_CHAT_EXCHANGE_DISPLAY_NO'].'</span>'
          .'<span class="createDate">'.$chat_direct_comment['DIRECT_CHAT_EXCHANGE_INS_TS'].'</span>'
          .'</a>'
        ;
      //いいねと件数を表示する
      echo '<span class="good">'
          .'<span data-url="/common/good/goodAjax" data-mode="SIMPLE" data-use-comet="TRUE">'
          .'<a href="javascript:void(0);" class="iconLink goodLinkOn" id="cbaGood-MP_CHAT-2:15547680-17601719-on" data-url="/comet/common/good/goodTurnJsonDirect">'
          .'<img src="https://cybozulive.com/static/49f5cdb4ca/images/good14.png" alt="" class="icon" />'
          .'<span>いいね！</span>'
          .'</a>'
          .'</span>'
          .'<span class="goodMemberCountParts" data-url="/common/good/goodMemberListAjax" style="display:none">'
          .'<a href="javascript:void(0);" class="goodMemberCount goodMemberListTip" data-url="/common/good/goodMemberListTip?application=MP_CHAT&amp;targetId=2%3A15547680&amp;subTargetId=17601719">'
          .date('Y年m月d日 H時i分s秒', strtotime($chat_direct_comment['DIRECT_CHAT_EXCHANGE_INS_TS']))
          .'</a>'
          .'<span>件</span>'
          .'</span>'
          .'</span>'
        ;
      //返信とお気に入りボタンなどを表示
      echo '<a href="javascript:void(0);" id="chatRoomComment_28" class="replyChatRoomComment iconLink">'
          .'<img src="https://cybozulive.com/static/49f5cdb4ca/images/reply14.png" alt="" class="icon" />'
          .'<span>返信</span>'
          .'</a>'
          .'<a href="javascript:void(0);" data-url="/common/star/starTurnJsonDirect" id="cbaStar-MP_CHAT--2:15547680-17601719" class="star iconLink" data-displayLabel="true">'
          .'<img src="https://cybozulive.com/static/49f5cdb4ca/images/star_off14.png" class="icon" alt="お気に入りに登録する" data-star="off" />'
          .'<span class="starLabel">お気に入り</span>'
          .'</a>'
          .'</div>'
          .'</div>'
          .'</div>'
          .'</div>'
          .'</div>'
          .'</div>'
        ;


      // echo $con->selectNo($direct_comment['DIRECT_CHAT_EXCHANGE_CREATE_MEMBER_NO'])['member_name']
      //     .' '.date('Y年m月d日 H時i分s秒', strtotime($direct_comment['DIRECT_CHAT_EXCHANGE_INS_TS']))
      //     .'</p>'
      //     .'<p id="comment_text">'
      //     //返信した時の返信に関するリンクをつける
      //     .$reply_link
      //     .$direct_comment['DIRECT_CHAT_EXCHANGE_COMMENT_TEXT']
      //     .'</p>'
      //     .'<a href="#reply_to" id="reply_comment" class="reply_to_'.$direct_comment['DIRECT_CHAT_EXCHANGE_DISPLAY_NO'].'" >返信する</a>'
      // ;
      //ログインユーザーに更新権限がある場合、削除ボタンの表示
      if($account->chkUpdatePermission($chat_direct_comment['DIRECT_CHAT_EXCHANGE_CREATE_MEMBER_NO'],null)){
        echo '<a href="#delete" name="delete_chat_direct_comment" class="delete_comment_'.$chat_direct_comment['DIRECT_CHAT_EXCHANGE_NO'].'" >削除する</a>'
        ;
      }
  }
}
$chat = new Chat;
//データベースとの接続
$con = new Db;
//ログインユーザーの表示
$account = new Account;
//ダイレクトチャット用のオブジェクトを作成する
$chat_direct = new ChatDirect;

//ダイレクトチャット画面でグループチャット番号で検索をかけるためにグループチャット番号を取得する
$group_chat_no=filter_input(INPUT_POST,"group_chat_no");
$chat_direct_create=filter_input(INPUT_POST,"direct_chat_create");
$GROUP_CHAT_EXCHANGE_COMMENT_TEXT=filter_input(INPUT_POST,"GROUP_CHAT_EXCHANGE_COMMENT_TEXT");
##########################################
// ダイレクトチャットで必要になる機能
// 送信ボタンを押した時に投稿内容が登録される
// 空で送信ボタンを押すと登録の処理が行われない
// 自分で作成した投稿に関しては、削除することが可能
// 投稿内容に関していいね、お気に入り登録、返信などをすることができる。(自分の投稿や相手の投稿に関わらず、つけれる)
##########################################

if(!is_null($chat_direct_create)){
  //ボタンが押された時の処理
  if(!is_null($GROUP_CHAT_EXCHANGE_COMMENT_TEXT) and $GROUP_CHAT_EXCHANGE_COMMENT_TEXT!==""){
    //データを登録した時に新たにダイレクトチャットのリストを表示する
    $chat->setDirectChatExchange($group_chat_no,$GROUP_CHAT_EXCHANGE_COMMENT_TEXT,$reply_to_dairect_chat_comment_no);
    //更新した値を取得する
    // //やり取りしている内容を全件取得する
    // $chat_direct_comments = $chat->getAllDirectChatExchange($group_chat_no);
    // $chat_direct_comments = $chat->getAllDirectChatExchange($group_chat_no);
    $chat_direct_comment = $chat->getlatestDirectChatExchange($group_chat_no);
    // $chat_direct->commnet_list($chat_direct_comments);
    $chat_direct->latest_commnet_list($chat_direct_comment);
  }else{
    //コメントが入力されていなければ何も処理されない
  }
}else{
  //初期表示でやり取りしていなかったら表示しない。
}

//コメント削除の時
if(!empty($_POST['delete_chat_direct_comment'])){
    $chat->deleteDirectChatExchangeNo($_POST['DIRECT_CHAT_EXCHANGE_NO']);
}
