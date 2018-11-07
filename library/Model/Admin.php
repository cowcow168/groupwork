<?php
//2018-05-11権限確認 見えるとこだけ
/**
 * Class Config
 *
 * コンフィグページに関するクラス
 *
 */

class Admin extends Db
{
    /**
     * 総務連絡管理画面に入れる人
     *
     * @param $memberNo
     *
     * @return bool
     */
    public static function chkWelfareEditMember($memberNo)
    {
        if(is_numeric($memberNo)){
            if($_SESSION['permission'] == 1 || $memberNo == 13) return true;
        }
    }

    /**
     * 週報確認管理ページの権限チェック
     *
     * @param $permission
     *
     * @return bool
     */
    public function chkResponsePermission($permission)
    {
        if($permission <= 2 || $permission >= 5){
            return true;
        }
    }

    /**
     * 設定変更
     *
     * @param $memberNo
     * @param $weeklyReportTeamShareStatus
     * @param $hintStatus
     * @param $showStatus
     *
     * @return string
     */
    public static function saveConfig($memberNo, $weeklyReportTeamShareStatus, $hintStatus, $showStatus)
    {
        if(is_numeric($weeklyReportTeamShareStatus) &&
           is_numeric($hintStatus) &&
           is_numeric($showStatus) &&
           is_numeric($memberNo)
        ){
            $weeklyReportTeamShareStatus = ($weeklyReportTeamShareStatus == 1 ? true : false);
            $hintStatus = ($hintStatus == 1 ? true : false);
            $showStatus = ($showStatus == 1 ? true : false);
            $con = new Db;
            $con->connect();
            $stmt = $con->dbh->query('SELECT member_no FROM config WHERE member_no = ' . $memberNo);
            if($stmt->fetch()){
                $sql  = 'UPDATE config SET '
                    . '  weekly_report_team_share_status = :weekly_report_team_share_status '
                    . ', hint_status = :hint_status '
                    . ', weekly_report_response_show_status = :weekly_report_response_show_status '
                    . ', config_upd_ts = CURRENT_TIMESTAMP() '
                    . ' WHERE member_no = :member_no'
                ;
            }else{
                $sql  = 'INSERT INTO config( '
                        . '  member_no '
                        . ', weekly_report_team_share_status '
                        . ', hint_status '
                        . ', weekly_report_response_show_status '
                    . ') VALUES ('
                        . '  :member_no'
                        . ', :weekly_report_team_share_status '
                        . ', :hint_status '
                        . ', :weekly_report_response_show_status '
                    . ' ) '
                ;
            }
            $stmt = $con->dbh->prepare($sql);
            $stmt->bindValue('member_no', $memberNo, PDO::PARAM_INT);
            $stmt->bindValue(':weekly_report_team_share_status', $weeklyReportTeamShareStatus, PDO::PARAM_INT);
            $stmt->bindValue(':hint_status', $hintStatus, PDO::PARAM_INT);
            $stmt->bindValue(':weekly_report_response_show_status', $showStatus, PDO::PARAM_INT);
            $stmt->execute();

            return '設定を変更しました。';
        }else{
            return '不正な値です。';
        }
    }

    /**
     * admin_topで週報コメント確認を本日行わない処理
     *
     * @param $memberNo
     *
     * @return void
     */
    public static function weeklyReportResponseTodayNotShow($memberNo)
    {
        if(is_numeric($memberNo)){
            $con = new Db;
            $con->connect();
            $stmt = $con->dbh->query('SELECT member_no FROM config WHERE member_no = ' . $memberNo);
            if($stmt->fetch()){
                $sql  = 'UPDATE config SET '
                    . '  weekly_report_response_not_show_date = ' . Db::dbstring(date('Y-m-d'))
                    . ', config_upd_ts = CURRENT_TIMESTAMP() '
                    . ' WHERE member_no = :member_no'
                ;
            }else{
                $sql  = 'INSERT INTO config( '
                        . '  member_no '
                        . ', weekly_report_response_not_show_date '
                    . ') VALUES ('
                        . '  :member_no'
                        . ', ' . Db::dbstring(date('Y-m-d'))
                    . ' ) '
                ;
            }
            $stmt = $con->dbh->prepare($sql);
            $stmt->bindValue('member_no', $memberNo, PDO::PARAM_INT);
            $stmt->execute();
            header('Location: admin_top');
            exit;
        }
    }

    /**
     * BOX情報取得
     *
     * @param $add
     * @param $del
     * @param $box
     *
     * @return array
     */
    public static function getConfig($memberNo)
    {
        if(is_numeric($memberNo)){
            $con = new Db;
            $con->connect();
            $stmt = $con->dbh->query('SELECT * FROM config WHERE member_no = ' . $memberNo);
            if($return = $stmt->fetch(PDO::FETCH_ASSOC)){
                return $return;
            }
        }
    }

