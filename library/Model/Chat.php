<?php
//2018/08/15
/**
* Class Chat
*
*チャットに関するクラス
*
*/
class Chat extends Db
{
  //テーマチャットタイトル文字数の上限
  const CHAT_TITLE_MAX = 30;
  //テーマチャット本文の文字数の上限
  const CHAT_TEXT_MAX = 10000;
  // ダイレクトチャットの本文表示文字数
  const DISPLAY_TEXT_NUM = 20;

#===============================================
// グループチャット一覧画面表示部分
#===============================================
  /**
   * グループチャットNoから全グループチャット(テーマチャット・個人チャットも含む)を取得
   *
   * @param $groupChatNo 検索するグループチャットNo
   *
   * @return array 検索結果のグループチャット全レコード
   */
  public function getAllToChat($groupChatNo = null)
  {
      //ない時は、表示させない
      $and_where = (!empty($groupChatNo) ? ' AND GROUP_CHAT_NO = :groupChatNo ' : null);

      $sql  = ' SELECT * FROM TO_CHAT '
          .' WHERE TO_CHAT_STATUS = 1 '
          .$and_where
          .' ORDER BY TO_CHAT_UPD_TS DESC '
      ;

      $con = new Db;
      $con->connect();
      $stmt = $con->dbh->prepare($sql);
      $stmt->bindValue(':groupChatNo', $groupChatNo);
      $stmt->execute();

      return $stmt->fetchAll();
  }

  /**
   * 全グループチャット取得
   * （デフォルトチャットのその他は末尾、
   * 更新日時、あるいは作成日時の新しい順に）
   *
   * @return array 検索結果のグループチャット(主にテーマチャットの部分)全レコード
   */
  public function getAllGroupChat()
  {
      $sql  = ' SELECT * FROM GROUP_CHAT '
          .' WHERE GROUP_CHAT_STATUS = 1 '
          .' ORDER BY '
          .' CASE WHEN GROUP_CHAT_UPD_TS IS NOT NULL '
          .' THEN GROUP_CHAT_UPD_TS '
          .' ELSE GROUP_CHAT_INS_TS END '
          .' DESC '
      ;

      $con = new Db;
      $con->connect();
      $stmt = $con->dbh->prepare($sql);
      $stmt->execute();
      return $stmt->fetchAll();
  }

  #===============================================
  // グループチャット作成部分
  #===============================================
  /**
   * グループチャット名検索(テーマチャットを登録する際の処理に必要)
   *
   * @param $groupChatTitle 検索するグループチャット名
   *
   * @return array 検索結果のグループチャット1レコード
   */
  public function getGroupChatByName($groupChatTitle)
  {
      $sql  = ' SELECT * FROM GROUP_CHAT '
          .' WHERE GROUP_CHAT_TITLE = :groupChatTitle '
          .' AND GROUP_CHAT_STATUS = 1 '
      ;

      $con = new Db;
      $con->connect();
      $stmt = $con->dbh->prepare($sql);
      $stmt->bindValue(':groupChatTitle', $groupChatTitle);
      $stmt->execute();

      return $stmt->fetch();
  }

  ###########グループチャット作成時に所属メンバー以外のメンバーを抽出する

  /**
  * グループチャット作成画面で所属メンバーでないメンバーを取得する
  * @param array $memberNoList 自分の社員番号以外を取得する
  *
  * @return array 社員番号のリストを返す(週報システムで使用している写真と名前を取得する)
  */
  public function notBelongMember($memberNoList)
  {
    //所属していないメンバーを検索する
    $sql  = ' SELECT PERMISSION_MEMBER_NO FROM PERMISSION '
        .' WHERE PERMISSION_MEMBER_NO IN(:permissionMemberNo) '
        .' AND PERMISSION_STATUS = 1 '
    ;

    $con = new Db;
    $con->connect();
    $stmt = $con->dbh->prepare($sql);
    //所属メンバー以外のデータを渡してあげる
    foreach($memberNoList as $memberNo) {
      $stmt->bindParam(':permissionMemberNo', $memberNo);
      $stmt->execute();
    }

    return $stmt->fetchAll();
  }

/**
  * グループチャット作成
  * グループチャットテキストは、必須、その他は、必須でないのでデフォルトは、null定義
  *
  * @param $groupChatNo グループチャットNo
  * @param $groupChatTitle グループチャットタイトル名
  * @param $groupChatMemo グループチャットメモ(グループの詳細の説明などを記載する所)
  * @param array $groupChatAttachedFile グループチャットでの添付画像
  * @param boolean $modifiable 作成者以外の所属メンバーがグループチャットを更新できるかのフラグ
  * @param array $belong_member グループに所属しているメンバー
  *
  * @return array
  * TODO $groupChatNoが来る時は、空でinsertされたタイミングでインクリメントされるので、トランザクションつけるか?
  */
  public function setGroupChat($groupChatNo, $groupChatTitle,$groupChatMemo=null,$groupChatAttachedFile=null,$modifiable=null,$belong_member=null)
  {
      $con = new Db;
      $con->connect();
      // 複数テーブル登録・更新のため、現在時刻を変数化
      $now = getNow();
      //所属メンバーのデータを格納する
      $belong_member = array();
      $belong_member = $_POST['addressId'];
      // チャットの登録
      $sql  = ' INSERT INTO GROUP_CHAT ( '
        .' GROUP_CHAT_NO, '
        .' GROUP_CHAT_TITLE, '
        .' GROUP_CHAT_MEMO, '
        .' GROUP_CHAT_CREATE_MEMBER_NO, '
        .' GROUP_CHAT_UPDATE_MEMBER_NO, '
        .' GROUP_CHAT_INS_TS, '
        .' GROUP_CHAT_UPD_TS, '
        .' GROUP_CHAT_STATUS) '
        .' VALUES '
        .' (:groupChatNo,'
        .' :groupChatTitle, '
        .' :groupChatMemo, '
        .' :groupChatCreateMemberNo, '
        .' :groupChatUpdateMemberNo, '
        .' :groupChatInsTs, '
        .' :groupChatUpdTs, '
        .' :groupChatStatus) '
        ;
        //グループチャットメモと添付ファイルの登録処理できるようにする
        // TODO 添付ファイルの登録処理とグループチャット
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':groupChatNo', $groupChatNo);
        $stmt->bindValue(':groupChatTitle', $groupChatTitle);
        $stmt->bindValue(':groupChatMemo', $groupChatMemo);
        $stmt->bindValue(':groupChatCreateMemberNo', $_SESSION['member_no']);
        $stmt->bindValue(':groupChatUpdateMemberNo', $_SESSION['member_no']);

