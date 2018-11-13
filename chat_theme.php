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

//複数画像のアップロード
function normalize_files_array($files = []){
  $normalize_array = [];
  foreach($files as $index => $file) {
    if(!is_array($file['name'])) {
      $normalize_array[$index][] = $file;
      continue;
    }
    //形式を並び替え
    foreach($file['name'] as $idx => $name) {
      $normalize_array[$index][$idx] = [
        'name' => $name,
        'type' => $file['type'][$idx],
        'tmp_name' => $file['tmp_name'][$idx],
        'error' => $file['error'][$idx],
        'size' => $file['size'][$idx],
      ];
    }
  }
  return $normalize_files_array;
}
//テーマチャット作成ボタンが押される前にファイルがアップロードされることは、ないのでファイルがアップロードされたら、セッションに入れておく
//上書きをした場合は、書き換わる
// for($i=0;$i<count($_FILES['file']);$i++){
//   if(empty($_SESSION['upfile']) || $_FILES['file']['error'][$i] === 0) {
//     unset($_SESSION['upfile']);
//     $_SESSION['upfile'] = $_FILES['file'];
//     logger('セッションの値を確認する'.$_SESSION['upfile']);
//   }
// }
//ファイルがアップロードされていた時
if(!empty($_FILES)){
  //アップロードするディレクトリを指定する
  foreach ($_FILES["file"]["error"] as $key => $error) {
      // unset($_SESSION['upfile'],$_SESSION['filelist']);
      if ($error == UPLOAD_ERR_OK) {
          $tmp_name = $_FILES["file"]["tmp_name"][$key];
          if(!$tmp_name){continue;}
          // basename() で、ひとまずファイルシステムトラバーサル攻撃は防げるでしょう。
          // ファイル名についてのその他のバリデーションも、適切に行いましょう。
          // $name = basename($_FILES["file"]["name"][$key]);
          $name = $_FILES["file"]["name"][$key];
          $_SESSION['upfile']['name'][$key] = $name;
          $_SESSION['upfile']['error'][$key] = $_FILES["file"]["error"][$key];
          // $file_list[] = preg_split("/[.]/",$_SESSION['upfile'][$key]);
          // $_SESSION['filelist'] = $file_list;
                  //imgのディレクトリを書き込みが出来るようにその他のユーザーにも書き込み権限を与える
        system('sudo chmod 0777 '.$_SERVER['DOCUMENT_ROOT'].'view/img');
        //ディレクトリが作成されていない場合、新規で作成する
        if(!file_exists(THEME_IMAGE)){
          mkdir(THEME_IMAGE,0777);
          chmod(THEME_IMAGE,0777);
        }
        system('sudo chmod 0777 '.THEME_IMAGE);
        //ディレクトリが作成されていない場合、新規で作成する
        if(!file_exists(THEME_IMAGE_TMP)){
          mkdir(THEME_IMAGE_TMP,0777);
          chmod(THEME_IMAGE_TMP,0777);
        }
        system('sudo chmod 0777 '.THEME_IMAGE_TMP);
          move_uploaded_file($tmp_name, THEME_IMAGE_TMP);
      }
  }
}

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

  //グループチャット作成登録画面に遷移してきた時
  if($_GET['edit_type'] == 'register'){
    //エラーがなければ登録処理を行う
    if(empty($_POST['err'])){
      $chat->setGroupChat($_POST['GROUP_GHAT_NO'],$_POST['GROUP_CHAT_TITLE'],$_POST['GROUP_CHAT_MEMO'],$_SESSION['file'],$_POST['modifiable'],$_SESSION['belong_member']);
      //チャットの一覧ページ(ダイレクトページとテーマチャットが一覧で見れるページ)メッセージは、
      $_SESSION['edit_topic_msg'] = 'トピックが'.$chat->getGroupChatEditType($_GET['edit_type']).'されました';
      //登録がうまく行った場合は、ページ遷移させずに表示させていたセレクトボックスの値などのデータをセッションに入れていたものをクリアする
      unset($_SESSION['belong_member']);
      header("Location:chat_top");
    }else{
      //エラーがある時は、セッションの値をクリア
      unset($_SESSION['upfile']);
    }
  }

}
//テーマチャットキャンセル押下時
if(!empty($_POST['chat_theme_cancel'])){
  //メンバー一覧の項目とアップロード画像はクリアする
  unset($_SESSION['belong_member'],$_SESSION['upfile']);
  header("Location:chat_top");
}
// 画面表示
Html::header($access_device);
Html::body(__FILE__, $access_device);