    /**
     * 福利厚生管理保存
     *
     * @param $save
     * @param $post
     *
     * @return void
     */
    public static function setWelfare($save, $post)
    {
        if($save && $post){
            $con = new Db;
            $con->connect();
            $stmt = $con->dbh->query('SELECT welfare_no FROM welfare');
            $con->dbh->beginTransaction();
            if(!$stmt->fetch()){
                //初回のみinsert
                try {
                    $stmt = $con->dbh->prepare('INSERT INTO welfare('
                            . ' welfare_description '
                        . ')VALUES('
                            . ':description'
                        . ')'
                    );
                    $stmt->bindValue('description', $post['welfare_description'], PDO::PARAM_STR);
                    $stmt->execute();

                    foreach($post['loop'] as $key => $val){
                        $stmt = $con->dbh->prepare('INSERT INTO welfare_detail('
                                . '  welfare_no '
                                . ', welfare_detail_title '
                                . ', welfare_detail_article '
                                . ', welfare_detail_contact '
                            . ')VALUES('
                                . '  1 '
                                . ', :title '
                                . ', :article '
                                . ', :contact '
                            . ')'
                        );
                        $stmt->bindValue('title', $val['welfare_detail_title'], PDO::PARAM_STR);
                        $stmt->bindValue('article', $val['welfare_detail_article'], PDO::PARAM_STR);
                        $stmt->bindValue('contact', $val['welfare_detail_contact'], PDO::PARAM_STR);
                        $stmt->execute();
                    }
                }catch(Exception $e){
                    echo Mysql::FAIL;
                    logger($e);
                    logger($request);
                    $con->dbh->rollBack();
                    die;
                }

            }else{
                //二回目以降
                try {
                    $stmt = $con->dbh->prepare('UPDATE welfare SET '
                        . '  welfare_description = :description '
                        . ', welfare_last_upd_ts = now() '
                        . ' WHERE welfare_no = 1'
                    );
                    $stmt->bindValue('description', $post['welfare_description'], PDO::PARAM_STR);
                    $stmt->execute();
                    $stmt = $con->dbh->query('DELETE FROM welfare_detail');

                    foreach($post['loop'] as $key => $val){
                        //入力される内容は全てnot nullの前提
                        if(!empty($val['welfare_detail_title'])
                            &&
                            !empty($val['welfare_detail_article'])
                            &&
                            !empty($val['welfare_detail_article'])
                        ){
                            $stmt = $con->dbh->prepare('INSERT INTO welfare_detail('
                                    . '  welfare_no '
                                    . ', welfare_detail_title '
                                    . ', welfare_detail_article '
                                    . ', welfare_detail_contact '
                                . ')VALUES('
                                    . '  1 '
                                    . ', :title '
                                    . ', :article '
                                    . ', :contact '
                                . ')'
                            );
                            $stmt->bindValue('title', $val['welfare_detail_title'], PDO::PARAM_STR);
                            $stmt->bindValue('article', $val['welfare_detail_article'], PDO::PARAM_STR);
                            $stmt->bindValue('contact', $val['welfare_detail_contact'], PDO::PARAM_STR);
                            $stmt->execute();
                        }else{
                            unset($_POST['loop'][$key]);
                        }
                    }
                }catch(Exception $e){
                    echo Mysql::FAIL;
                    logger($e);
                    logger($request);
                    $con->dbh->rollBack();
                    die;
                }
            }
            $con->dbh->commit();
        }
    }

    /**
     * 福利厚生管理取得
     *
     * @return array
     */
    public static function getWelfare()
    {
        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->query('SELECT welfare_description FROM welfare');
        //DBに前文が保存されているかどうか
        $description = $stmt->fetch()[0];
        if(!$_POST['loop']){
            //DBにwelfare_detailが保存されてない場合
            $stmt = $con->dbh->query('SELECT welfare_detail_title, welfare_detail_article, welfare_detail_contact FROM welfare_detail');
            if($result['loop'] = $stmt->fetchAll(PDO::FETCH_ASSOC)){
                return (array)$result + array('welfare_description' => $description);
            }
        }else{
            $_POST['loop'] = array_merge($_POST['loop']);
            return $_POST + array('welfare_description' => $description);
        }
    }

    /**
     * boxの数を取得
     *
     * @return array
     */
    public static function getBoxNumber()
    {
        if($_REQUEST['box']){
            return $_REQUEST['box'];
        }elseif(count($_REQUEST['loop'] > 1) && $_REQUEST['loop']){
            return count($_REQUEST['loop']);
        }else{
            return 1;
        }
    }

    /**
     * 作業内容ボタンの表示
     *
     * @return string
     */
    public static function getPageType()
    {
        $PAGENAME = array(
            'welfare' => '福利厚生',
            'club' => '同好会',
            'general_affairs_report' => '総務お知らせ',
            'human_report' => '人事通信',
            'leader_report' => 'リーダーお知らせ',
            'weekly_report_response' => '週報確認',
        );
        $name_type = explode('_', explode('.',explode('/', $_SERVER['SCRIPT_NAME'])[1])[0]);
        for($i=0; $i<=count($name_type)-2; $i++){
            $pagename .= $name_type[$i];
            if($i != count($name_type)-2){
                $pagename .= '_';
            }
        }
        $MODE = array(
            'list' => 'リスト',
            'add' => '登録',
            'upd' => '編集',
            'del' => '削除',
            'dtl' => 'プレビュー',
        );
        if(!$_GET['mode']){
            $mode = $MODE[end($name_type)];
        }else{
            $mode = $MODE[$_GET['mode']];
        }
        $type = explode('.', end(explode('_', end(explode('/', $_SERVER['SCRIPT_NAME'])))))[0];
        $TYPE = array(
            'conf' => '確認',
            'done' => '完了',
        );

        if(!$PAGENAME[$pagename]){
            $PAGENAME = array(
                'organization_chart' => '組織図',
            );
            return $PAGENAME[str_replace('.php', '',basename($_SERVER['SCRIPT_NAME']))];
        }
        return $PAGENAME[$pagename].'管理'.$mode.$TYPE[$type];
    }