        $stmt->bindValue(':groupChatInsTs', $now);
        $stmt->bindValue(':groupChatUpdTs', $now);

        //デフォルトでは、表示させるようにする
        $stmt->bindValue(':groupChatStatus', 1);
        $stmt->execute();

        //上記で登録されたID(チャット全体のテーブルに紐付かせるグループチャット番号)を取得する
        $tmpChatNo = $con->dbh->lastInsertId('GROUP_CHAT_NO');
        //所属メンバーは、必須では、ないので空でない時の判定を入れる
        if(!empty($belong_member)){
          //所属メンバーが複数の時もあるのでその時は、同じグループチャット番号で所属メンバーテーブルに複数レコード作る
          foreach($belong_member as $belong_member_no){
            $this->setGroupChatBelongs($tmpChatNo,$belong_member_no,$modifiable);
          }
        }
        //グループチャット登録時パスを指定してあげる(登録する時のjavaScriptのファイル名をグループチャットの後ろにつける)
        //グループチャット番号が、100005で4枚画像があれば、1000051 1000052 1000053 1000054などのように登録する
        //グループチャット番号が、100005で100枚画像があれば、100005100 が最後の登録になるようにする
        //画像のパスを指定する
        $fileName = $tmpChatNo;
        $filepath = THEME_IMAGE.'/'.$fileName;

        //imgのディレクトリを書き込みが出来るようにその他のユーザーにも書き込み権限を与える
        system('sudo chmod 0777 '.$_SERVER['DOCUMENT_ROOT'].'view/img');
        //ディレクトリが作成されていない場合、新規で作成する
        if(!file_exists(THEME_IMAGE)){
          mkdir(THEME_IMAGE,0777);
          chmod(THEME_IMAGE,0777);
        }
        //グループチャット番号のディレクトリを作成する
        mkdir($filepath,0777,true);
        chmod($filepath,0777);

