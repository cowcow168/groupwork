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

  // TODO 20181024 追加と削除の時のセレクトボックスの表示と作成した時の動きに問題があるので、見直す
  //バリデーションエラー時に入力画面で表示させる為にセッションに入れておく
  //所属メンバーのリスト
  if($_SESSION['belong_member']){
    //バリデーションエラーにひっかかって後にさらに追加した場合
    $_SESSION['belong_member'] = array_merge($_SESSION['belong_member'],(array)$_REQUEST['addressId']);
    //セッションがあってリクエストがない時(誰も追加されずに作成ボタンを押された時)は、セッションの値を入れる
  }else{
    //バリデーションエラー前に初回追加した時(複数登録されることを想定して配列にしておく)
    $_SESSION['belong_member'] = (array)$_REQUEST['addressId'];
  }
  // elseif(!$_REQUEST['addressId']){
  //   //追加されない状態でもう一度作成ボタンを押した際や二重でクリックした時の対策
  //   $_SESSION['belong_member'] = $_SESSION['belong_member'];
  // }
  //所属していないメンバーのリスト
  if($_SESSION['choice_member']){
    //バリデーションエラーにひっかかって後にさらに追加した場合
    $_SESSION['choice_member'] = array_merge($_SESSION['choice_member'],$_REQUEST['choiceList']);
  }else{
    //バリデーションエラー前に初回追加した時
    $_SESSION['choice_member'] = $_REQUEST['choiceList'];
  }

  //TODO 　画像のファイルサイズを指定する(html側で指定してあげる)
  // TODO 画像の時は、画像をテーマチャット画面で表示して、.txtの時は、クリックするとテキストの内容が表示されて、csvファイルの時は、押すと出力される
  //ファイルがアップロードされたかの判定が必要
  if(isset($_FILES) && isset($_FILES['file']) && is_upload_file($_FILES['file']['tmp_name'])){
    //ディレクトリ確認
    if(!file_exists(THEME_IMAGE)){
      system('sudo chmod 0777 '.$_SERVER['DOCUMENT_ROOT'].DOCUMENT_ROOT.'view/img');
      //なければ作成する
      mkdir('view/img/themeimg');
      system('sudo chmod 0777 '.$_SERVER['DOCUMENT_ROOT'].'view/img/themeimg');
    }
    //permisiion変更
    system('sudo chmod 0777 '.$_SERVER['DOCUMENT_ROOT'].'view/img');
    system('sudo chmod 0777 '.$_SERVER['DOCUMENT_ROOT'].'view/img/themeimg');

    // TODO ファイル名が被らないように考える必要がある　サーバーのアップロード先
    $url = 'view/img/themeimg/'.basename($_FILES['file']['name']);

    // tmpから保存先に移動
    if(move_uploaded_file($_FILES['upload']['tmp_name'],$url)){
      $msg = $url. 'のアップロードに成功しました';
    }else{
      $msg = 'アップロードに失敗しました';
    }
    // permission戻す
    system('sudo chmod 0775 '.$_SERVER['DOCUMENT_ROOT'].'view/upload');
    system('sudo chmod 0775 '.$_SERVER['DOCUMENT_ROOT'].'view/upload/img');

    echo $msg;
    // ログに残す
    logger($msg);

    // system('sudo chmod 644 '.$_SERVER['DOCUMENT_ROOT'].'view/upload');
    // system('sudo chmod 644 '.$_SERVER['DOCUMENT_ROOT'].'view/upload/img');
  }else{
    echo 'error';
  }

  //グループチャット作成登録画面に遷移してきた時
  if($_GET['edit_type'] == 'register'){
    //エラーがなければ登録処理を行う
    if(empty($_POST['err'])){
      $chat->setGroupChat($_POST['GROUP_GHAT_NO'],$_POST['GROUP_CHAT_TITLE'],$_POST['GROUP_CHAT_MEMO'],$_FILE['file']['tmp_name'],$_POST['modifiable'],$_SESSION['belong_member']);
      //チャットの一覧ページ(ダイレクトページとテーマチャットが一覧で見れるページ)メッセージは、
      $_SESSION['edit_topic_msg'] = 'トピックが'.$chat->getGroupChatEditType($_GET['edit_type']).'されました';
      //登録がうまく行った場合は、ページ遷移させずに表示させていたセレクトボックスの値などのデータをセッションに入れていたものをクリアする
      unset($_SESSION['belong_member']);
      header("Location:chat_top");
    }
  }

}
//テーマチャットキャンセル押下時
if(!empty($_POST['chat_theme_cancel'])){
  //メンバー一覧の項目はクリアする
  unset($_SESSION['belong_member']);
  header("Location:chat_top");
}
// 画面表示
Html::header($access_device);
Html::body(__FILE__, $access_device);