    /**
     * 公開状態の変更
     *
     * @param $openStatus
     *
     * @return array
     */
    public static function changeStatus($tableName, $openStatus=null)
    {
        if(!is_null($openStatus) && !is_null($tableName)){
            if(is_numeric($openStatus)){
                if($openStatus == 1){
                    $status = 'true';
                }elseif($openStatus == 0){
                    $status = 'false';
                }
                $sql = 'UPDATE '.$tableName.' SET '
                    . $tableName.'_status = ' . $status
                    . ' WHERE '.$tableName.'_no = ' . $_REQUEST[$tableName.'_no'];
                $con = new Db;
                $con->connect();
                $stmt = $con->dbh->query($sql);
            }
            header('Location: '.$tableName.'_list');
            exit;
        }
    }

    /**
     * 福利厚生管理保存
     *
     * @param $save
     * @param $post
     *
     * @return void
     */
    public static function setClub($save, $post)
    {
        if($save && $post){
            foreach($post['loop'] as $key => $val){
                //truncateするとrollback出来なくなるので先に入力チェック
                if(empty($val['club_detail_name']) || empty($val['club_detail_purpose']) || count($val['member_no']) < 5){
                    $_REQUEST['error'] = '入力条件に満たないです。';
                    return false;
                }
            }
            $con = new Db;
            $con->connectTruncate();
            $stmt = $con->dbh->query('SELECT club_no FROM club');
            $con->dbh->beginTransaction();
            if(!$stmt->fetch()){
                //初回のみinsert
                try {
                    //前文の登録
                    $stmt = $con->dbh->prepare('INSERT INTO club('
                            . ' club_description '
                        . ')VALUES('
                            . ':description'
                        . ')'
                    );
                    $stmt->bindValue('description', $post['club_description'], PDO::PARAM_STR);
                    $stmt->execute();
                    //同好会単位でループ
                    foreach($post['loop'] as $key => $val){
                        $stmt = $con->dbh->prepare('INSERT INTO club_detail('
                                . '  club_detail_no '
                                . ', club_no '
                                . ', club_detail_name '
                                . ', club_detail_purpose'
                            . ')VALUES('
                                . $key
                                . ', 1 '
                                . ', :name '
                                . ', :purpose '
                            . ')'
                        );
                        $stmt->bindValue('name', $val['club_detail_name'], PDO::PARAM_STR);
                        $stmt->bindValue('purpose', $val['club_detail_purpose'], PDO::PARAM_STR);
                        $stmt->execute();

                        //一つの同好会に対して部員の登録
                        foreach($val['club_member'] as $key2 => $val2){
                            $con->dbh->query('INSERT INTO club_member('
                                    . ' club_detail_no '
                                    . ', member_no '
                                . ')VALUES('
                                    .    $key
                                    .','.$key2
                                . ')'
                            );
                        }
                    }
                }catch(Exception $e){
                    echo Mysql::FAIL;
                    logger($e);
                    logger($request);
                    $con->dbh->rollBack();
                    die;
                }

            }else{
                //二回目以降
                try {
                    $stmt = $con->dbh->prepare('UPDATE club SET '
                        . '  club_description = :description '
                        . ', club_last_upd_ts = now() '
                        . ' WHERE club_no = 1'
                    );
                    $stmt->bindValue('description', $post['club_description'], PDO::PARAM_STR);
                    $stmt->execute();
                    //編集の場合は一度detailとmemberは消す
                    $con->dbh->query('truncate table club_member');
                    $con->dbh->query('truncate table club_detail');

                    foreach($post['loop'] as $key => $val){
                        //同好会単位でループ
                        $sql = 'INSERT INTO club_detail('
                                . '  club_detail_no '
                                . ', club_no '
                                . ', club_detail_name '
                                . ', club_detail_purpose'
                            . ')VALUES('
                                . (int)($key+1)
                                . ', 1 '
                                . ', :name '
                                . ', :purpose '
                            . ')'
                        ;
                        $stmt = $con->dbh->prepare($sql);
                        $stmt->bindValue('name', $val['club_detail_name'], PDO::PARAM_STR);
                        $stmt->bindValue('purpose', $val['club_detail_purpose'], PDO::PARAM_STR);
                        $stmt->execute();

                        //一つの同好会に対して部員の登録
                        foreach($val['member_no'] as $key2 => $val2){
                            $con->dbh->query('INSERT INTO club_member('
                                    . ' club_detail_no '
                                    . ', member_no '
                                . ')VALUES('
                                    .    (int)($key+1)
                                    .','.$key2
                                . ')'
                            );
                        }
                    }
                }catch(Exception $e){
                    echo Mysql::FAIL;
                    logger($e);
                    logger($request);
                    $con->dbh->rollBack();
                    die;
                }
            }
            $_REQUEST['error'] = '保存できました';
            $con->dbh->commit();
        }
    }