        logger('ファイルの中身を確認する'.print_r($_SESSION['upfile']));
        // logger('リクエストで来た値を確認する'.$groupChatAttachedFile);
        //ファイルのアップロードがある時
        if(count($_SESSION['upfile'])>0){
          //配列として画像データを取得してあげて、エラーがある時は、飛ばして、問題ない時は、指定した所にファイルを移動してあげる
          foreach($_SESSION['upfile'] as $key => $tmp_name){
            if($_SESSION['upfile']['error'][$key] > 0){continue;}
            //名前の被りを防いで登録するために、拡張子とファイル名の所を分割する
            $file_list = explode("/[.]/",$_SESSION['upfile']['name'][$key]);
            logger('ファイルの分割状況を確認'.$file_list);
            // //実際にあげられたファイル名で拡張子を除く(ダブリをなくす為に上書きしてあげる必要あり)
            // $tmp_file_name = $file_list[0];
            //グループチャット番号の後にアップロードした枚数だけを連番にして、名前を上書き(拡張子は、そのままつける)
            $tmp_name = $tmpChatNo($key+1).".".$file_list[1];
            logger('ファイルの名前を確認'.$tmp_name);
            //TODO 同じ名前にならないようにする必要がある
            $groupChatAttachedFilePath = THEME_IMAGE.'/'.$tmp_name;
            logger('格納されるファイルのフルパスを確認'.$groupChatAttachedFilePath);
            move_uploaded_file($tmp_name,THEME_IMAGE);
            //画像がある時は、登録する
            if(file_exists($groupChatAttachedFilePath)){
              $this->setGroupChatAttachedFile($tmpChatNo,$groupChatAttachedFilePath);
            }
          }
        }
        //書き込み後は、権限を元に戻す
        system('sudo chmod 0775 '.$_SERVER['DOCUMENT_ROOT'].'view/img');
        //新規判定用
        $sql  = ' SELECT * FROM TO_CHAT '
        .' WHERE GROUP_CHAT_NO = :groupChatNo '
        ;
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':groupChatNo', $tmpChatNo);
        $stmt->execute();
        $data = $stmt->fetch();
        // グループチャット新規作成時は、チャットテーブルにないので紐づけの為登録する
        if(empty($data['GROUP_CHAT_NO'])){
          $this->setToChatNo($tmpChatNo);
        }
        //ある時で、変更がある時は、更新でない時は、時間だけ更新する
        // グループチャットの更新(動きを確認して検証する)
        // $sql  = ' UPDATE BOARD_CATEGORY SET '
        //     .' BOARD_TOPIC_NUM = :groupChatNum, '
        //     .' BOARD_CATEGORY_UPDATE_MEMBER_NO = :boardCategoryUpdateMemberNo, '
        //     .' BOARD_CATEGORY_UPD_TS = :boardCategoryUpdTs '
        //     .' WHERE BOARD_CATEGORY_NO = :boardCategoryNo'
        // ;
        // $stmt = $con->dbh->prepare($sql);
        // $stmt->bindValue(':groupChatNum', count($this->getAllBoardTopic($boardCategoryNo)));
        // $stmt->bindValue(':boardCategoryUpdateMemberNo', $_SESSION['member_no']);
        // $stmt->bindValue(':boardCategoryUpdTs', $now);
        // $stmt->bindValue(':boardCategoryNo', $boardCategoryNo);

        // return $stmt->execute();
  }

  /**
   * テーマチャットの複数画像データ登録
   * 編集時に更新できて、画像データ数を更新できるようにする(呼び元で配列で渡してあげる)
   * @param $groupChatNo 登録するテーマチャットに紐づくグループチャット番号
   *
   * @return array 登録用データ
   */
   public function setGroupChatAttachedFile($groupChatNo,$groupChatAttachedFilePath=null)
   {
     $con = new Db;
     $con->connect();

     // 複数テーブル登録・更新のため、現在時刻を変数化
     $now = getNow();
     // チャットの登録
     $sql  = ' INSERT INTO GROUP_CHAT_ATTACHED_FILE ( '
         .' GROUP_CHAT_ATTACHED_FILE_NO, '
         .' GROUP_CHAT_NO, '
         .' GROUP_CHAT_ATTACHED_FILE, '
         .' GROUP_CHAT_ATTACHED_FILE_INS_TS, '
         .' GROUP_CHAT_ATTACHED_FILE_UPD_TS, '
         .' GROUP_CHAT_ATTACHED_FILE_STATUS) '
         .' VALUES '
         .' (:groupChatAttachedFileNo,'
         .' :groupChatNo,'
         .' :groupChatAttachedFile, '
         .' :groupChatAttachedFileInsTs, '
         .' :groupChatAttachedFileUpdTs, '
         .' :groupChatAttachedFileStatus) '
     ;
     $stmt = $con->dbh->prepare($sql);
     // TODO グループチャット番号が入ってこない
     $stmt->bindValue(':groupChatAttachedFileNo', '');
     $stmt->bindValue(':groupChatNo', $groupChatNo);
     $stmt->bindValue(':groupChatAttachedFile', $groupChatAttachedFilePath);
     $stmt->bindValue(':groupChatAttachedFileInsTs', $now);
     $stmt->bindValue(':groupChatAttachedFileUpdTs', $now);
     //デフォルトでは、表示させるようにする(削除した際は、0に更新してあげる)
     $stmt->bindValue(':groupChatAttachedFileStatus', 1);
     $stmt->execute();
   }

   /**
    * テーマチャットの複数画像データ登録
    * 編集時に更新できて、画像データ数を更新できるようにする(呼び元で配列で渡してあげる)
    * @param $groupChatNo 登録するテーマチャットに紐づくグループチャット番号
    * @param string $groupChatBelongsMemberNo グループに所属しているメンバー
    * @param boolean $groupChatUpdatePermissionStatus 作成者以外の所属メンバーがグループチャットを更新できるかのフラグ
    *
    * @return array 登録用データ
    */
    public function setGroupChatBelongs($groupChatNo,$groupChatBelongsMemberNo=null,$groupChatUpdatePermissionStatus=null)
    {
      $con = new Db;
      $con->connect();

      // 複数テーブル登録・更新のため、現在時刻を変数化
      $now = getNow();
      // チャットの登録
      $sql  = ' INSERT INTO GROUP_CHAT_BELONG ( '
          .' GROUP_CHAT_BELONG_NO, '
          .' GROUP_CHAT_NO, '
          .' GROUP_CHAT_BELONGS_MEMBER_NO, '
          .' GROUP_CHAT_BELONG_INS_TS, '
          .' GROUP_CHAT_BELONG_UPD_TS, '
          .' GROUP_CHAT_UPDATE_PERMISSION_STATUS) '
          .' VALUES '
          .' (:groupChatBelongsNo,'
          .' :groupChatNo,'
          .' :groupChatBelongsMemberNo, '
          .' :groupChatBelongsInsTs, '
          .' :groupChatBelongsUpdTs, '
          .' :groupChatUpdatePermissionStatus) '
      ;
      $stmt = $con->dbh->prepare($sql);
      // TODO グループチャット番号が入ってこない
      $stmt->bindValue(':groupChatBelongsNo', '');
      $stmt->bindValue(':groupChatNo', $groupChatNo);
      $stmt->bindValue(':groupChatBelongsMemberNo', $groupChatBelongsMemberNo);
      $stmt->bindValue(':groupChatBelongsInsTs', $now);
      $stmt->bindValue(':groupChatBelongsUpdTs', $now);
      //作成者以外でも更新できるにチェックを入れていた時に1にする(所属しているメンバーの方)
      if($groupChatUpdatePermissionStatus){
        $stmt->bindValue(':groupChatUpdatePermissionStatus', 1);
      }else{
        $stmt->bindValue(':groupChatUpdatePermissionStatus', 0);
      }
      $stmt->execute();
    }
  /**
   * チャット(グループチャット・ダイレクトチャット)登録
   * @param $groupChatNo 登録するテーマチャットのグループチャットNo
   *
   * @return array 登録用データ
   */
   public function setToChatNo(&$groupChatNo){
     $con = new Db;
     $con->connect();

     // 複数テーブル登録・更新のため、現在時刻を変数化
     $now = getNow();
     // チャットの登録
     $sql  = ' INSERT INTO TO_CHAT ( '
         .' TO_CHAT_NO, '
         .' GROUP_CHAT_NO, '
         .' TO_CHAT_TEXT, '
         .' TO_CHAT_ATTACHED_FILE, '
         .' TO_CHAT_CREATE_MEMBER_NO, '
         .' TO_CHAT_UPDATE_MEMBER_NO, '
         .' TO_CHAT_COMMENT_GOOD_NUM, '
         .' TO_CHAT_COMMENT_FAVORITE_NUM, '
         .' TO_CHAT_PARENT_REPLY_COMMENT_NO, '
         .' TO_CHAT_REPLY_COMMENT_NO, '
         .' TO_CHAT_INS_TS, '
         .' TO_CHAT_UPD_TS, '
         .' TO_CHAT_STATUS, '
         .' TO_CHAT_ADD_FAVORITE_STATUS) '
         .' VALUES '
         .' (:toChatNo,'
         .' :groupChatNo,'
         .' :toChatCommentText, '
         .' :toChatAttachedFile, '
         .' :toChatCreateMemberNo, '
         .' :toChatUpdateMemberNo, '
         .' :toChatCommentGoodNum, '
         .' :toChatCommentFavoriteNum, '
         .' :toChatParentReplyCommentNo, '
         .' :toChatReplyCommentNo, '
         .' :toChatInsTs, '
         .' :topChatUpdTs, '
         .' :toChatStatus, '
         .' :toChatAddFavoriteStatus) '
     ;
     $stmt = $con->dbh->prepare($sql);
     // TODO グループチャット番号が入ってこない
     $stmt->bindValue(':toChatNo', '');
     $stmt->bindValue(':groupChatNo', $groupChatNo);
     //やり取りする前は、存在しないのでデフォルトは、NULLで作成する
     $stmt->bindValue(':toChatCommentText', null);
     $stmt->bindValue(':toChatAttachedFile', null);
     $stmt->bindValue(':toChatCreateMemberNo', $_SESSION['member_no']);
     $stmt->bindValue(':toChatUpdateMemberNo', $_SESSION['member_no']);
     //グループチャット登録時は、NULLで良い項目
     $stmt->bindValue(':toChatCommentGoodNum', null);
     $stmt->bindValue(':toChatCommentFavoriteNum', null);
     $stmt->bindValue(':toChatParentReplyCommentNo', null);
     $stmt->bindValue(':toChatReplyCommentNo', null);
     $stmt->bindValue(':toChatInsTs', $now);
     $stmt->bindValue(':topChatUpdTs', $now);
     //デフォルトでは、表示させるようにする
     $stmt->bindValue(':toChatStatus', 1);
     $stmt->bindValue(':toChatAddFavoriteStatus', 1);
     $stmt->execute();
   }

  /**
   * グループチャット編集
   *
   * @param $groupChatNo 編集するグループチャットNo
   * @param $groupChatTitle 編集後のグループチャット名
   *
   * @return bool
   */
  public function updateGroupChat($groupChatNo, $groupChatTitle)
  {
      $sql  = ' UPDATE GROUP_CHAT SET '
          .' GROUP_CHAT_UPDATE_MEMBER_NO = :groupChatUpdateMemberNo, '
          .' GROUP_CHAT_UPD_TS = :groupChatUpdTs, '
          .' GROUP_CHAT_TITLE =  :groupChatTitle'
          .' WHERE GROUP_CHAT_NO = :groupChatNo'
      ;

      $con = new Db;
      $con->connect();
      $stmt = $con->dbh->prepare($sql);
      $stmt->bindValue(':groupChatUpdateMemberNo', $_SESSION['member_no']);
      $stmt->bindValue(':groupChatUpdTs', getNow());
      $stmt->bindValue(':groupChatTitle', $groupChatTitle);
      $stmt->bindValue(':groupChatNo', $groupChatNo);

      return $stmt->execute();
  }

  /**
   * グループチャット削除
   *
   * @param $groupChatNo 削除するグループチャットNo
   *
   * @return bool
   */
  public function deleteGroupChat($groupChatNo)
  {
      $con = new Db;
      $con->connect();

      // 複数テーブル登録・更新のため、現在時刻を変数化
      $now = getNow();

      // カテゴリの論理削除
      $sql  = ' UPDATE GROUP_CHAT SET '
          .' GROUP_CHAT_UPDATE_MEMBER_NO = :groupChatUpdateMemberNo, '
          .' GROUP_CHAT_UPD_TS = :groupChatUpdTs, '
          .' GROUP_CHAT_STATUS = 0 '
          .' WHERE GROUP_CHAT_NO = :groupChatNo'
      ;

      $stmt = $con->dbh->prepare($sql);
      $stmt->bindValue(':groupChatUpdateMemberNo', $_SESSION['member_no']);
      $stmt->bindValue(':groupChatUpdTs', $now);
      $stmt->bindValue(':groupChatNo', $groupChatNo);
      $stmt->execute();

      // グループNoに紐づくすべてのチャットを論理削除
      $sql  = ' UPDATE TO_CHAT SET '
          .' TO_CHAT_UPDATE_MEMBER_NO = :toChatUpdateMemberNo, '
          .' TO_CHAT_UPD_TS = :toChatUpdTs, '
          .' TO_CHAT_STATUS = 0 '
          .' WHERE GROUP_GHAT_NO = :groupChatNo'
      ;

      $stmt = $con->dbh->prepare($sql);
      $stmt->bindValue(':toChatUpdateMemberNo', $_SESSION['member_no']);
      $stmt->bindValue(':toChatUpdTs', $now);
      $stmt->bindValue(':groupChatNo', $groupChatNo);

      return $stmt->execute();
  }

  /**
   * グループチャット名検索(同じグループチャット名で違うものかの削除時の判定で使用する)
   *
   * @param $groupChatName 検索するグループチャット名
   *
   * @return array 検索結果のグループチャット1レコード
   */
  public function getBoardCategoryByName($groupChatName)
  {
      $sql  = ' SELECT * FROM GROUP_CHAT '
          .' WHERE GROUP_CHAT_TITLE = :groupChatName '
          .' AND GROUP_CHAT_STATUS = 1 '
      ;

      $con = new Db;
      $con->connect();
      $stmt = $con->dbh->prepare($sql);
      $stmt->bindValue(':groupChatName', $groupChatName);
      $stmt->execute();

      return $stmt->fetch();
  }

    /**
     * グループチャットNOからトピック検索
     *
     * @param $groupChatNo 検索するチャットNo
     *
     * @return array 検索結果のグループチャット1レコード
     */
    public function getGroupChat($groupChatNo)
    {
        $sql  = ' SELECT * FROM GROUP_CHAT '
            .' WHERE GROUP_CHAT_NO = :groupChatNo '
            .' AND GROUP_CHAT_STATUS = 1 '
        ;

        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':groupChatNo', $groupChatNo);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * グループチャットNOからトピック検索(テーマチャット編集時)
     *
     * @param $groupChatNo 検索するチャットNo
     *
     * @return array 検索結果のグループチャット1レコード
     */
    public function getGroupChatTheme($groupChatNo)
    {
        $sql  = ' SELECT * FROM GROUP_CHAT '
            .' WHERE GROUP_CHAT_NO = :groupChatNo '
            .' AND GROUP_CHAT_STATUS = 1 '
        ;

        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':groupChatNo', $groupChatNo);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


