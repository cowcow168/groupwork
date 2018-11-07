<?php
//2018-06-17
/**
 * Class Board
 *
 * 掲示板全般に関するクラス
 *
 */


class Board extends Db
{
    // カテゴリ名の文字数の上限
    const CATEGORY_NAME_MAX = 30;

    // カテゴリ削除出来るトピック数の上限
    const DELETABLE_CATEGORY_TOPIC_MAX = 10;

    // デフォルトのカテゴリ
    const DEFAULT_CATEGORY = [
        'bottom' => 'その他',
        'trash' => 'ゴミ箱',
    ];

    // トピックタイトルの文字数の上限
    const TOPIC_TITLE_MAX = 30;

    // トピック本文の文字数の上限
    const TOPIC_TEXT_MAX = 10000;

    // トピックの本文表示文字数
    const DISPLAY_TEXT_NUM = 20;

    // コメントの文字数の上限
    const COMMENT_TEXT_MAX = 10000;

    // 1ページあたりの表示コメント数
    const DISPLAY_COMMENT_NUM = 3;

    /**
     * カテゴリ追加
     *
     * @param $boardCategoryName 新規登録するカテゴリ名
     *
     * @return bool
     */
    public function setBoardCategory($boardCategoryName)
    {
        $sql  = ' INSERT INTO BOARD_CATEGORY ( '
            .' BOARD_CATEGORY_NAME, '
            .' BOARD_TOPIC_NUM, '
            .' BOARD_CATEGORY_CREATE_MEMBER_NO, '
            .' BOARD_CATEGORY_INS_TS) '
            .' VALUES '
            .' (:boardCategoryName, '
            .' 0, '
            .' :boardCategoryCreateMemberNo, '
            .' :boardCategoryInsTs) '
        ;

        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':boardCategoryCreateMemberNo', $_SESSION['member_no']);
        $stmt->bindValue(':boardCategoryName', $boardCategoryName);
        $stmt->bindValue(':boardCategoryInsTs', getNow());