    /**
     * 同好会管理取得
     *
     * @return array
     */
    public static function getClub()
    {
        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->query('SELECT club_description FROM club');
        //DBに前文が保存されているかどうか
        $description = $stmt->fetch()[0];
        if(!$_POST['loop']){
            //DBにclub_detailが保存されてない場合
            $stmt = $con->dbh->query('SELECT club_detail_name, club_detail_purpose FROM club_detail');
            if($result['loop'] = $stmt->fetchAll(PDO::FETCH_ASSOC)){
                $stmt = $con->dbh->query('SELECT club_detail_no, member_no FROM club_member');
                if($club_member = $stmt->fetchAll(PDO::FETCH_ASSOC)){
                    //メンバー情報をloopに取り込む
                    foreach($result['loop'] as $key => $val){
                        foreach($club_member as $key2 => $val2){
                            if($key+1 == $val2['club_detail_no']){
                                $result['loop'][$key]['member_no'][$club_member[$key2]['member_no']] = $club_member[$key2]['member_no'];
                            }
                        }
                    }
                    return (array)$result + array('club_description' => $description);
                }
            }
        }else{
            $_POST['loop'] = array_merge($_POST['loop']);
            return $_POST + array('club_description' => $description);
        }
    }

    /**
     * 同好会メンバーが4つ以上迂闊を掛け持ちしてないか監視
     *
     * @param member
     *
     * @return array
     */
    public static function chkClubMemberDuplicate($member)
    {
        if($member){
            foreach($member as $k => $v){
                if($v['member_no']){
                    foreach($v['member_no'] as $vv){
                        $count[$vv]++;
                    }
                }
            }

            foreach($count as $k => $v){
                //4回以上登録されていたら保存
                if($v > 3){
                    $_REQUEST['result'][$k] = $v;
                }
            }

            if($_REQUEST['result']){
                return 1;
            }
        }
    }

    /**
     * edit→conf→doneタイプのDB保存
     *
     * @param $mode
     * @param $no
     *
     * @return bool
     */
    public static function saveGeneralAffairsReport($mode, $no=null)
    {
        if($mode != 'add' && $mode != 'upd'){
            return false;
        }

        if($mode == 'upd' && !is_numeric($no)){
            return false;
        }

        //xss対策しないでください

        //新規登録
        if($mode == 'add'){
            $sql = 'INSERT INTO general_affairs_report('
                    . ' general_affairs_report_title '
                    . ', general_affairs_report_article '
                    .($_POST['general_affairs_report_description'] ? ', general_affairs_report_description ' : '')
                . ')VALUES('
                    .        Db::dbstring($_POST['general_affairs_report_title'])
                    . ', ' . Db::dbstring($_POST['general_affairs_report_article'])
                    .($_POST['general_affairs_report_description'] ? ', ' . Db::dbstring($_POST['general_affairs_report_description']) : '')
                . ')'
            ;

            $con = new Db;
            $con->connect();
            $stmt = $con->dbh->query($sql);
        }elseif($mode == 'upd'){
            //まずcontactNoが不正な値でないか確認
            $sql = 'SELECT general_affairs_report_no FROM general_affairs_report WHERE general_affairs_report_no = :general_affairs_report_no';
            $con = new Db;
            $con->connect();
            $stmt = $con->dbh->prepare($sql);
            $stmt->bindValue(':general_affairs_report_no', $no);
            $stmt->execute();
            if(!$general_affairs_report_no = $stmt->fetch()['general_affairs_report_no']){
                return false;
            }else{
                $sql = 'UPDATE general_affairs_report SET '
                    . ' general_affairs_report_upd_ts = CURRENT_TIMESTAMP '
                    .', general_affairs_report_title = ' . ($_POST['general_affairs_report_title'] ? Db::dbstring($_POST['general_affairs_report_title']) : 'NULL')
                    .', general_affairs_report_article = ' . ($_POST['general_affairs_report_article'] ? Db::dbstring($_POST['general_affairs_report_article']) : 'NULL')
                    .', general_affairs_report_description = ' . ($_POST['general_affairs_report_description'] ? Db::dbstring($_POST['general_affairs_report_description']) : 'NULL')
                    . ' WHERE general_affairs_report_no = :general_affairs_report_no'
                ;echo $general_affairs_report_no;
                $stmt = $con->dbh->prepare($sql);
                $stmt->bindValue(':general_affairs_report_no', $general_affairs_report_no);
                $stmt->execute();
                return true;
            }
        }
    }