//グループチャットと紐付いているTo_chatテーブルの更新や新規作成などをする(画面上は、削除)
    // /**
    //  * トピック編集
    //  *
    //  * @param $boardTopicNo 編集するトピックNo
    //  * @param $boardCategoryNo 編集後カテゴリNo
    //  * @param $boardTopicTitle 編集後トピック名
    //  * @param $boardTopicText 編集後トピック本文
    //  *
    //  * @return bool
    //  */
    // public function updateBoardTopic($boardTopicNo, $newBoardCategoryNo, $boardTopicTitle = null, $boardTopicText = null)
    // {
    //     $oldBoardCategoryNo = $this->getBoardTopic($boardTopicNo)['BOARD_CATEGORY_NO'];
    //
    //     // カテゴリの変更があった際にフラグをtrueで保持する
    //     $changeCategoryFlg = ($oldBoardCategoryNo != $newBoardCategoryNo ? true : false);
    //
    //     $changeBoardTopicStmtFlg = false;
    //     $changeBoardTopicStmtSql = '';
    //
    //     if(!empty($boardTopicTitle) && !empty($boardTopicText)){
    //         $changeBoardTopicStmtFlg = true;
    //         $changeBoardTopicStmtSql = ' BOARD_TOPIC_TITLE = :boardTopicTitle, BOARD_TOPIC_TEXT = :boardTopicText, ';
    //     }
    //
    //     $con = new Db;
    //     $con->connect();
    //
    //     // 複数テーブル登録・更新のため、現在時刻を変数化
    //     $now = getNow();
    //
    //     // トピックの編集
    //     $sql  = ' UPDATE BOARD_TOPIC SET '
    //         .' BOARD_CATEGORY_NO = :boardCategoryNo, '
    //         .$changeBoardTopicStmtSql
    //         .' BOARD_TOPIC_UPDATE_MEMBER_NO = :boardTopicUpdateMemberNo, '
    //         .' BOARD_TOPIC_UPD_TS = :boardTopicUpdTs '
    //         .' WHERE BOARD_TOPIC_NO = :boardTopicNo '
    //     ;
    //     $stmt = $con->dbh->prepare($sql);
    //     $stmt->bindValue(':boardCategoryNo', $newBoardCategoryNo);
    //
    //     if($changeBoardTopicStmtFlg){
    //         $stmt->bindValue(':boardTopicTitle', $boardTopicTitle);
    //         $stmt->bindValue(':boardTopicText', $boardTopicText);
    //     }
    //
    //     $stmt->bindValue(':boardTopicUpdateMemberNo', $_SESSION['member_no']);
    //     $stmt->bindValue(':boardTopicUpdTs', $now);
    //     $stmt->bindValue(':boardTopicNo', $boardTopicNo);
    //     $result = $stmt->execute();
    //
    //     // カテゴリの変更があった際には、新旧カテゴリどちらも更新
    //     if($changeCategoryFlg){
    //
    //         // 旧カテゴリの更新
    //         $sql  = ' UPDATE BOARD_CATEGORY SET '
    //             .' BOARD_TOPIC_NUM = :boardTopicNum, '
    //             .' BOARD_CATEGORY_UPDATE_MEMBER_NO = :boardCategoryUpdateMemberNo, '
    //             .' BOARD_CATEGORY_UPD_TS = :boardCategoryUpdTs '
    //             .' WHERE BOARD_CATEGORY_NO = :boardCategoryNo'
    //         ;
    //         $stmt = $con->dbh->prepare($sql);
    //         $stmt->bindValue(':boardTopicNum', count($this->getAllBoardTopic($oldBoardCategoryNo)));
    //         $stmt->bindValue(':boardCategoryUpdateMemberNo', $_SESSION['member_no']);
    //         $stmt->bindValue(':boardCategoryUpdTs', $now);
    //         $stmt->bindValue(':boardCategoryNo', $oldBoardCategoryNo);
    //         $stmt->execute();
    //
    //         // 新カテゴリの更新
    //         $sql  = ' UPDATE BOARD_CATEGORY '
    //             .' SET BOARD_TOPIC_NUM = :boardTopicNum, '
    //             .' BOARD_CATEGORY_UPDATE_MEMBER_NO = :boardCategoryUpdateMemberNo, '
    //             .' BOARD_CATEGORY_UPD_TS = :boardCategoryUpdTs '
    //             .' WHERE BOARD_CATEGORY_NO = :boardCategoryNo'
    //         ;
    //         $stmt = $con->dbh->prepare($sql);
    //         $stmt->bindValue(':boardTopicNum', count($this->getAllBoardTopic($newBoardCategoryNo)));
    //         $stmt->bindValue(':boardCategoryUpdateMemberNo', $_SESSION['member_no']);
    //         $stmt->bindValue(':boardCategoryUpdTs', $now);
    //         $stmt->bindValue(':boardCategoryNo', $newBoardCategoryNo);
    //         $result = $stmt->execute();
    //     }
    //
    //     return $result;
    // }


    // /**
    //  * トピック削除
    //  *
    //  * @param $boardTopicNo 削除するトピックNo
    //  *
    //  * @return bool
    //  */
    // public function deleteBoardTopic($boardTopicNo)
    // {
    //     $con = new Db;
    //     $con->connect();
    //
    //     // 複数テーブル更新のため、現在時刻を変数化
    //     $now = getNow();
    //
    //     // トピックの論理削除
    //     $sql  = ' UPDATE BOARD_TOPIC SET '
    //         .' BOARD_TOPIC_STATUS = 0, '
    //         .' BOARD_TOPIC_UPDATE_MEMBER_NO = :boardTopicUpdateMemberNo, '
    //         .' BOARD_TOPIC_UPD_TS = :boardTopicUpdTs '
    //         .' WHERE BOARD_TOPIC_NO = :boardTopicNo '
    //     ;
    //     $stmt = $con->dbh->prepare($sql);
    //     $stmt->bindValue(':boardTopicUpdateMemberNo', $_SESSION['member_no']);
    //     $stmt->bindValue(':boardTopicUpdTs', $now);
    //     $stmt->bindValue(':boardTopicNo', $boardTopicNo);
    //     $result = $stmt->execute();
    //
    //     // トピックIDに紐づくすべてのコメントを論理削除
    //     $sql  = ' UPDATE BOARD_COMMENT SET '
    //         .' BOARD_COMMENT_STATUS = 0, '
    //         .' BOARD_COMMENT_UPDATE_MEMBER_NO = :boardCategoryUpdateMemberNo, '
    //         .' BOARD_COMMENT_UPD_TS = :boardCategoryUpdTs '
    //         .' WHERE BOARD_TOPIC_NO = :boardTopicNo '
    //         .' AND BOARD_COMMENT_STATUS = 1 '
    //     ;
    //     $stmt = $con->dbh->prepare($sql);
    //     $stmt->bindValue(':boardCategoryUpdateMemberNo', $_SESSION['member_no']);
    //     $stmt->bindValue(':boardCategoryUpdTs', $now);
    //     $stmt->bindValue(':boardTopicNo', $boardTopicNo);
    //     $stmt->execute();
    //
    //     return $result;
    // }