        return $stmt->execute();
    }

    /**
     * カテゴリ編集
     *
     * @param $boardCategoryNo 編集するカテゴリNo
     * @param $boardCategoryName 編集後のカテゴリ名
     *
     * @return bool
     */
    public function updateBoardCategory($boardCategoryNo, $boardCategoryName)
    {
        $sql  = ' UPDATE BOARD_CATEGORY SET '
            .' BOARD_CATEGORY_UPDATE_MEMBER_NO = :boardCategoryUpdateMemberNo, '
            .' BOARD_CATEGORY_UPD_TS = :boardCategoryUpdTs, '
            .' BOARD_CATEGORY_NAME =  :boardCategoryName'
            .' WHERE BOARD_CATEGORY_NO = :boardCategoryNo'
        ;

        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':boardCategoryUpdateMemberNo', $_SESSION['member_no']);
        $stmt->bindValue(':boardCategoryUpdTs', getNow());
        $stmt->bindValue(':boardCategoryName', $boardCategoryName);
        $stmt->bindValue(':boardCategoryNo', $boardCategoryNo);

        return $stmt->execute();
    }

    /**
     * カテゴリ削除
     *
     * @param $boardCategoryNo 削除するカテゴリNo
     *
     * @return bool
     */
    public function deleteBoardCategory($boardCategoryNo)
    {
        $con = new Db;
        $con->connect();

        // 複数テーブル登録・更新のため、現在時刻を変数化
        $now = getNow();

        // カテゴリの論理削除
        $sql  = ' UPDATE BOARD_CATEGORY SET '
            .' BOARD_CATEGORY_UPDATE_MEMBER_NO = :boardCategoryUpdateMemberNo, '
            .' BOARD_CATEGORY_UPD_TS = :boardCategoryUpdTs, '
            .' BOARD_CATEGORY_STATUS = 0 '
            .' WHERE BOARD_CATEGORY_NO = :boardCategoryNo'
        ;

        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':boardCategoryUpdateMemberNo', $_SESSION['member_no']);
        $stmt->bindValue(':boardCategoryUpdTs', $now);
        $stmt->bindValue(':boardCategoryNo', $boardCategoryNo);
        $stmt->execute();

        // カテゴリIDに紐づくすべてのトピックを論理削除
        $sql  = ' UPDATE BOARD_TOPIC SET '
            .' BOARD_TOPIC_UPDATE_MEMBER_NO = :boardTopicUpdateMemberNo, '
            .' BOARD_TOPIC_UPD_TS = :boardTopicUpdTs, '
            .' BOARD_TOPIC_STATUS = 0 '
            .' WHERE BOARD_CATEGORY_NO = :boardCategoryNo'
        ;

        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':boardTopicUpdateMemberNo', $_SESSION['member_no']);
        $stmt->bindValue(':boardTopicUpdTs', $now);
        $stmt->bindValue(':boardCategoryNo', $boardCategoryNo);

        return $stmt->execute();
    }

    /**
     * カテゴリ名検索
     *
     * @param $boardCategoryName 検索するカテゴリ名
     *
     * @return array 検索結果のカテゴリ1レコード
     */
    public function getBoardCategoryByName($boardCategoryName)
    {
        $sql  = ' SELECT * FROM BOARD_CATEGORY '
            .' WHERE BOARD_CATEGORY_NAME = :boardCategoryName '
            .' AND BOARD_CATEGORY_STATUS = 1 '
        ;

        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':boardCategoryName', $boardCategoryName);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * カテゴリNOからカテゴリ検索
     *
     * @param $boardCategoryNo 検索するカテゴリNo
     *
     * @return array 検索結果のカテゴリ1レコード
     */
    public function getBoardCategory($boardCategoryNo)
    {
        $sql  = ' SELECT * FROM BOARD_CATEGORY '
            .' WHERE BOARD_CATEGORY_NO = :boardCategoryNo '
            .' AND BOARD_CATEGORY_STATUS = 1 '
        ;

        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':boardCategoryNo', $boardCategoryNo);
        $stmt->execute();

        return $stmt->fetch();
    }


    /**
     * 全カテゴリ取得
     * （デフォルトカテゴリのその他は末尾、
     * 更新日時、あるいは作成日時の新しい順に）
     *
     * @return array 検索結果のカテゴリ全レコード
     */
    public function getAllBoardCategory()
    {
        $sql  = ' SELECT * FROM BOARD_CATEGORY '
            .' WHERE BOARD_CATEGORY_STATUS = 1 '
            .' AND BOARD_CATEGORY_NAME <> :trashCategory'
            .' ORDER BY BOARD_CATEGORY_NAME = :bottomDefaulCategory ASC, '
            .' CASE WHEN BOARD_CATEGORY_UPD_TS IS NOT NULL '
            .' THEN BOARD_CATEGORY_UPD_TS '
            .' ELSE BOARD_CATEGORY_INS_TS END '
            .' DESC '
        ;

        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':trashCategory', $this::DEFAULT_CATEGORY['trash']);
        $stmt->bindValue(':bottomDefaulCategory', $this::DEFAULT_CATEGORY['bottom']);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * カテゴリNoから全トピック取得
     *
     * @param $boardCategoryNo 検索するカテゴリNo
     *
     * @return array 検索結果のトピック全レコード
     */
    public function getAllBoardTopic($boardCategoryNo = null)
    {
        $and_where = (!empty($boardCategoryNo) ? ' AND BOARD_CATEGORY_NO = :boardCategoryNo ' : null);

        $sql  = ' SELECT * FROM BOARD_TOPIC '
            .' WHERE BOARD_TOPIC_STATUS = 1 '
            .$and_where
            .' ORDER BY BOARD_TOPIC_UPD_TS DESC '
        ;

        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':boardCategoryNo', $boardCategoryNo);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * トピックNOからトピック検索
     *
     * @param $boardTopicNo 検索するカテゴリNo
     *
     * @return array 検索結果のトピック1レコード
     */
    public function getBoardTopic($boardTopicNo)
    {
        $sql  = ' SELECT * FROM BOARD_TOPIC '
            .' WHERE BOARD_TOPIC_NO = :boardTopicNo '
            .' AND BOARD_TOPIC_STATUS = 1 '
        ;

        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':boardTopicNo', $boardTopicNo);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * トピック名検索
     *
     * @param $boardTopicTitle 検索するトピック名
     *
     * @return array 検索結果のトピック1レコード
     */
    public function getBoardTopicByName($boardTopicTitle)
    {
        $sql  = ' SELECT * FROM BOARD_TOPIC '
            .' WHERE BOARD_TOPIC_TITLE = :boardTopicTitle '
            .' AND BOARD_TOPIC_STATUS = 1 '
        ;

        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':boardTopicTitle', $boardTopicTitle);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * トピック作成
     *
     * @param $boardCategoryNo カテゴリNo
     * @param $boardTopicTitle トピック名
     * @param $boardTopicText トピック本文
     *
     * @return bool
     */
    public function setBoardTopic($boardCategoryNo, $boardTopicTitle, $boardTopicText)
    {
        $con = new Db;
        $con->connect();

        // 複数テーブル登録・更新のため、現在時刻を変数化
        $now = getNow();

        // トピックの登録
        $sql  = ' INSERT INTO BOARD_TOPIC ( '
            .' BOARD_CATEGORY_NO, '
            .' BOARD_TOPIC_TITLE, '
            .' BOARD_TOPIC_TEXT, '
            .' BOARD_TOPIC_CREATE_MEMBER_NO, '
            .' BOARD_TOPIC_UPDATE_MEMBER_NO, '
            .' BOARD_TOPIC_INS_TS, '
            .' BOARD_TOPIC_UPD_TS) '
            .' VALUES '
            .' (:boardCategoryNo,'
            .' :boardTopicTitle, '
            .' :boardTopicText, '
            .' :boardTopicCreateMemberNo, '
            .' :boardTopicUpdateMemberNo, '
            .' :boardTopicInsTs, '
            .' :boardTopicUpdTs) '
        ;
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':boardCategoryNo', $boardCategoryNo);
        $stmt->bindValue(':boardTopicTitle', $boardTopicTitle);
        $stmt->bindValue(':boardTopicText', $boardTopicText);
        $stmt->bindValue(':boardTopicCreateMemberNo', $_SESSION['member_no']);
        $stmt->bindValue(':boardTopicUpdateMemberNo', $_SESSION['member_no']);
        $stmt->bindValue(':boardTopicInsTs', $now);
        $stmt->bindValue(':boardTopicUpdTs', $now);
        $stmt->execute();

        // カテゴリの更新
        $sql  = ' UPDATE BOARD_CATEGORY SET '
            .' BOARD_TOPIC_NUM = :boardTopicNum, '
            .' BOARD_CATEGORY_UPDATE_MEMBER_NO = :boardCategoryUpdateMemberNo, '
            .' BOARD_CATEGORY_UPD_TS = :boardCategoryUpdTs '
            .' WHERE BOARD_CATEGORY_NO = :boardCategoryNo'
        ;
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':boardTopicNum', count($this->getAllBoardTopic($boardCategoryNo)));
        $stmt->bindValue(':boardCategoryUpdateMemberNo', $_SESSION['member_no']);
        $stmt->bindValue(':boardCategoryUpdTs', $now);
        $stmt->bindValue(':boardCategoryNo', $boardCategoryNo);

        return $stmt->execute();
    }

    /**
     * トピック編集
     *
     * @param $boardTopicNo 編集するトピックNo
     * @param $boardCategoryNo 編集後カテゴリNo
     * @param $boardTopicTitle 編集後トピック名
     * @param $boardTopicText 編集後トピック本文
     *
     * @return bool
     */
    public function updateBoardTopic($boardTopicNo, $newBoardCategoryNo, $boardTopicTitle = null, $boardTopicText = null)
    {
        $oldBoardCategoryNo = $this->getBoardTopic($boardTopicNo)['BOARD_CATEGORY_NO'];
        
        // カテゴリの変更があった際にフラグをtrueで保持する
        $changeCategoryFlg = ($oldBoardCategoryNo != $newBoardCategoryNo ? true : false);

        $changeBoardTopicStmtFlg = false;
        $changeBoardTopicStmtSql = '';

        if(!empty($boardTopicTitle) && !empty($boardTopicText)){
            $changeBoardTopicStmtFlg = true;
            $changeBoardTopicStmtSql = ' BOARD_TOPIC_TITLE = :boardTopicTitle, BOARD_TOPIC_TEXT = :boardTopicText, ';
        }

        $con = new Db;
        $con->connect();

        // 複数テーブル登録・更新のため、現在時刻を変数化
        $now = getNow();

        // トピックの編集
        $sql  = ' UPDATE BOARD_TOPIC SET '
            .' BOARD_CATEGORY_NO = :boardCategoryNo, '
            .$changeBoardTopicStmtSql
            .' BOARD_TOPIC_UPDATE_MEMBER_NO = :boardTopicUpdateMemberNo, '
            .' BOARD_TOPIC_UPD_TS = :boardTopicUpdTs '
            .' WHERE BOARD_TOPIC_NO = :boardTopicNo '
        ;
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':boardCategoryNo', $newBoardCategoryNo);

        if($changeBoardTopicStmtFlg){
            $stmt->bindValue(':boardTopicTitle', $boardTopicTitle);
            $stmt->bindValue(':boardTopicText', $boardTopicText);
        }

        $stmt->bindValue(':boardTopicUpdateMemberNo', $_SESSION['member_no']);
        $stmt->bindValue(':boardTopicUpdTs', $now);
        $stmt->bindValue(':boardTopicNo', $boardTopicNo);
        $result = $stmt->execute();

        // カテゴリの変更があった際には、新旧カテゴリどちらも更新
        if($changeCategoryFlg){

            // 旧カテゴリの更新
            $sql  = ' UPDATE BOARD_CATEGORY SET '
                .' BOARD_TOPIC_NUM = :boardTopicNum, '
                .' BOARD_CATEGORY_UPDATE_MEMBER_NO = :boardCategoryUpdateMemberNo, '
                .' BOARD_CATEGORY_UPD_TS = :boardCategoryUpdTs '
                .' WHERE BOARD_CATEGORY_NO = :boardCategoryNo'
            ;
            $stmt = $con->dbh->prepare($sql);
            $stmt->bindValue(':boardTopicNum', count($this->getAllBoardTopic($oldBoardCategoryNo)));
            $stmt->bindValue(':boardCategoryUpdateMemberNo', $_SESSION['member_no']);
            $stmt->bindValue(':boardCategoryUpdTs', $now);
            $stmt->bindValue(':boardCategoryNo', $oldBoardCategoryNo);
            $stmt->execute();

            // 新カテゴリの更新
            $sql  = ' UPDATE BOARD_CATEGORY '
                .' SET BOARD_TOPIC_NUM = :boardTopicNum, '
                .' BOARD_CATEGORY_UPDATE_MEMBER_NO = :boardCategoryUpdateMemberNo, '
                .' BOARD_CATEGORY_UPD_TS = :boardCategoryUpdTs '
                .' WHERE BOARD_CATEGORY_NO = :boardCategoryNo'
            ;
            $stmt = $con->dbh->prepare($sql);
            $stmt->bindValue(':boardTopicNum', count($this->getAllBoardTopic($newBoardCategoryNo)));
            $stmt->bindValue(':boardCategoryUpdateMemberNo', $_SESSION['member_no']);
            $stmt->bindValue(':boardCategoryUpdTs', $now);
            $stmt->bindValue(':boardCategoryNo', $newBoardCategoryNo);
            $result = $stmt->execute();
        }

        return $result;
    }

    /**
     * トピック削除
     *
     * @param $boardTopicNo 削除するトピックNo
     *
     * @return bool
     */
    public function deleteBoardTopic($boardTopicNo)
    {
        $con = new Db;
        $con->connect();

        // 複数テーブル更新のため、現在時刻を変数化
        $now = getNow();

        // トピックの論理削除
        $sql  = ' UPDATE BOARD_TOPIC SET '
            .' BOARD_TOPIC_STATUS = 0, '
            .' BOARD_TOPIC_UPDATE_MEMBER_NO = :boardTopicUpdateMemberNo, '
            .' BOARD_TOPIC_UPD_TS = :boardTopicUpdTs '
            .' WHERE BOARD_TOPIC_NO = :boardTopicNo '
        ;
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':boardTopicUpdateMemberNo', $_SESSION['member_no']);
        $stmt->bindValue(':boardTopicUpdTs', $now);
        $stmt->bindValue(':boardTopicNo', $boardTopicNo);
        $result = $stmt->execute();

        // トピックIDに紐づくすべてのコメントを論理削除
        $sql  = ' UPDATE BOARD_COMMENT SET '
            .' BOARD_COMMENT_STATUS = 0, '
            .' BOARD_COMMENT_UPDATE_MEMBER_NO = :boardCategoryUpdateMemberNo, '
            .' BOARD_COMMENT_UPD_TS = :boardCategoryUpdTs '
            .' WHERE BOARD_TOPIC_NO = :boardTopicNo '
            .' AND BOARD_COMMENT_STATUS = 1 '
        ;
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':boardCategoryUpdateMemberNo', $_SESSION['member_no']);
        $stmt->bindValue(':boardCategoryUpdTs', $now);
        $stmt->bindValue(':boardTopicNo', $boardTopicNo);
        $stmt->execute();

        return $result;
    }

    /**
     * トピックNoから全コメント取得
     *
     * @param $boardTopicNo 検索するトピックNo
     * @param $pageNum 「もっと見る」を押下した回数
     * @param $countFlg 登録時に表示番号を全件から計算するためのフラグ
     * @param $insertFlg 登録時に新しいコメントを先頭に1件だけ追加表示するためのフラグ
     * @return array 検索結果のコメント全レコード
     */
    public function getAllBoardComment($boardTopicNo, $pageNum = null, $countFlg = false, $insertFlg = false, $registerNum = null)
    {
        if(empty($pageNum)){
            $pageNum = 0;
        }

        $limit_sql = '';

        if(!empty($registerNum)){
            $limit_sql = ' LIMIT 1 ';
        }else{
            $registerNum = 0;
        }



        if(!$countFlg){
            $limit_start = $pageNum * $this::DISPLAY_COMMENT_NUM + $registerNum;
            $limit_sql = ' LIMIT '.$limit_start.' , '.$this::DISPLAY_COMMENT_NUM;
        }

        if($insertFlg){
            $limit_sql = ' LIMIT 1 ';
        }


        // 削除済みのコメントも削除済みとして表示するため、statusを限定しない
        // 返信コメントは返信先コメントの直下に表示する
        $sql  = ' SELECT * FROM BOARD_COMMENT '
            .' WHERE BOARD_TOPIC_NO = :boardTopicNo'
            .' ORDER BY '
            .' BOARD_COMMENT_NO DESC '
            .$limit_sql
        ;

        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':boardTopicNo', $boardTopicNo);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * コメントNOからコメント検索
     *
     * @param $boardCommentNo 検索するコメントNo
     *
     * @return array 検索結果のトピック1レコード
     */
    public function getBoardComment($boardCommentNo)
    {
        // 削除済みのコメントも削除済みとして表示するため、statusを限定しない
        $sql  = ' SELECT * FROM BOARD_COMMENT '
            .' WHERE BOARD_COMMENT_NO = :boardCommentNo '
        ;

        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':boardCommentNo', $boardCommentNo);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * 大本の返信先コメントNOからコメント検索
     *
     * @param $parentReplyToBoardCommentNo 検索する返信先コメントNo
     *
     * @return array 検索結果のトピック全レコード
     */
    public function getAllBoardCommentByReply($parentReplyToBoardCommentNo)
    {
        // 削除済みのコメントも削除済みとして表示するため、statusを限定しない
        $sql  = ' SELECT * FROM BOARD_COMMENT '
            .' WHERE PARENT_REPLY_TO_BOARD_COMMENT_NO = :parentReplyToBoardCommentNo '
            .' OR BOARD_COMMENT_NO = :boardCommentNo '
            .' ORDER BY BOARD_COMMENT_NO ASC'
        ;

        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':parentReplyToBoardCommentNo', $parentReplyToBoardCommentNo);
        $stmt->bindValue(':boardCommentNo', $parentReplyToBoardCommentNo);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * コメント投稿
     *
     * @param $boardTopicNo トピックNo
     * @param $boardCommentText コメント本文
     * @param $replyToBoardCommentNo 返信先コメントNo
     *
     * @return bool
     */
    public function setBoardComment($boardTopicNo, $boardCommentText, $replyToBoardCommentNo = null)
    {
        // 親返信先（返信先の大本）コメントNo、返信先コメントNoの初期化
        $parent_reply_to_board_comment_no = null;
        $reply_to_board_comment_no = null;

        // 返信コメントの場合
        if(!empty($replyToBoardCommentNo)){

            // 返信先コメントの親返信先コメントNoを取得
            $parent_reply_to_board_comment_no = $this->getBoardComment($replyToBoardCommentNo)['PARENT_REPLY_TO_BOARD_COMMENT_NO'];

            // もし返信先のコメントが他のコメントへの返信をしていなかった場合、
            // 返信先コメントNoを親返信先コメントNoとして登録する
            if(empty($parent_reply_to_board_comment_no)) {
                $parent_reply_to_board_comment_no = $replyToBoardCommentNo;
            }
        }

        $con = new Db;
        $con->connect();

        // 複数テーブル登録・更新のため、現在時刻を変数化
        $now = getNow();

        // コメントの登録
        $sql  = ' INSERT INTO BOARD_COMMENT ( '
            .' BOARD_TOPIC_NO, '
            .' BOARD_COMMENT_DISPLAY_NO, '
            .' BOARD_COMMENT_TEXT, '
            .' PARENT_REPLY_TO_BOARD_COMMENT_NO, '
            .' REPLY_TO_BOARD_COMMENT_NO, '
            .' BOARD_COMMENT_CREATE_MEMBER_NO, '
            .' BOARD_COMMENT_UPDATE_MEMBER_NO, '
            .' BOARD_COMMENT_INS_TS, '
            .' BOARD_COMMENT_UPD_TS) '
            .' VALUES '
            .' (:boardTopicNo,'
            .' :boardCommentDisplayNo, '
            .' :boardCommentText, '
            .' :parentReplyToBoardCommentNo, '
            .' :replyToBoardCommentNo, '
            .' :boardCommentCreateMemberNo, '
            .' :boardCommentUpdateMemberNo, '
            .' :boardCommentInsTs, '
            .' :boardCommentUpdTs) '
        ;
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':boardTopicNo', $boardTopicNo);
        $stmt->bindValue(':boardCommentDisplayNo', count($this->getAllBoardComment($boardTopicNo, null, true)) + 1);
        $stmt->bindValue(':boardCommentText', $boardCommentText);
        $stmt->bindValue(':parentReplyToBoardCommentNo', $parent_reply_to_board_comment_no);
        $stmt->bindValue(':replyToBoardCommentNo', $replyToBoardCommentNo);
        $stmt->bindValue(':boardCommentCreateMemberNo', $_SESSION['member_no']);
        $stmt->bindValue(':boardCommentUpdateMemberNo', $_SESSION['member_no']);
        $stmt->bindValue(':boardCommentInsTs', $now);
        $stmt->bindValue(':boardCommentUpdTs', $now);
        $stmt->execute();

        // トピックの更新
        $sql  = ' UPDATE BOARD_TOPIC SET '
            .' BOARD_TOPIC_UPDATE_MEMBER_NO = :boardTopicUpdateMemberNo, '
            .' BOARD_TOPIC_UPD_TS = :boardTopicUpdTs '
            .' WHERE BOARD_TOPIC_NO = :boardTopicNo'
        ;
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':boardTopicUpdateMemberNo', $_SESSION['member_no']);
        $stmt->bindValue(':boardTopicUpdTs', $now);
        $stmt->bindValue(':boardTopicNo', $boardTopicNo);
        $stmt->execute();

        // カテゴリの更新
        $sql  = ' UPDATE BOARD_CATEGORY SET '
            .' BOARD_CATEGORY_UPDATE_MEMBER_NO = :boardCategoryUpdateMemberNo, '
            .' BOARD_CATEGORY_UPD_TS = :boardCategoryUpdTs '
            .' WHERE BOARD_CATEGORY_NO = :boardCategoryNo'
        ;
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':boardCategoryUpdateMemberNo', $_SESSION['member_no']);
        $stmt->bindValue(':boardCategoryUpdTs', $now);
        $stmt->bindValue(':boardCategoryNo', $this->getBoardTopic($boardTopicNo)['BOARD_CATEGORY_NO']);

        return $stmt->execute();
    }

    /**
     * コメント削除
     *
     * @param $boardCommentNo 削除するコメントNo
     *
     * @return bool
     */
    public function deleteBoardComment($boardCommentNo)
    {
        $con = new Db;
        $con->connect();

        // 複数テーブル更新のため、現在時刻を変数化
        $now = getNow();

        // コメントの論理削除
        $sql  = ' UPDATE BOARD_COMMENT SET '
            .' BOARD_COMMENT_UPDATE_MEMBER_NO = :boardCommentUpdateMemberNo, '
            .' BOARD_COMMENT_UPD_TS = :boardCommentUpdTs, '
            .' BOARD_COMMENT_STATUS = 0 '
            .' WHERE BOARD_COMMENT_NO = :boardCommentNo'
        ;

        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':boardCommentUpdateMemberNo', $_SESSION['member_no']);
        $stmt->bindValue(':boardCommentUpdTs', $now);
        $stmt->bindValue(':boardCommentNo', $boardCommentNo);
        $stmt->execute();

        // トピックの更新

        $board_topic_no = $this->getBoardComment($boardCommentNo)['BOARD_TOPIC_NO'];

        $sql  = ' UPDATE BOARD_TOPIC SET '
            .' BOARD_TOPIC_UPDATE_MEMBER_NO = :boardTopicUpdateMemberNo, '
            .' BOARD_TOPIC_UPD_TS = :boardTopicUpdTs '
            .' WHERE BOARD_TOPIC_NO = :boardTopicNo'
        ;
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':boardTopicUpdateMemberNo', $_SESSION['member_no']);
        $stmt->bindValue(':boardTopicUpdTs', $now);
        $stmt->bindValue(':boardTopicNo', $board_topic_no);
        $stmt->execute();

        // カテゴリの更新
        $sql  = ' UPDATE BOARD_CATEGORY SET '
            .' BOARD_CATEGORY_UPDATE_MEMBER_NO = :boardCategoryUpdateMemberNo, '
            .' BOARD_CATEGORY_UPD_TS = :boardCategoryUpdTs '
            .' WHERE BOARD_CATEGORY_NO = :boardCategoryNo'
        ;
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':boardCategoryUpdateMemberNo', $_SESSION['member_no']);
        $stmt->bindValue(':boardCategoryUpdTs', $now);
        $stmt->bindValue(':boardCategoryNo', $this->getBoardTopic($board_topic_no)['BOARD_CATEGORY_NO']);

        return $stmt->execute();
    }

    /**
     * トピック作成画面か編集画面かの判定
     *
     * @param $editType GETパラメータ
     *
     * @return string 作成か編集か
     */
    public function getTopicEditType($editType)
    {
        if($editType == 'register'){
            return '作成';
        }

        if($editType == 'update'){
            return '編集';
        }
    }

    /**
     * 本文を掲示板トップ画面表示用にフォーマット
     *
     * @param $boardTopicText 本文全文
     *
     * @return string フォーマットされた本文
     */
    public function formatBoardTopicTextForDisplay($boardTopicText)
    {
        // 最初の改行前の文字列を取得
        $firstRow = explode("\r\n", $boardTopicText)[0];

        // 先頭から表示文字分だけを取得
        $formatBoardTopicText = substr($firstRow, 0, $this::DISPLAY_TEXT_NUM);

        if($firstRow != $formatBoardTopicText){
            $formatBoardTopicText .= '…';
        }

        return $formatBoardTopicText;
    }

}