    /**
     * edit→conf→doneタイプのDB保存 人事通信だからxss対策してはいけない
     *
     * @param $mode
     * @param $no
     *
     * @return bool
     */
    public static function saveHumanReport($mode, $no=null)
    {
        if($mode != 'add' && $mode != 'upd'){
            return false;
        }

        if($mode == 'upd' && !is_numeric($no)){
            return false;
        }

        //xss対策しないでください

        //新規登録
        if($mode == 'add'){
            $sql = 'INSERT INTO human_report('
                    . ' human_report_title '
                    . ', human_report_article '
                . ')VALUES('
                    .        Db::dbstring($_SESSION['human_report_title'])
                    . ', ' . Db::dbstring($_SESSION['human_report_article'])
                . ')'
            ;

            $con = new Db;
            $con->connect();
            $stmt = $con->dbh->query($sql);
        }elseif($mode == 'upd'){
            $sql = 'SELECT human_report_no FROM human_report WHERE human_report_no = :human_report_no';
            $con = new Db;
            $con->connect();
            $stmt = $con->dbh->prepare($sql);
            $stmt->bindValue(':human_report_no', $no);
            $stmt->execute();
            if(!$human_report_no = $stmt->fetch()['human_report_no']){
                return false;
            }else{
                $sql = 'UPDATE human_report SET '
                    . ' human_report_upd_ts = CURRENT_TIMESTAMP '
                    .', human_report_title = ' . ($_SESSION['human_report_title'] ? Db::dbstring($_SESSION['human_report_title']) : 'NULL')
                    .', human_report_article = ' . ($_SESSION['human_report_article'] ? Db::dbstring($_SESSION['human_report_article']) : 'NULL')
                    . ' WHERE human_report_no = :human_report_no'
                ;echo $human_report_no;
                $stmt = $con->dbh->prepare($sql);
                $stmt->bindValue(':human_report_no', $human_report_no);
                $stmt->execute();
                return true;
            }
        }
    }

    /**
     * 公開状態の変更
     *
     * @param $openStatus
     *
     * @return void
     */
    public static function changeOpenStatus($table, $openStatus=null)
    {
        if(!is_null($openStatus)){
            if(is_numeric($openStatus)){
                if($openStatus == 1){
                    $status = 'true';
                }elseif($openStatus == 0){
                    $status = 'false';
                }
                $sql = 'UPDATE '.$table.'_report SET '
                    . $table.'_report_status = ' . $status
                    . ' WHERE '.$table.'_report_no = ' . $_REQUEST[$table.'_report_no'];
                $con = new Db;
                $con->connect();
                $stmt = $con->dbh->query($sql);
            }
            header('Location: '.$table.'_report_list');
            exit;
        }
    }

    /**
     * 公開状態の変更
     *
     * @param $openStatus
     *
     * @return void
     */
    public static function changeGeneralAffairsReportStatus($openStatus=null)
    {
        if(!is_null($openStatus)){
            if(is_numeric($openStatus)){
                if($openStatus == 1){
                    $status = 'true';
                }elseif($openStatus == 0){
                    $status = 'false';
                }
                $sql = 'UPDATE general_affairs_report SET '
                    . ' general_affairs_report_status = ' . $status
                    . ' WHERE general_affairs_report_no = ' . $_REQUEST['general_affairs_report_no'];
                $con = new Db;
                $con->connect();
                $stmt = $con->dbh->query($sql);
            }
            header('Location: general_affairs_report_list');
            exit;
        }
    }

    /**
     * リストページの取得
     *
     * @return bool
     */
    public static function generalAffairsReportList()
    {
        $sql = 'SELECT * FROM general_affairs_report ORDER BY general_affairs_report_no DESC';
        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->query($sql);
        $return = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(!$return){
            return false;
        }else{
            return $return;
        }
    }

    /**
     * リストページの取得
     *
     * @return bool
     */
    public static function getReportList($table)
    {
        $sql = 'SELECT * FROM '.$table.'_report ORDER BY '.$table.'_report_no DESC';
        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->query($sql);
        $return = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(!$return){
            return false;
        }else{
            return $return;
        }
    }

    /**
     * 人事通信画像リストページの取得
     *
     * @return mixed
     */
    public static function getHumanImgList()
    {
        $sql = 'SELECT * FROM human_report_img ORDER BY human_report_img_no DESC';
        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->query($sql);
        $return = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(!$return){
            return false;
        }else{
            return $return;
        }
    }