###########テーマチャットと個人チャットを含むTO_CHATテーブルから取得する関係

//やり取りをした際にTO_CHATテーブルのメンバーの項目に登録をする(確認する)
  /**
  * チャットトップ画面のメンバー毎のダイレクトチャットで表示させるメッセージを取得する(自分以外のテーマチャット以外のgoup_chat_noから取得できる)
  * @param $chatMemberNo やり取りをしていない時は、自分、やり取りした時は、更新したメンバー
  *
  * @return string やり取りした際のテキスト
  *
  */

// TODO TO_CHATテーブルとPERMISSIONテーブルを紐付ける方法と実装方法を決める(PERMISSIONテーブルに登録する際にTO_CHATテーブルに登録する方法が一番ラク)

##########トップ画面(グループチャット、ダイレクトチャットのどこにいる時でも共通)すべての項目

/**
  * PERMISSIONテーブルにデータが格納されている状態で、新たに社員が入社した際に現時点での全チャットテーブルの最新のGROUP_CHAT_NOを取得する
  *
  * @return int 最新のGROUP_CHAT_NOを取得する
  *
  */
  public function latestGroupChatNO()
  {
    $sql = ' SELECT GROUP_CHAT_NO FROM TO_CHAT where GROUP_CHAT_NO=(select max(GROUP_CHAT_NO) from TO_CHAT)';

    $con = new Db;
    $con->connect();
    $stmt = $con->dbh->prepare($sql);
    $stmt->execute();

    //配列でない形式(countなどの抽出結果関係)
    // $group_chat_no = $stmt->fetch();

    // return $group_chat_no;
    return $stmt->fetchColumn();;
  }

  /**
    * PERMISSIONテーブルにデータが格納されている状態で、新たに社員が入社した際に新たに入社してきた社員情報をPERMISSIONテーブルに格納するために必要
    *
    * @return int 最新のPERMISSION_MEMBER_NOを取得する
    *
    */
    public function latestPermissionMemberNo()
    {
      $sql = ' SELECT PERMISSION_MEMBER_NO FROM PERMISSION where PERMISSION_NO=(select max(PERMISSION_NO) from PERMISSION)';

      $con = new Db;
      $con->connect();
      $stmt = $con->dbh->prepare($sql);
      $stmt->execute();


      return $stmt->fetchColumn();;
    }


/**
  * 週報システムのmemberテーブルからデータを取り込んでPERMISSIONテーブルに格納するための判定の為に使用する
  *
  * @return bool データがない時は、falseを返却
  *
  */
  public function newPermissionRegistCheck()
  {
    //PERMISSIONテーブルの新規判定に使用する
    $sql = ' SELECT count(PERMISSION_NO) as member_count FROM PERMISSION ';

    $con = new Db;
    $con->connect();
    $stmt = $con->dbh->prepare($sql);
    $stmt->execute();

    //配列でない形式(countなどの抽出結果関係)
    $member = $stmt->fetch();
    //データがない時
    if($member['member_count'] == 0){
      return true;
    }else{
      //データがある時
      return false;
    }
  }

  /**
   * PERMISSIONテーブルにデータを格納する為に週報システムのメンバーテーブルからデータを取得してくる
   *
   * @param $MemberNo 検索するメンバーNo
   *
   * @return array 検索結果の週報システムのメンバーを取得する
   */
  public function getWeeklyMember($MemberNo = null)
  {
      //初回にmemberテーブルからデータを格納した際に、取り込んでいる社員以降(新入社員のデータ)を取得する
      $where = (!empty($MemberNo) ? ' WHERE member_no > :MemberNo ' : null);

      $sql  = ' SELECT * FROM member '
          .$where
      ;

      //週報システムのデータベースに接続する
      $con = new Db;
      $con->connectWeeklyReport();
      $stmt = $con->dbh->prepare($sql);
      $stmt->bindValue(':MemberNo', $MemberNo);
      $stmt->execute();

      $member_data = array();
      $member_data = $stmt->fetchAll();
      return $member_data;
  }