    /**
     * editページのデータ取得
     *
     * @param $mode
     * @param $no
     *
     * @return array
     */
    public static function getGeneralAffairsReportData($mode, $no)
    {
        if($mode == 'upd'){
            if(is_numeric($no)){
                $sql = 'SELECT * FROM general_affairs_report WHERE general_affairs_report_no = ' . $no;
                $con = new Db;
                $con->connect();
                $stmt = $con->dbh->query($sql);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
    }

    /**
     * editページのデータ取得
     *
     * @param $mode
     * @param $no
     *
     * @return array
     */
    public static function getReportData($table, $mode, $no)
    {
        if($mode == 'upd'){
            if(is_numeric($no)){
                $sql = 'SELECT * FROM '.$table.'_report WHERE '.$table.'_report_no = ' . $no;
                $con = new Db;
                $con->connect();
                $stmt = $con->dbh->query($sql);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
    }

    /**
     * general_affairs_reportの取得
     *
     * @return array
     */
    public static function getGeneralAffairsReport()
    {
        $col = 'general_affairs_report_no, general_affairs_report_title, general_affairs_report_description, general_affairs_report_article ';
        $sql = 'SELECT '. $col
            . ', CASE WHEN general_affairs_report_upd_ts IS NULL '
                . ' THEN general_affairs_report_ins_ts '
                . ' ELSE general_affairs_report_upd_ts '
                . ' END AS general_affairs_report_upd_ts '
            . ' FROM general_affairs_report '
            . ' WHERE general_affairs_report_status = true '
            . ' ORDER BY general_affairs_report_upd_ts DESC'
        ;

        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->query($sql);
        if($return = $stmt->fetchAll(PDO::FETCH_ASSOC)){
            return $return;
        }else{
            return false;
        }
    }

    /**
     * 人事通信の取得
     *
     * @return array
     */
    public static function getHumanReport()
    {
        $col = 'human_report_no, human_report_title, human_report_article ';
        $sql = 'SELECT '. $col
            . ', CASE WHEN human_report_upd_ts IS NULL '
                . ' THEN human_report_ins_ts '
                . ' ELSE human_report_upd_ts '
                . ' END AS human_report_upd_ts '
            . ' FROM human_report '
            . ' WHERE human_report_status = true '
            . ' ORDER BY human_report_upd_ts DESC'
        ;

        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->query($sql);
        if($return = $stmt->fetchAll(PDO::FETCH_ASSOC)){
            return $return;
        }else{
            return false;
        }
    }

    /**
     * edit→conf→doneタイプのDB保存
     *
     * @param $mode
     * @param $no
     *
     * @return bool
     */
    public static function saveLeaderReport($mode, $no=null)
    {
        if($mode != 'add' && $mode != 'upd'){
            return false;
        }

        if($mode == 'upd' && !is_numeric($no)){
            return false;
        }

        //xss対策しないでください

        //新規登録
        if($mode == 'add'){
            $sql = 'INSERT INTO leader_report('
                    . ' leader_report_title '
                    . ', leader_report_article '
                    .($_POST['leader_report_description'] ? ', leader_report_description ' : '')
                . ')VALUES('
                    .        Db::dbstring($_POST['leader_report_title'])
                    . ', ' . Db::dbstring($_POST['leader_report_article'])
                    .($_POST['leader_report_description'] ? ', ' . Db::dbstring($_POST['leader_report_description']) : '')
                . ')'
            ;

            $con = new Db;
            $con->connect();
            $stmt = $con->dbh->query($sql);
        }elseif($mode == 'upd'){
            //まずcontactNoが不正な値でないか確認
            $sql = 'SELECT leader_report_no FROM leader_report WHERE leader_report_no = :leader_report_no';
            $con = new Db;
            $con->connect();
            $stmt = $con->dbh->prepare($sql);
            $stmt->bindValue(':leader_report_no', $no);
            $stmt->execute();
            if(!$leader_report_no = $stmt->fetch()['leader_report_no']){
                return false;
            }else{
                $sql = 'UPDATE leader_report SET '
                    . ' leader_report_upd_ts = CURRENT_TIMESTAMP '
                    .', leader_report_title = ' . ($_POST['leader_report_title'] ? Db::dbstring($_POST['leader_report_title']) : 'NULL')
                    .', leader_report_article = ' . ($_POST['leader_report_article'] ? Db::dbstring($_POST['leader_report_article']) : 'NULL')
                    .', leader_report_description = ' . ($_POST['leader_report_description'] ? Db::dbstring($_POST['leader_report_description']) : 'NULL')
                    . ' WHERE leader_report_no = :leader_report_no'
                ;echo $leader_report_no;
                $stmt = $con->dbh->prepare($sql);
                $stmt->bindValue(':leader_report_no', $leader_report_no);
                $stmt->execute();
                return true;
            }
        }
    }

    /**
     * 公開状態の変更
     *
     * @param $openStatus
     *
     * @return void
     */
    public static function changeLeaderReportStatus($openStatus=null)
    {
        if(!is_null($openStatus)){
            if(is_numeric($openStatus)){
                if($openStatus == 1){
                    $status = 'true';
                }elseif($openStatus == 0){
                    $status = 'false';
                }
                $sql = 'UPDATE leader_report SET '
                    . ' leader_report_status = ' . $status
                    . ' WHERE leader_report_no = ' . $_REQUEST['leader_report_no'];
                $con = new Db;
                $con->connect();
                $stmt = $con->dbh->query($sql);
            }
            header('Location: leader_report_list');
            exit;
        }
    }

    /**
     * リストページの取得
     *
     * @return bool
     */
    public static function leaderReportList()
    {
        $sql = 'SELECT * FROM leader_report ORDER BY leader_report_no DESC';
        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->query($sql);
        $return = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(!$return){
            return false;
        }else{
            return $return;
        }
    }

    /**
     * editページのデータ取得
     *
     * @param $mode
     * @param $no
     *
     * @return array
     */
    public static function getLeaderReportData($mode, $no)
    {
        if($mode == 'upd'){
            if(is_numeric($no)){
                $sql = 'SELECT * FROM leader_report WHERE leader_report_no = ' . $no;
                $con = new Db;
                $con->connect();
                $stmt = $con->dbh->query($sql);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
    }

    /**
     * general_affairs_reportの取得
     *
     * @return array
     */
    public static function getLeaderReport()
    {
        $col = 'leader_report_no, leader_report_title, leader_report_description, leader_report_article ';
        $sql = 'SELECT '. $col
            . ', CASE WHEN leader_report_upd_ts IS NULL '
                . ' THEN leader_report_ins_ts '
                . ' ELSE leader_report_upd_ts '
                . ' END AS leader_report_upd_ts '
            . ' FROM leader_report '
            . ' WHERE leader_report_status = true '
            . ' ORDER BY leader_report_upd_ts DESC'
        ;

        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->query($sql);
        if($return = $stmt->fetchAll(PDO::FETCH_ASSOC)){
            return $return;
        }else{
            return false;
        }
    }

    /**
     * 週報コメント非表示の日付を取得
     *
     * @param $memberNo
     *
     * @return date
     */
    public static function getWeeklyReportResponseNotDate($memberNo)
    {
        if(is_numeric($memberNo)){
            $con = new Db;
            $con->connect();
            $stmt = $con->dbh->query('SELECT weekly_report_response_not_show_date FROM config WHERE member_no = ' . $memberNo);
            return ($stmt->fetch()['weekly_report_response_not_show_date'] ?? '1970-01-01');
        }
    }

    /**
     * 週報確認管理リストページの取得
     *
     * @return bool
     */
    public static function getResponseList($memberNo, $col=99, $order='DESC')
    {
        $COL = [
            99 => 'weekly_report_response_no',
            1 => 'target_member_no',
            2 => 'year_month',
            3 => 'weekly_report_response_ins_ts',
            4 => 'weekly_report_response_looked_ts',
        ];
        //悪意ある引数の改変が認められれば処理しない
        if(!$COL[$col]){
            return false;
        }
        if($order !== 'DESC' && $order !== 'ASC'){
            return false;
        }

        if(is_numeric($memberNo)){
            if($col == 2){
                $order = ' ORDER BY weekly_report_year_month '.$order.', weekly_report_week_num '.$order. ',weekly_report_response_no '.$order;
            }else{
                $order = 'ORDER BY '.$COL[$col].' IS NULL ASC, '.$COL[$col].' '.$order;
            }
            $sql = 'SELECT * FROM weekly_report_response LEFT JOIN('
                    . ' SELECT member_no AS target_member_no, member_name FROM member '
                . ')member USING(target_member_no) LEFT JOIN('
                    . ' SELECT weekly_report_no, weekly_report_week_num, weekly_report_year_month FROM weekly_report '
                . ')weekly_report USING(weekly_report_no) '
                . ' WHERE member_no = :member_no '
                . $order
            ;
            $con = new Db;
            $con->connect();
            $stmt = $con->dbh->prepare($sql);
            $stmt->bindValue(':member_no', $memberNo);
            $stmt->execute();
            $return = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if(!$return){
            return false;
        }else{
            return $return;
        }
    }

    /**
     * 週報確認管理リストページの取得
     * @param $no
     *
     * @return string
     */
    public static function deleteResponseList($no)
    {
        if($no){
            $no = explode(',', $no);
            array_pop($no);
            foreach($no as $k => $v){
                if(is_numeric($v)){
                    $sql = 'SELECT weekly_report_response_no FROM weekly_report_response WHERE weekly_report_response_no = '. $v;
                    $con = new Db;
                    $con->connect();
                    $stmt = $con->dbh->query($sql);
                    if($stmt->fetch()){
                        $sql = 'DELETE FROM weekly_report_response WHERE weekly_report_response_no = ' . $v
                            . ' AND member_no = '. $_SESSION['member_no']
                        ;
                        $con = new Db;
                        $con->connect();
                        $stmt = $con->dbh->query($sql);
                    }
                }else{
                    $error++;
                }
            }
            if($error){
                (new Mailer)->sendResponseErrMail();
                return $error;
            }
        }
    }

/**
     * 人事通信の画像アップロード
     *
     * @param $tmpFileName
     * @param $fileName
     *
     * @return mixed
     */
    public static function imgUploadHuman($tmpFileName, $fileName)
    {
        if (isset($tmpFileName)) {
            $sql = 'SELECT human_report_img_name FROM human_report_img WHERE human_report_img_name = ' . Db::dbstring($fileName);
            $con = new Db;
            $con->connect();
            $stmt = $con->dbh->query($sql);
            if(!$stmt->fetch()){
                $beforeDir = $tmpFileName;
                $imgName   = $fileName;
                $afterDir  = $_SERVER['DOCUMENT_ROOT'].'view/upload/human_img/'.$imgName;
                $chmod = $_SERVER['DOCUMENT_ROOT'].'view/upload/human_img';
                system('sudo chmod 777 '.$chmod);
                try {
                    if (move_uploaded_file($beforeDir, $afterDir)) {
                        system('sudo chmod 755 '.$chmod);

                        $sql      = 'INSERT INTO human_report_img(human_report_img_name) VALUES (:human_report_img_name);';
                        $stmt = $con->dbh->prepare($sql);
                        $stmt->bindValue(':human_report_img_name', $imgName);
                        $stmt->execute();
                        $msg = $fileName.' をアップロードしました。';
                    }else{
                        $msg = $fileName.' をアップロードできませんでした。';
                    }
                } catch (Exception $e) {
                    (new Mailer)->catchErrorMail($e);
                    logger($e);
                    die;
                }
                //実行者の記録
                Account::executeHistoryWriter(explode('.', basename(__FILE__))[0].'::'.__FUNCTION__.'($tmpFileName, $fileName)');
                return $msg;
            }else{
                return '同名ファイルが検出されました。処理は中断されました。';
            }
        }
    }

    /**
     * 画像削除
     *
     * @param $del
     * @param $ImgNo
     *
     * @return string
     */
    public static function imgDeleteHuman($del, $imgNo)
    {
        if ($del && is_numeric($imgNo)) {
            $con = new Db;
            $con->connect();
            $sql = 'SELECT human_report_img_name FROM human_report_img WHERE human_report_img_no = :human_report_img_no';
            $stmt = $con->dbh->prepare($sql);
            $stmt->bindValue(':human_report_img_no', $imgNo, PDO::PARAM_INT);
            $stmt->execute();
            $con->dbh->beginTransaction();
            if($img_name = $stmt->fetch()['human_report_img_name']){
                try {
                    $sql  = 'DELETE FROM human_report_img WHERE human_report_img_no = :human_report_img_no';
                    $stmt = $con->dbh->prepare($sql);
                    $stmt->bindValue(':human_report_img_no', $imgNo, PDO::PARAM_INT);
                    $stmt->execute();
                    $con->dbh->commit();
                    //ファイル削除のため取り扱い注意
                    //======================================================================
                    unlink('/var/www/html/xxx-report.xyz/view/upload/human_img/'.$img_name);
                    //======================================================================
                    //実行者の記録
                    Account::executeHistoryWriter(explode('.', basename(__FILE__))[0].'::'.__FUNCTION__.'($imgDelete, $imgNo)');
                    return '画像を削除しました。';
                } catch(Exception $e){
                    $con->dbh->rollback();
                    $_REQUEST['msg'] = '削除に失敗しました。';
                }
            }else{
                $_REQUEST['msg'] = 'その画像はデータベースに存在しませんでした。';
            }
        }
    }

    /**
     * 組織図の社員情報を取得
     *
     * @return array
     */
    public static function getOrganizationChart()
    {
        $con = new Db;
        $con->connect();
        $sql = 'SELECT member_no, member_name, member_belonging, member_level_type, member_status FROM member WHERE member_no < 99999990';
        $stmt = $con->dbh->prepare($sql);
        $stmt->execute();
        $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $all_member = $array;
        $result['front'] = array();
        $result['back'] = array(4 => $array[3]);
        unset($array[1], $array[3], $array[4], $array[0], $array[57], $array[121]);
        //まず退職者を消す　SQLで消すと配列番号おかしくなる
        foreach($array as $k => $v){
            if($v['member_status'] == false){
                unset($array[$k]);
            }
        }

        //原田直属
        foreach($array as $k => $v){
            if($v['member_belonging'] == 4/* && $v['member_no'] == 21*/){
                if($v['member_no'] == 21){
                    $result['back'][$v['member_belonging']]['sub_manager'][$v['member_no']] = $v;
                    unset($array[$k]);
                }elseif($v['member_level_type'] == 3){
                    $result['back'][$v['member_belonging']]['leader'][$v['member_no']] = $v;
                    unset($array[$k]);
                }else{
                    $result['back'][$v['member_belonging']]['member'][$v['member_no']] = $v;
                    unset($array[$k]);
                }
            }
        }

        //天野さん直属
        foreach($array as $k => $v){
            if($result['back'][4]['sub_manager'][$v['member_belonging']]){
                $result['back'][4]['sub_manager'][$v['member_belonging']]['leader'][$v['member_no']] = $v;
                unset($array[$k]);
            }
        }

        //リーダー直属
        foreach($array as $k => $v){
            if($result['back'][4]['sub_manager'][21]['leader'][$v['member_belonging']]){
                $result['back'][4]['sub_manager'][21]['leader'][$v['member_belonging']]['member'][$v['member_no']] = $v;
                unset($array[$k]);
            }elseif($result['back'][4]['leader'][$v['member_belonging']]){
                $result['back'][4]['leader'][$v['member_belonging']]['member'][$v['member_no']] = $v;
                unset($array[$k]);
            }

        }

        //デザインチーム
        foreach($array as $k => $v){
            if($v['member_level_type'] == 3 || $v['member_no'] == 13){
                $result['front']['leader'][$v['member_no']] = $v;
                unset($array[$k]);
            }
        }

        //デザインチームメンバー
        foreach($array as $k => $v){
            if($result['front']['leader'][$v['member_belonging']]){
                $result['front']['leader'][$v['member_belonging']]['member'][$v['member_no']] = $v;
                unset($array[$k]);
            }
        }

        //営業部
        $result['sales'][3] = $array[2];
        unset($array[2]);
        $result['sales'][3]['member'][26] = $array[25];
        unset($array[25]);
        $result['sales'][3]['member'][30] = $array[29];
        unset($array[29]);
        $result['sales'][3]['member'][111] = $array[110];
        unset($array[110]);
        $result['sales'][3]['member'][131] = $array[130];
        unset($array[130]);
        $result['sales'][3]['member'][161] = $array[160];
        unset($array[160]);

        foreach($array as $k => $v){
            $result['not'][$v['member_no']] = $v;
            unset($array[$k]);
        }

        $_REQUEST['result'] = $result;
        $_REQUEST['all_member'] = $all_member;
    }
}