/**
  * グループチャットのトップからダイレクトチャットで表示させる為のグループチャット番号をPERMISSIONテーブルに割り当てる
  *
  * GROUP_CHAT_NOを格納する時にテーブルにデータが入っていない時は、社員データを全件取り込みTO_CHATテーブルと紐づけてあげる
  * 社員になって初めてグループウェアにログインした時は、テーマチャットは持っていないので最初に紐づけをしてあげて、
  * 辞めた社員もデータの整合性がおかしくなるので残しておく。
  *
  * 二回目以降の取り込みの際(社員が増えた際など)は、TO_CHATテーブルの最新のGROUP_CHAT_NOを取得して、そこで新たに増えた社員などのデータを入れる
  * 週報システムのメンバーテーブルに存在社員を取得して上記の条件で更新してあげる
  * TODO または、運用の人が手で更新しても良いかもしれない
  * @return array
  *
  */
  public function setPERMISSION()
  {

      $con = new Db;
      //元々使用していた週報システムのデータを取得する(アカウント状態すべてを取得する)
      $data = $this->getWeeklyMember();
      //現在使用しているtodoebaに接続しなおす
      $con->connect();
      // 複数テーブル登録・更新のため、現在時刻を変数化
      $now = getNow();

      //PERMISSIOnテーブルの判定で使用する
      $permission_check = $this->newPermissionRegistCheck();
      //PERMISSIONテーブルにデータがまったくなく初めて取り込む時
      if($permission_check == 'TRUE'){
        //全社員データを取り込む
        foreach($data as $key => $member_data){
          // 管理テーブルの登録(ダイレクトチャットでも使用できるように修正)
          $sql  = ' INSERT INTO PERMISSION ( '
            .' PERMISSION_NO, '
            .' GROUP_CHAT_NO, '
            .' PERMISSION_MEMBER_NO, '
            .' PERMISSION_TYPE, '
            .' PERMISSION_IMAGE, '
            .' PERMISSION_INS_TS, '
            .' PERMISSION_UPD_TS, '
            .' PERMISSION_STATUS) '
            .' VALUES '
            .' (:permissionNo,'
            .' :groupChatNo, '
            .' :permissionMemberNo, '
            .' :permissionType, '
            .' :permissionImage, '
            .' :permissionInsTs, '
            .' :permissionUpdTs, '
            .' :permissionStatus) '
            ;

            //初めて取り込む時の初期値
            $groupChatNo = 1;
            //格納用の画像名とパスを指定する
            $member_image = MEMBER_IMAGE.$member_data['member_no'].'.png';
            //グループチャットメモと添付ファイルの登録処理できるようにする
            // TODO 添付ファイルの登録処理とグループチャット
            $stmt = $con->dbh->prepare($sql);
            $stmt->bindValue(':permissionNo', $permissionNo);
            $stmt->bindValue(':groupChatNo', $groupChatNo);
            $stmt->bindValue(':permissionMemberNo', $member_data['member_no']);
            $stmt->bindValue(':permissionType', $member_data['member_level_type']);
            //社員の顔写真を置くパスを指定して、格納する
            // TODO 写真の名前は、permissionMemberNo.pngなどに統一してディレクトリに格納する(ファイル形式は、とりあえずpngにしておく)
            $stmt->bindValue(':permissionImage', $member_image);
            $stmt->bindValue(':permissionInsTs', $member_data['member_ins_ts']);
            $stmt->bindValue(':permissionUpdTs', $member_data['member_upd_ts']);
            $stmt->bindValue(':permissionStatus', $member_data['member_status']);
            $stmt->execute();
            //上記で登録されたID(チャット全体のテーブルに紐付かせるグループチャット番号)を取得する
            $tmpChatNo = $con->dbh->lastInsertId('GROUP_CHAT_NO');
            //連番にして登録する TODO インサートしている時は、データベースをロックする必要があるかも
            $groupChatNo++;
            //新規判定用
            $sql  = ' SELECT * FROM TO_CHAT '
            .' WHERE GROUP_CHAT_NO = :groupChatNo '
            ;
            $stmt = $con->dbh->prepare($sql);
            $stmt->bindValue(':groupChatNo', $tmpChatNo);
            $stmt->execute();
            $data = $stmt->fetch();
            // グループチャット新規作成時は、チャットテーブルにないので紐づけの為登録する
            if(empty($data['GROUP_CHAT_NO'])){
              $this->setToChatNo($tmpChatNo);
            }
            //TODO このテーブルは、紐づけの為だけのテーブルなので更新のことは、考えなくて良い
          }
      }else{
        //PERMISSIONテーブルにデータが格納されている時は、チャットテーブル(すべてのチャットの情報が入るもの)の最新のGROUP_CHAT_NOを取得して新たに登録
        //TODO 最新のGROUP_CHAT_NOを取得
        $groupChatNo = $this->latestGroupChatNO();
        //更新の時は、PERMISSIONテーブルに新規登録した時以降のmember_no以降を取得
        $member_no = $this->latestPermissionMemberNo();
        //元々使用していた週報システムのデータを取得する(取得していない新たに入社した社員のデータを取得)
        $data = $this->getWeeklyMember($member_no);
        foreach($data as $key => $member_data){
          // 管理テーブルの登録(ダイレクトチャットでも使用できるように修正)
          $sql  = ' INSERT INTO PERMISSION ( '
            .' PERMISSION_NO, '
            .' GROUP_CHAT_NO, '
            .' PERMISSION_MEMBER_NO, '
            .' PERMISSION_TYPE, '
            .' PERMISSION_IMAGE, '
            .' PERMISSION_INS_TS, '
            .' PERMISSION_UPD_TS, '
            .' PERMISSION_STATUS) '
            .' VALUES '
            .' (:permissionNo,'
            .' :groupChatNo, '
            .' :permissionMemberNo, '
            .' :permissionType, '
            .' :permissionImage, '
            .' :permissionInsTs, '
            .' :permissionUpdTs, '
            .' :permissionStatus) '
            ;

            //格納用の画像名とパスを指定する
            $member_image = MEMBER_IMAGE.$member_data['member_no'].'.png';
            //グループチャットメモと添付ファイルの登録処理できるようにする
            // TODO 添付ファイルの登録処理とグループチャット
            $stmt = $con->dbh->prepare($sql);
            $stmt->bindValue(':permissionNo', $permissionNo);
            $stmt->bindValue(':groupChatNo', $groupChatNo);
            $stmt->bindValue(':permissionMemberNo', $member_data['member_no']);
            $stmt->bindValue(':permissionType', $member_data['member_level_type']);
            //社員の顔写真を置くパスを指定して、格納する
            // TODO 写真の名前は、permissionMemberNo.pngなどに統一してディレクトリに格納する(ファイル形式は、とりあえずpngにしておく)
            $stmt->bindValue(':permissionImage', $member_image);
            $stmt->bindValue(':permissionInsTs', $member_data['member_ins_ts']);
            $stmt->bindValue(':permissionUpdTs', $member_data['member_upd_ts']);
            $stmt->bindValue(':permissionStatus', $member_data['member_status']);
            $stmt->execute();
            //連番にしてダイレクトチャット用のGROUP_CHAT_NOを登録する TODO インサートしている時は、データベースをロックする必要があるかも
            $groupChatNo++;

            //上記で登録されたID(チャット全体のテーブルに紐付かせるグループチャット番号)を取得する
            $tmpChatNo = $con->dbh->lastInsertId('GROUP_CHAT_NO');
            //全件のチャットテーブル(TO_CHAT)にデータを登録する
            $this->setToChatNo($tmpChatNo);
        }
      }
  }

  /**
  * チャット一覧画面で自分以外の社員番号を取得する(退職者を含む)
  * @param $memberNo 自分の社員番号以外を取得する
  *
  * @return array 社員番号から写真と名前を取得する
  */
  public function getPermissionMemberAll($memberNo)
  {
    //自分以外のメンバーを検索する
    $sql  = ' SELECT * FROM PERMISSION '
        .' WHERE PERMISSION_MEMBER_NO <> :permissionMemberNo ';

    $con = new Db;
    $con->connect();
    $stmt = $con->dbh->prepare($sql);
    $stmt->bindValue(':permissionMemberNo', $memberNo);
    $stmt->execute();

    return $stmt->fetchAll();
  }

/**
* チャットトップ画面で退社も含む(自分は、省く)すべての社員数をカウントする
* @param 条件は、固定なのでパラメータは、なし
*
* @return string 社員数(退社含む)をカウントしたもの
*/
public function getPermissionMemberAllCount()
{
  //辞めていない社員を検索
  $sql = ' SELECT count(PERMISSION_MEMBER_NO) as member_count FROM PERMISSION ';

  $con = new Db;
  $con->connect();
  $stmt = $con->dbh->prepare($sql);
  $stmt->execute();

  //配列でない形式(countなどの抽出結果関係)
  $member = $stmt->fetch();
  return $member['member_count'];
}



###########自分以外の社員を取得する(サイドバーの横とダイレクトチャットの画面で必要)ダイレクトチャットの項目

  /**
  * チャット一覧画面で自分以外の社員番号を取得する(退職者は、省く)
  * @param $memberNo 自分の社員番号以外を取得する
  *
  * @return array 社員番号から写真と名前を取得する
  */
  public function getPermissionMember($memberNo)
  {
    //自分以外のメンバーを検索する
    $sql  = ' SELECT * FROM PERMISSION '
        .' WHERE PERMISSION_MEMBER_NO <> :permissionMemberNo '
        .' AND PERMISSION_STATUS = 1 '
    ;

    $con = new Db;
    $con->connect();
    $stmt = $con->dbh->prepare($sql);
    $stmt->bindValue(':permissionMemberNo', $memberNo);
    $stmt->execute();

    return $stmt->fetchAll();
  }


  /**
  * チャットトップ画面で退社していない社員数をカウントする
  * @param 条件は、固定なのでパラメータは、なし
  *
  * @return string 社員数をカウントしたもの
  */
  public function getPermissionMemberCount()
  {
    //辞めていない社員を検索
    $sql = ' SELECT count(PERMISSION_MEMBER_NO) as member_count FROM PERMISSION '
        .' WHERE PERMISSION_STATUS = 1'
    ;

    $con = new Db;
    $con->connect();
    $stmt = $con->dbh->prepare($sql);
    $stmt->execute();

    //配列でない形式(countなどの抽出結果関係)
    $member = $stmt->fetch();
    return $member['member_count'];
  }

##################@TODO 社員の画像のパスの登録方法を確認する(予めこのパスと決めてそこに画像をおくのかそしたら画像を定数化して登録すればよいだけ)

    /**
    * グループチャット作成画面か編集画面かの判定
    *
    * @param $editType GETパラメータ
    *
    * @return string 作成か編集か
    */
    public function getGroupChatEditType($editType)
    {
      if($editType == 'register'){
        return '作成';
      }

      if($editType == 'update'){
        return '編集';
      }
    }

    /**
    * チャットトップ画面で(ダイレクトチャットの画面でテキストを表示させる処理)
    *
    * @param $directChatText ダイレクトチャットメッセージ(最新のメッセージを渡してあげる)
    *
    * @return string フォーマットされたメッセージ
    * @memo	TODO 掲示板でやり取りをした際に最新のコメントの20文字の切り出しとやり取りがない時の処理は、未実装
    */
    public function formatDirectChatTextDisplay($directChatText)
    {
      //テキストの改行前の1行目の文字列を取得する($directChatTextには、最新のテキストを渡してあげる)
      // $firstRow =　explode("\r\n",$directChatText);
      //先頭から数文字分だけを取得(日本語と数字が混じると思うのでマルチバイト文字で文字化け対策でutf-8で固定)
      $formatChatText = mb_substr($directChatText,0,$this::DISPLAY_TEXT_NUM,"utf-8");
      //文字数が先頭の数文字分と違えばテキストをフォーマットして返す
      if($firstRow != $formatChatText){
        $formatChatText .= '…';
      }
      return $formatChatText;
    }

}
 ?>
