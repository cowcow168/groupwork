<?php

class Account extends Db
{
    const PERMISSION = [
        1 => '管理者',
        2 => '準管理者',
        3 => 'リーダー',
        4 => 'メンバー',
        5 => 'サブマネ',
        6 => 'マネージャ',
        7 => '部長',
    ];

    /**
     * ログイン情報を照合し、セッションへ
     *
     * @param $userId
     * @param $userPw
     *
     * @return bool
     */
    public function login($userId, $userPw)
    {
        // ログイン試行時に、アクセス情報を記録
        logger('↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓');
        logger('Login Request From ...');
        logger($_SERVER['REMOTE_ADDR']);
        logger($_SERVER['HTTP_USER_AGENT']);
        logger('↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑');

        $_member = (new Db)->getWeeklyReportAccount($userId);
        if (!password_verify($userPw, $_member['member_pass']) || $_member['member_status'] != 1) {
            return false;
        }
        $_SESSION['_EBA_AUTH_TODOEBA_'] = hash('sha256', "{$userId}{$userPw}");
        $_SESSION['user_name']         = $userId;
        $_SESSION['permission']        = $_member['member_level_type'];
        $_SESSION['member_no']         = $_member['member_no'];
        $_SESSION['user_real_name']    = $_member['member_name'];
        $_SESSION['member_belonging']  = $_member['member_belonging'];
        $_SESSION['belonging_leader']  = $_member['belonging_leader'];
        $_SESSION['member_team_type']  = $_member['member_team_type'];
        logger('member_no:['.$_member['member_no'].'] is login');
        session_regenerate_id();
        header("Location: top");
    }

    /**
     * デバッグ用ログイン
     *
     * @param $memberNo
     */
    public function debugLogin($memberNo)
    {
        $result = (new Db)->selectNo($memberNo);
        $_member = (new Db)->selectId($result['member_id']);
        $_SESSION['_EBA_AUTH_TODOEBA_'] = hash('sha256', "{$userId}{$userPw}");
        $_SESSION['user_name']         = $_member['member_name'];
        $_SESSION['permission']        = $_member['member_level_type'];
        $_SESSION['member_no']         = $_member['member_no'];
        $_SESSION['user_real_name']    = $_member['member_name'];
        $_SESSION['member_belonging']  = $_member['member_belonging'];
        $_SESSION['belonging_leader']  = $_member['belonging_leader'];//Db->selectId->array_push
        $_SESSION['member_team_type']  = $_member['member_team_type'];
        if ($_SESSION['permission'] == 1 || $_SESSION['permission'] == 2) {
            header("Location:admin_top");
            exit;
        } elseif ($_SESSION['permission'] == 3) {
            header("Location:leader_top");
            exit;
        } else {
            header("Location:time_card");
            exit;
        }
    }

    /**
     * 最終ログイン更新
     *
     * @param $memberNo
     */
    public static function updLoginTs($memberNo)
    {
        // logger('[Page Request] member_no:'.$memberNo);
        $con = new Db();
        $con->connectWeeklyReport();
        $sql  = 'UPDATE member SET member_upd_ts = :now WHERE member_no = :member_no';
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':now', (new DateTime)->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(':member_no', $memberNo, PDO::PARAM_INT);
        $stmt->execute();
    }

    public static function chkAdminTopColor($memberNo)
    {
        $none = array(
            1 => '全権管理者',
            2 => '江畑社長',
            3 => '宮本さん',
            5 => '畑田さん',
            13 => '星さん',
            26 => '秋山さん',
            30 => '張さん',
            58 => '石丸さん',
            111 => '斉藤さん',
            122 => '北川さん',
            131 => 'サトエリ',
            99999994 => 'テストフロント2',
            99999995 => 'テストフロント1',
            99999996 => 'テストフロントリーダー',
            99999997 => 'テストサーバ2',
            99999998 => 'テストサーバ1',
            99999999 => 'テストサーバリーダー',
        );
        if($none[$memberNo]){
            return true;
        }

    }

    public function loginErr($err)
    {
        if ($err) {
            $return = '<span class="login_err">認証できませんでした。</span><br>';
            return $return;
        }
    }

    public function formChk($id, $pw)
    {
        if (empty($id) || empty($pw)) {
            $return = 1;
        }
        return $return;
    }

    /**
     * セッションチェック
     *
     * @param $id
     */
    public static function authChk($id)
    {
        $stmt = (new Db)->selectId($id);
        if (!$_SESSION['_EBA_AUTH_TODOEBA_'] === hash('sha256', "{$stmt['member_id']}{$stmt['member_pass']}") || empty($id)) {
            header("Location:index");
        }
    }

    /**
     * 権限チェック
     *
     * @param $id
     */
    public static function adminChk($id)
    {
        //$requestMember = (new Db)->selectId($id);
        //なんでか上のselectIdで値が取れないから直接SESSIONの値を使う。セキュリティ的には下策。
        if ($_SESSION['permission'] == 3 || $_SESSION['permission'] == 4) {
            header("Location:weekly_report");
        }
    }

    /**
     * リーダーチェック
     *
     * @param $sessionPermission
     */
    public static function leaderChk($sessionPermission)
    {
        // リーダー以上の権限でない場合、週報ページへ
        if ($sessionPermission == 4) {
            header("Location:weekly_report");
        }
    }

    /**
     * アカウント更新チェック(更新されていたらSESSIONの書き換え)
     *
     * @param $id
     * @param $permission
     * @param $memberNo
     * @param $memberName
     * @param $memberBelonging
     * @param $memberTeamType
     *
     */
    public static function sessionChangeChk($id, $permission, $memberNo, $memberName, $memberBelonging, $memberTeamType)
    {
        $con  = new Db;
        $con->connectWeeklyReport();
        try{
            $sql  =  ' SELECT * FROM member '
                  .  ' WHERE member_id = :id '
                  .  ' AND member_level_type = :member_level_type '
                  .  ' AND member_no = :member_no '
                  .  ' AND member_name = :member_name '
                  .  ' AND member_belonging = :member_belonging '
                  .  ' AND member_team_type = :member_team_type '
            ;
            $stmt = $con->dbh->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':member_level_type', $permission);
            $stmt->bindValue(':member_no', $memberNo);
            $stmt->bindValue(':member_name', $memberName);
            $stmt->bindValue(':member_belonging', $memberBelonging);
            $stmt->bindValue(':member_team_type', $memberTeamType);
            $stmt->execute();

            //アカウントの情報が更新されていたらSESSION情報を書き換える
            if(!$stmt->fetch()){
                $sql  = 'SELECT * FROM member WHERE member_no = :no';
                $stmt = $con->dbh->prepare($sql);
                $stmt->bindValue(':no', $memberNo);
                $stmt->execute();
                $member = $stmt->fetch();
                $session_change = (new Db)->selectId($member['member_id']);

                $_SESSION['user_name']         = $session_change['member_name'];
                $_SESSION['permission']        = $session_change['member_level_type'];
                $_SESSION['member_no']         = $memberNo;
                $_SESSION['user_real_name']    = $session_change['member_name'];
                $_SESSION['member_belonging']  = $session_change['member_belonging'];
                $_SESSION['belonging_leader']  = $session_change['belonging_leader'];
                $_SESSION['member_team_type']  = $session_change['member_team_type'];
            }
        } catch(Exception $e){
            self::executeHistoryWriter('Account::sessionChangeChk($id, $permission, $memberNo, $memberName, $memberBelonging, $memberTeamType)');
            Mailer::catchErrorMail($e);
        }
    }

    /**
     * 管理者・準管理者 以外は閲覧制限をかける
     *
     * @param $requestMemberNo
     * @param $sessionMemberNo
     * @param $permission
     */
    public static function chkPermission($requestMemberNo, $sessionMemberNo, $permission)
    {
        if(preg_match('/weekly_report/', basename($_SERVER['PHP_SELF']))){
            // リーダーかメンバーが自分以外のものを見る場合
            if (isset($requestMemberNo) && ($permission == 3 || $permission == 4) && $requestMemberNo != $sessionMemberNo) {
                if(is_numeric($requestMemberNo)){
                    if($requestMemberNo <= (int)Db::allMemberCount() || $requestMemberNo >= 99999990){
                        if(is_numeric($_REQUEST['harada_flg']) && $_REQUEST['harada_flg'] == 1){
                            $requestMember = (new Db)->selectNo(4);
                            return true;
                        }elseif(is_numeric($_REQUEST['team_flg']) && $_REQUEST['team_flg'] == 1){
                            //member_noが自分のチームには存在しない場合は自分の週報に戻す
                            Db::selectAllTeamMember();
                            foreach($_REQUEST['members'] as $key => $val){
                                if($val['member_no'] == $_REQUEST['member_no']){
                                    $requestMember = (new Db)->selectNo($requestMemberNo);
                                    return true;
                                }
                            }
                            header('Location: weekly_report');
                        }else{
                            //自分のチームのメンバーなら表示
                            $requestMember = (new Db)->selectNo($requestMemberNo);
                            $sql = 'SELECT * FROM member WHERE member_no = ' . $requestMember['member_no']
                                . ' OR member_belonging = ' . $requestMember['member_no'];
                            $con = new Db;
                            $con->connect();
                            $stmt = $con->dbh->prepare($sql);
                            $stmt->execute();
                            if(!$result = $stmt->fetchAll(PDO::FETCH_ASSOC)){
                                header('Location:weekly_report');
                            }else{
                                return true;
                            }
                        }
                        //不正なメンバーno
                        header('Location:weekly_report');
                    }
                }else{
                    //不正なgetパラメーター
                    header('Location:weekly_report');
                }
                if ($requestMember['member_belonging'] != $sessionMemberNo) {
                    header('Location:weekly_report');
                }
            }
        }else{
            //現在ページがweekly_reportじゃなければ
            // 他人リクエストがあり、リーダー以下で、自分以外のものを見る場合
            if (isset($requestMemberNo) && $permission >= 3 && $requestMemberNo != $sessionMemberNo) {
                $requestMember = (new Db)->selectNo($requestMemberNo);
                if ($requestMember['member_belonging'] != $sessionMemberNo) {
                    header('Location:weekly_report');
                }
            }
        }
    }

    public static function chkOverSubManagerPermission()
    {
        // サブマネ以上が自分の管理するリーダーのチームメンバーコメントに書き込み
        if($_SESSION['permission'] == 1
            ||
            $_SESSION['permission'] == 5
            ||
            $_SESSION['permission'] == 6
        ){
            $_REQUEST['members'] = Db::selectAllBelonging('member', $_SESSION['member_no']);
            Db::selectAllBelongingMember('member', $_REQUEST['members']);
            foreach($_REQUEST['members'] as $v){
                if($v['member_no'] == $_REQUEST['member_no']){
                    $_REQUEST['article_edit'] = 1;
                }
            }
        }
    }
    /**
     * ログアウトボタン押下チェック
     *
     * @param $val
     */
    public function logoutChk($val)
    {
        if ($val) {
            session_destroy();
            header("Location:index");
        }
    }

    /**
     * パスワード変更
     *
     * @param $memberNo
     * @param $oldPassword
     * @param $newPassword
     *
     * @return Exception|string
     */
    public static function changePassword($memberNo, $oldPassword, $newPassword)
    {
        // $old_hash = password_hash($oldPassword,PASSWORD_DEFAULT,['cost' => 10]);
        $con = new Db;
        $con->connect();
        $sql  = "SELECT member_pass FROM member WHERE member_no = {$memberNo};";
        $stmt = $con->dbh->prepare($sql);
        $stmt->execute();
        $tmp          = $stmt->fetch();
        $old_password = $tmp['member_pass'];
        if (!password_verify($oldPassword, $old_password)) {
            return 'パスワードが違います。';
        }
        $new_hash = password_hash($newPassword, PASSWORD_DEFAULT, ['cost' => 10]);
        $sql      = 'UPDATE member SET member_pass = :member_pass WHERE member_no = :member_no;';
        try {
            $con->dbh->beginTransaction();
            $stmt = $con->dbh->prepare($sql);
            $stmt->bindValue(':member_pass', $new_hash, PDO::PARAM_STR);
            $stmt->bindValue(':member_no', $memberNo, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            return $e;
            $con->dbh->rollBack();
        }
        $con->dbh->commit();
        return '更新しました。';
    }

    /**
     * アカウント新規登録
     *
     * @param $memberId
     * @param $memberName
     * @param $memberKana
     * @param $memberGender
     * @param $memberStart
     * @param $memberLevelType
     * @param $memberBeLonging
     * @param $memberTeamType
     *
     * @return array
     */
    public static function addMember($memberId, $memberName, $memberKana, $memberGender, $memberStart, $memberLevelType, $memberBelonging, $memberTeamType)
    {
        try {
            // パスワード生成
            $password = Account::makePass();
            $hash     = password_hash($password, PASSWORD_DEFAULT);

            $con = new Db;
            $con->connect();
            // トランザクション開始
            $con->dbh->beginTransaction();
            //member_noを手動で取る（テストユーザーで1億番くらいを取っているため）
            $sql = 'SELECT member_no FROM member WHERE member_no < 99999900 ORDER BY member_no DESC LIMIT 1';
            $stmt = $con->dbh->query($sql);
            $member_no = $stmt->fetch(PDO::FETCH_ASSOC)['member_no'];
            $sql  = 'INSERT INTO member ('
                    . '  member_no '
                    . ', member_id '
                    . ', member_pass '
                    . ', member_name '
                    . ', member_kana '
                    . ', member_gender '
                    . ', member_start_ts '
                    . ', member_level_type '
                    . ', member_belonging '
                    . ', member_team_type '
                    . ', member_unique_id '
                . ')VALUES('
                    . '  :member_no '
                    . ', :member_id '
                    . ', :member_pass '
                    . ', :member_name '
                    . ', :member_kana '
                    . ', :member_gender '
                    . ', :member_start_ts '
                    . ', :member_level_type '
                    . ', :member_belonging '
                    . ', :member_team_type '
                    . ', :member_unique_id '
                . ')'
            ;
            $stmt = $con->dbh->prepare($sql);
            $stmt->bindValue(':member_no', $member_no+1, PDO::PARAM_INT);
            $stmt->bindValue(':member_id', $memberId, empty($memberId) ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':member_pass', $hash);
            $stmt->bindValue(':member_name', $memberName, empty($memberName) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':member_kana', $memberKana, empty($memberKana) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':member_gender', $memberGender, empty($memberGender) ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':member_start_ts', $memberStart, empty($memberStart) ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':member_level_type', $memberLevelType, empty($memberLevelType) ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':member_belonging', $memberBelonging, PDO::PARAM_INT);
            $stmt->bindValue(':member_team_type', $memberTeamType, PDO::PARAM_INT);
            $stmt->bindValue(':member_unique_id', uniqid('', true), PDO::PARAM_INT);
            $stmt->execute();
            $con->dbh->commit();
            return [
                1,
                '登録が完了しました。',
                'パスワード：'.$password,
                'password' => $password,
                'hash' => $hash,
            ];
        } catch (Exception $e){
            logger($e);
            $con->dbh->rollBack();
            return [
                2,
                '',
                '登録できませんでした。<br>既に登録済みのIDかどうか、確認して下さい。',
                'password' => $password,
                'hash' => $hash,
            ];
        }

    }

    /**
     * アカウント更新
     *
     * @param $memberNo
     * @param $memberId
     * @param $memberName
     * @param $memberKana
     * @param $memberGender
     * @param $memberLevelType
     * @param $memberTeamType
     * @param $memberBelonging
     * @param $memberStatus
     *
     * @return string
     */
    public static function updateMember($memberNo, $memberId, $memberName, $memberKana, $memberGender, $memberStart, $memberLevelType, $memberTeamType, $memberBelonging, $memberStatus)
    {
        try {
            $con  = new Db;
            $con->connect();
            $sql  = ' UPDATE member SET '
                  . '   member_id = :member_id '
                  . ' , member_name = :member_name '
                  . ' , member_kana = :member_kana '
                  . ' , member_gender = :member_gender '
                  . ' , member_start_ts = :member_start_ts '
                  . ' , member_level_type = :member_level_type '
                  . ' , member_team_type = :member_team_type '
                  . ' , member_belonging = :member_belonging '
                  . ' , member_status = :member_status '
                  . ' WHERE member_no = :member_no; '
            ;
            $stmt = $con->dbh->prepare($sql);
            $stmt->bindValue(':member_id', $memberId, empty($memberId) ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':member_name', $memberName, empty($memberName) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':member_kana', $memberKana, empty($memberKana) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':member_gender', $memberGender, empty($memberGender) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':member_start_ts', $memberStart, empty($memberStart) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':member_level_type', $memberLevelType);
            $stmt->bindValue(':member_team_type', $memberTeamType);
            $stmt->bindValue(':member_belonging', $memberBelonging, PDO::PARAM_INT);
            $stmt->bindValue(':member_status', $memberStatus, PDO::PARAM_INT);
            $stmt->bindValue(':member_no', $memberNo, empty($memberNo) ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->execute();
            return [1, '更新が完了しました。'];
        } catch (Exception $e){
            logger($e);
            return [2, "更新に失敗しました。メールアドレスの重複など確認してください。"];
        }
    }

    /**
     * 週報提出状況
     *
     * @param $memberNo
     *
     * @return int|string
     */
    public static function chkSubmission($memberNo)
    {
        //退職済み
        $con = new Db;
        $con->connect();
        $sql  = 'SELECT member_status FROM member WHERE member_no = :member_no';
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':member_no', $memberNo);
        $stmt->execute();
        $status = $stmt->fetch(PDO::FETCH_ASSOC)['member_status'];
        if($status == 0){
            return 99;
        }

        // 指定社員が初回ログイン前であれば処理しない
        $con = new Db;
        $con->connect();
        $sql  = 'SELECT member_upd_ts FROM member WHERE member_no = :member_no';
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':member_no', $memberNo);
        $stmt->execute();
        $chk           = $stmt->fetch();
        $member_upd_ts = $chk['member_upd_ts'];
        if (!isset($member_upd_ts)) {
            return 6;
        }

        // 指定社員の最終提出日を参照
        $con = new Db;
        $con->connect();
        $sql  = 'SELECT MAX(weekly_report_sbm_ts) upd_ts FROM weekly_report WHERE member_no = :member_no';
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':member_no', $memberNo);
        $stmt->execute();
        $chk    = $stmt->fetch();
        $upd_ts = $chk['upd_ts'];
        if (!isset($upd_ts)) {
            return 0;
        }
        // 最終提出 日時
        $db_last_subm_ts = new DateTime($upd_ts);
        // 最終提出 時刻
        $last_subm_time = $db_last_subm_ts->format('H:i:s');
        // 最終提出 曜日
        $last_subm_weekday = $db_last_subm_ts->format('w');
        // 最終提出 週番号
        $last_subm_week_id = $db_last_subm_ts->format('W');

        // 操作 日時
        $now = new DateTime();
        // 操作 曜日
        $now_weekday = $now->format('w');
        // 操作 週番号
        $now_week_id = $now->format('W');

        // 操作日が金土日
        // 同じ週に提出された週報が金土日の場合 提出済み
        if (($now_weekday == 0 || $now_weekday >= 5) && ($last_subm_week_id == $now_week_id) && ($last_subm_weekday == 0 || $last_subm_weekday >= 5)) {
            $return = 1;
        } else {
            $return = 0;
        }

        // 操作日が月～木
        if (1 <= $now_weekday && $now_weekday <= 4) {
            // 先週の金土日に提出...提出済
            if (($last_subm_weekday == 0 || $last_subm_weekday >= 5) && ($last_subm_week_id == $now_week_id - 1)) {
                $return = 1;
                // 操作週の月曜日9時まで...提出済
            } elseif (($last_subm_week_id == $now_week_id) && ($last_subm_weekday == 1) && ($last_subm_time <= '09:00:00')) {
                $return = 1;
                // 操作週の月曜9時から火曜未満...当日遅れ
            } elseif (($last_subm_week_id == $now_week_id) && ($last_subm_weekday == 1) && ($last_subm_time > '09:00:00')) {
                $return = 2;
                // 操作週の火曜日...1日遅れ
            } elseif (($last_subm_week_id == $now_week_id) && ($last_subm_weekday == 2)) {
                $return = 3;
                // 操作週の水曜日...2日遅れ
            } elseif (($last_subm_week_id == $now_week_id) && ($last_subm_weekday == 3)) {
                $return = 4;
                // 操作週の木曜日...3日遅れ
            } elseif (($last_subm_week_id == $now_week_id) && ($last_subm_weekday == 4)) {
                $return = 5;
            }
        }
        return $return;
    }

    /**
     * 承認状態
     *
     * @param $memberNo
     *
     * @return mixed
     */
    public static function chkAgreement($memberNo)
    {
        $sql = ' SELECT weekly_report_authenticate_status FROM '
                . ' weekly_report '
            . ' WHERE '
                . '  member_no = :member_no '
            . ' ORDER BY '
                . '  weekly_report_sbm_ts DESC '
            . ' LIMIT 1 '
        ;
        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':member_no', $memberNo);
        $stmt->execute();
        return $stmt->fetch()['weekly_report_authenticate_status'];
    }

    /**
     * 今までの提出状況を集計
     *
     * @param $memberNo
     *
     * @return mixed
     */
    public static function sumSubmission($memberNo)
    {
        //このメソッドは絶対に今後バグる。週番号の取得の結果翌年にまたぐことがあるため。$w>52の辺りに注意
        $con = new Db;
        $con->connect();
        $sql = ' SELECT member_start_ts '
            . ' , weekly_report_sbm_ts '
            . ' , weekly_report_year_month '
            . ' , weekly_report_week_num '
            . ' , weekly_report_exemption_status '
            . ' FROM '
            . '   weekly_report report '
            . ' , member member '
            . ' WHERE '
            . '   member.member_no = report.member_no '
                . ' AND report.member_no = :member_no '
                . ' AND((report.weekly_report_week_num >= 3 AND report.weekly_report_year_month = ' . Db::dbstring('2017-11-01'). ')'
                    . ' OR (report.weekly_report_year_month >= ' . Db::dbstring('2017-12-01').')'
                . ' )AND report.weekly_report_sbm_ts >= '. Db::dbstring('2017-11-17 00:00:00')
        ;

        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':member_no', $memberNo);
        $stmt->execute();
        //現在入力されている週報のデータを取得
        if(!$report = $stmt->fetchAll(PDO::FETCH_ASSOC)){
            //週報が保存されていなければとりあえずstart_timeを取得
            $sql = ' SELECT member_start_ts FROM member WHERE member_no = :member_no ';
            $stmt = $con->dbh->prepare($sql);
            $stmt->bindValue(':member_no', $memberNo);
            $stmt->execute();
            $report = $stmt->fetchAll(PDO::FETCH_ASSOC);
            //保存がなくてstart_tsもなければグラフの演算の必要なし
            if(is_null($report[0]['member_start_ts'])){
                $return['delay']['']      = 0;
                $return['within']         = 0;
                $return['a_little_over']  = 0;
                $return['one_day_over']   = 0;
                $return['two_day_over']   = 0;
                $return['three_day_over'] = 0;
                $return['delay_count']    = 0;
                $return['not_submitted']  = 1;
                $return['report_message'] = '表示させるデータがありません。';
                return $return;
            }
        }
        //週報に保存はされているけど入場日が未入力の場合も0にしておく
        if(is_null($report[0]['member_start_ts'])){
            $return['delay']['']      = 0;
            $return['within']         = 0;
            $return['a_little_over']  = 0;
            $return['one_day_over']   = 0;
            $return['two_day_over']   = 0;
            $return['three_day_over'] = 0;
            $return['delay_count']    = 0;
            $return['not_submitted']  = 1;
            $return['report_message'] = '入場日が設定されていません。';
            return $return;
        }
        $y = (int)(date('Y', strtotime($report[0]['member_start_ts'])));
        $w = (int)(date('W', strtotime($report[0]['member_start_ts'])))+1;

        //週番号の調整　プラス1するから存在しない週番号になる可能性の考慮
        if((int)($w) > 52){
            $w -= 52;
        }
        //Wは一桁では認識できない
        if($w < 10){
            $w = '0'.$w;
        }
        //初回提出期限
        $week_no = date('Y-m-d 09:00:00', strtotime(date($y.'\W'.$w, strtotime($value['member_start_ts']))));

        //直近提出期限
        $week_now = date('Y-m-d 09:00:00', strtotime(date('Y\WW', strtotime('now'))));

        $report_int1 = new DateTime($week_now);
        $report_int2 = new DateTime($week_no);
        $__interval = $report_int1->diff($report_int2);
        $report_int3 = $__interval->format('%a');
        //最大で何回週報提出機会があったか
        $report_int = (int)($report_int3)/7;

        // 提出したレポートに遅れがあるかどうかの取得
        foreach ($report as $key => $value) {
            //提出ボタンを押下しない週報はカウントしたくない
            //if($_REQUEST['weekly_report_year_month'])
            // 入場日が登録されていない場合、カウントがおかしくなるのでメッセージを添えて0件表示にする。
            // 入場日が登録され次第、未提出数のカウントがされる。
            // 休職中でも提出ボタンは押すこととする　じゃないと休職中カウント省きが出来ない
            // todo 週の途中の月報分までカウントしてるのでそこの対応
            if (!is_null($value['weekly_report_sbm_ts'])){
                $_week1 = (string)date('YW', strtotime($value['weekly_report_year_month'])) + $value['weekly_report_week_num'] ;
                $_week3 = substr_replace($_week1, 'W', 4,0);
                $_week4   = strtotime($_week3);
                $_week  = date('Y-m-d 09:00:00', $_week4);

                $_day1 = new DateTime($value['weekly_report_sbm_ts']);
                $_day2 = new DateTime($_week);
                $_days = $_day1->diff($_day2)->days;
                $_time = $_day1->diff($_day2);

                if ($_day1 < $_day2){
                    $_delay[$key]['time'] = '-'.$_days.$_time->format(' %H:%I:%S');
                    if($value['weekly_report_exemption_status'] == 1){
                        $_delay[$key]['exemption'] = 1;
                    }
                }else{
                    $_delay[$key]['time'] = $_days.$_time->format(' %H:%I:%S');
                    if($value['weekly_report_exemption_status'] == 1){
                        $_delay[$key]['exemption'] = 1;
                    }
                }
            }
        }

        if($_delay){
            foreach ($_delay as $key => $value) {
                if (substr($value['time'], 0, 1) === '-'){
                    $_within[] = $value['time']; //時間通り提出

                }else{
                    $_pieces = explode(' ', $value['time']);
                    //何時から何時まで、ではなく何時間の遅れかを確認
                    if ($_pieces[0] == '0'){
                        if ( strtotime('00:00:00') <= strtotime($_pieces[1]) && strtotime($_pieces[1]) <= strtotime('15:00:01') ) {
                            if(!$value['exemption']){
                                $_a_little[] = $_pieces[0]; //当日遅れ
                            }
                        }else{
                            if(!$value['exemption']){
                                $_one_day[] = $_pieces[0]; //一日遅れ
                            }
                        }
                    }else if ($_pieces[0] == '1'){
                        if ( strtotime('00:00:00') <= strtotime($_pieces[1]) && strtotime($_pieces[1]) <= strtotime('15:00:01') ) {
                            if(!$value['exemption']){
                                $_one_day[] = $_pieces[0]; //一日遅れ
                            }
                        }else{
                            if(!$value['exemption']){
                                $_two_day[] = $_pieces[0]; //二日遅れ
                            }
                        }
                    }else if($_pieces[0] == '2'){
                        if ( strtotime('00:00:00') <= strtotime($_pieces[1]) && strtotime($_pieces[1]) <= strtotime('15:00:01') ) {
                            if(!$value['exemption']){
                                $_two_day[] = $_pieces[0]; //二日遅れ
                            }
                        }else{
                            if(!$value['exemption']){
                                $_three_day[] = $_pieces[0]; //三日遅れ
                            }
                        }
                    }else if($_pieces[0] == '3'){
                        if ( strtotime('00:00:00') <= strtotime($_pieces[1]) && strtotime($_pieces[1]) <= strtotime('15:00:01') ) {
                            if(!$value['exemption']){
                                $_three_day[] = $_pieces[0]; //三日遅れ
                            }
                        }
                    }else{
                        if(!$value['exemption']){
                            $_four_day[] = $_pieces[0]; //四日以上遅れ
                        }
                    }
                }
                //休職中フラグが立っていると上記分岐でカウントされず未提出扱いになるため
                if($value['exemption']){
                    $exemption_count++;
                }
            }
        }

        $return['within']         = count($_within);
        $return['a_little_over']  = count($_a_little);
        $return['one_day_over']   = count($_one_day);
        $return['two_day_over']   = count($_two_day);
        $return['three_day_over'] = count($_three_day);
        $return['delay_count']  = count($_four_day);
        $return['not_submitted'] = $report_int - (count($_within) + count($_a_little) + count($_one_day) + count($_two_day) + count($_three_day) + count($_four_day)) - $exemption_count;
        //週が変わって提出してない時は未提出がマイナスになってしまう対応
        if($return['not_submitted'] < 0){
            $return['not_submitted']++;
        }

        if($_four_day){
            sort($_four_day); //遅れ日数の昇順
            $delay = array_count_values($_four_day); //遅れ日数のカウント
        }

        if(empty($delay)){
            $return['delay'][''] = 0;
        }else{
            foreach ($delay as $key => $value) {
                $return['delay'][$key.' 日遅れ  '] = $value;
            }
        }
        return $return;
    }

    /*public static function sumSubmission($memberNo) 柿沼さんの　このコメントアウト内はしばらく消さないで。
    {
        // 初回レポート提出日時
        $con = new Db;
        $con->connect();
        $sql  = 'SELECT MIN(weekly_report_upd_ts) upd_ts FROM weekly_report WHERE member_no = :member_no';
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':member_no', $memberNo);
        $stmt->execute();
        $chk  = $stmt->fetch();
        $from = (new DateTime($chk['upd_ts']))->format('Y-m-d');

        // 最新レポート提出日時
        $con = new Db;
        $con->connect();
        $sql  = 'SELECT MAX(weekly_report_upd_ts) upd_ts FROM weekly_report WHERE member_no = :member_no';
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':member_no', $memberNo);
        $stmt->execute();
        $chk = $stmt->fetch();
        $to  = (new DateTime($chk['upd_ts']))->format('Y-m-d');

        // 初回レポート日から最新のレポート日まで、何提出分あるか計算
        $now = (new DateTime)->format('Y-m-d');

        // from 年月
        $date_sep_from = explode('-', $from);
        $from_month    = $date_sep_from[0].'-'.$date_sep_from[1];
        // from 週番号
        $from_week_id = (new DateTime($from))->format('W');
        // from 月末週番号
        $from_month_end_week_id = (new DateTime('last day of '.$from_month))->format('W');
        // from 曜日番号
        $from_weekday = (new DateTime($from))->format('w');

        // to 年月
        $date_sep_to = explode('-', $to);
        $to_month    = $date_sep_to[0].'-'.$date_sep_to[1];
        // to 週番号
        $to_week_id = (new DateTime($to))->format('W');
        // to 月初週番号
        $to_month_start_seek_id = (new DateTime('first day of '.$to_month))->format('W');
        // to 曜日番号
        $to_weekday = (new DateTime($to))->format('w');

//        // 操作日 年月
//        $date_sep_now = explode('-', $now);
//        $now_month    = $date_sep_now[0].'-'.$date_sep_now[1];
//        // 操作日 週番号
//        $now_week_id = (new DateTime($now))->format('W');
//        // 操作日 月初週番号
//        $now_month_start = (new DateTime('first day of '.$now_month))->format('W');

        $sum = 0;

        // 初回レポートと最新レポートが同月の場合
        if ($from_month == $to_month) {
            $sum += $to_week_id - $from_week_id + 1;
        } elseif ($from_month != $to_month) {

            // 初回と最新レポートが違う月の場合
            // from 月分加算
            $sum += $from_month_end_week_id - $from_week_id + 1;

            // loop が to と同月になるまでループ
            $loop_month = (new DateTime('first day of '.$from_month))->modify('+1 month')->format('Y-m');
            while ($loop_month != $to_month) {
                // loop 月初
                $start_week_id = date('W', strtotime('first day of '.$loop_month, (new DateTime('first day of '.$loop_month))->format('Y-m-d')));
                // loop 月末
                $end_week_id = (new DateTime('last day of '.$loop_month))->format('W');

                $sum        += $end_week_id - $start_week_id;
                $loop_month = (new DateTime($loop_month))->modify('+1 month')->format('Y-m');
            }

            // to 月分加算
            $sum += $to_week_id - $to_month_start_seek_id;
        }

        // 初回提出が月～木の場合にずれるので調節
        if ($from_weekday == 1 || $from_weekday == 2 || $from_weekday == 3 || $from_weekday == 4) {
            $sum++;
        }

        // 最終提出が月～木の場合にずれるので調節
        if ($to_weekday == 1 || $to_weekday == 2 || $to_weekday == 3 || $to_weekday == 4) {
            $sum--;
        }

        // 遅刻別にレポートを取得
        // within
        $con = new Db;
        $con->connect();
        $sql = " SELECT count(*) within FROM ( "
                . " SELECT * FROM ( "
                    . " SELECT "
                        . " * "
                        . " , DATE_FORMAT(weekly_report_sbm_ts, '%Y-%m-%d') Ymd "
                        . " , DATE_FORMAT(weekly_report_sbm_ts, '%H-%i-%s') His "
                        . " , DATE_FORMAT(weekly_report_sbm_ts, '%w') weekday_no "
                        . " , week(weekly_report_sbm_ts + INTERVAL 3 DAY, 7) week_id "
                    . " FROM "
                        . " weekly_report "
                . " ) whole "
                . " WHERE "
                    . " (weekday_no = 5 AND member_no = :member_no1) "
                    . " OR (weekday_no = 6 AND member_no = :member_no2) "
                    . " OR (weekday_no = 0 AND member_no = :member_no3) "
                    . " OR (weekday_no = 1 AND His BETWEEN '00:00:00' AND '09:00:00' AND member_no = :member_no4) "
            . " ) AS within_table "
        ;

        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':member_no1', $memberNo);
        $stmt->bindValue(':member_no2', $memberNo);
        $stmt->bindValue(':member_no3', $memberNo);
        $stmt->bindValue(':member_no4', $memberNo);
        $stmt->execute();
        $within = $stmt->fetch()['within'];

        // a_little_over
        $con = new Db;
        $con->connect();
        $sql = " SELECT count(*) a_little_over FROM ( "
             . " SELECT "
             . " * "
             . " FROM ( "
             . " SELECT "
             . " * "
             . " , DATE_FORMAT(weekly_report_sbm_ts, '%Y-%m-%d') Ymd "
             . " , DATE_FORMAT(weekly_report_sbm_ts, '%H-%i-%s') His "
             . " , DATE_FORMAT(weekly_report_sbm_ts, '%w') weekday_no "
             . " , week(weekly_report_sbm_ts + INTERVAL 3 DAY, 7) week_id "
             . " FROM "
             . " weekly_report "
             . " ) whole "
             . " WHERE "
             . " (weekday_no = 1 AND His BETWEEN '09:00:01' AND '23:59:59' AND member_no = :member_no) "
             . " ) a_little_over"
        ;

        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':member_no', $memberNo);
        $stmt->execute();
        $a_little_over = $stmt->fetch()['a_little_over'];

        // one_day_over
        $con = new Db;
        $con->connect();
        $sql = " SELECT count(*) one_day_over FROM ( "
             . " SELECT "
             . " * "
             . " FROM ( "
             . " SELECT "
             . " * "
             . " , DATE_FORMAT(weekly_report_sbm_ts, '%Y-%m-%d') Ymd "
             . " , DATE_FORMAT(weekly_report_sbm_ts, '%H-%i-%s') His "
             . " , DATE_FORMAT(weekly_report_sbm_ts, '%w') weekday_no "
             . " , week(weekly_report_sbm_ts + INTERVAL 3 DAY, 7) week_id "
             . " FROM "
             . " weekly_report "
             . " ) whole "
             . " WHERE "
             . " weekday_no = 2 AND member_no = :member_no "
             . " ) one_day_over "
        ;

        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':member_no', $memberNo);
        $stmt->execute();
        $one_day_over = $stmt->fetch()['one_day_over'];

        // two_day_over
        $con = new Db;
        $con->connect();
        $sql = " SELECT count(*) two_day_over FROM ( "
             . " SELECT "
             . " * "
             . " FROM ( "
             . " SELECT "
             . " * "
             . " , DATE_FORMAT(weekly_report_sbm_ts, '%Y-%m-%d') Ymd "
             . " , DATE_FORMAT(weekly_report_sbm_ts, '%H-%i-%s') His "
             . " , DATE_FORMAT(weekly_report_sbm_ts, '%w') weekday_no "
             . " , week(weekly_report_sbm_ts + INTERVAL 3 DAY, 7) week_id "
             . " FROM "
             . " weekly_report "
             . " ) whole "
             . " WHERE "
             . " weekday_no = 3 AND member_no = :member_no "
             . " ) two_day_over "
        ;

        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':member_no', $memberNo);
        $stmt->execute();
        $two_day_over = $stmt->fetch()['two_day_over'];

        // three_day_over
        $con = new Db;
        $con->connect();
        $sql = " SELECT count(*) three_day_over FROM ( "
             . " SELECT "
             . " * "
             . " FROM ( "
             . " SELECT "
             . " * "
             . " , DATE_FORMAT(weekly_report_sbm_ts, '%Y-%m-%d') Ymd "
             . " , DATE_FORMAT(weekly_report_sbm_ts, '%H-%i-%s') His "
             . " , DATE_FORMAT(weekly_report_sbm_ts, '%w') weekday_no "
             . " , week(weekly_report_sbm_ts + INTERVAL 3 DAY, 7) week_id "
             . " FROM "
             . " weekly_report "
             . " ) whole "
             . " WHERE "
             . " weekday_no = 4 AND member_no = :member_no "
             . " ) three_day_over "
        ;

        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':member_no', $memberNo);
        $stmt->execute();
        $three_day_over = $stmt->fetch()['three_day_over'];

        $return['within']         = $within;
        $return['a_little_over']  = $a_little_over;
        $return['one_day_over']   = $one_day_over;
        $return['two_day_over']   = $two_day_over;
        $return['three_day_over'] = $three_day_over;
        $return['not_submitted']  = $sum - ($within + $a_little_over + $one_day_over + $two_day_over + $three_day_over) < 0
            ? 0
            : $sum - ($within + $a_little_over + $one_day_over + $two_day_over + $three_day_over);

        return $return;
    }*/

    /**
     * リーダー一覧を返す
     *
     * @return mixed
     */
    public static function leaderList()
    {
        $con = new Db;
        $con->connect();
        $sql  = 'SELECT * FROM member WHERE member_level_type != 4';
        $stmt = $con->dbh->prepare($sql);
        $stmt->execute();
        $member_list = $stmt->fetchAll();
        return $member_list;
    }

    /**
     * ランダム８文字パスワードを作成
     *
     * @param int $length
     *
     * @return bool|string
     */
    public static function makePass($length = 8)
    {
        $str      = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!?~-_';
        $shuffled = str_shuffle($str);
        $pass     = substr($shuffled, 0, $length);
        return $pass;
    }

    /**
     * 週報をチーム内で共有する設定があったらチーム内全員のmember_noを取得する
     *
     * @return array $member_no
     */
    public static function getMemberReportOpen()
    {
        $con = new Db;
        $con->connect();
        $sql  = 'SELECT * FROM member WHERE member_level_type != 4';
        $stmt = $con->dbh->prepare($sql);
        $stmt->execute();
        $member_list = $stmt->fetchAll();
        return $member_list;
    }

    /**
     * 週報30人以上確認する人向けに最終アクセスをINSERTする
     *
     * @return string $last_looked_ts
     */
    public static function setLookedReportTs($memberNo, $targetMemberNo)
    {
        $targetMemberNo = (int)$targetMemberNo;
        if(LOOKCHECK_ADMIN_MEMBER[$memberNo]){
            $con = new Db;
            $con->connect();
            $sql = 'SELECT * FROM looked_report '
                . ' WHERE looked_report_member_no = :member_no1 '
                . ' AND looked_report_target_member_no = :target_member_no1 '
            ;
            $stmt = $con->dbh->prepare($sql);
            $stmt->bindValue(':member_no1', $memberNo,PDO::PARAM_INT);
            $stmt->bindValue(':target_member_no1', $targetMemberNo,PDO::PARAM_INT);
            $stmt->execute();
            if(!($stmt->fetch())){
                $sql  = 'INSERT INTO looked_report( '
                        . ' looked_report_member_no '
                        . ', looked_report_target_member_no '
                    . ')VALUES('
                        . ' :member_no2 '
                        . ', :target_member_no2 '
                    . ')'
                ;
                $stmt = $con->dbh->prepare($sql);
                $stmt->bindValue(':member_no2', $memberNo,PDO::PARAM_INT);
                $stmt->bindValue(':target_member_no2', $targetMemberNo,PDO::PARAM_INT);
                $stmt->execute();
            }else{
                $sql = 'UPDATE looked_report SET '
                    . ' looked_report_last_access_ts = CURRENT_TIMESTAMP '
                    . ' WHERE looked_report_member_no = :member_no3 '
                    . ' AND looked_report_target_member_no = :target_member_no3 '
                ;
                $stmt = $con->dbh->prepare($sql);
                $stmt->bindValue(':member_no3', $memberNo,PDO::PARAM_INT);
                $stmt->bindValue(':target_member_no3', $targetMemberNo,PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }

    /**
     * 週報を30人以上程度確認する人向けに最終アクセス時間の取得
     * 配列番号はlooked_report_target_member_no
     * @return array looked_report
     */
    public static function getLookedReportTs($memberNo)
    {
        if(LOOKCHECK_ADMIN_MEMBER[$memberNo]){
            $con = new Db;
            $con->connect();
            $sql  = 'SELECT * FROM looked_report '
                . ' WHERE looked_report_member_no = :member_no';
            $stmt = $con->dbh->prepare($sql);
            $stmt->bindValue(':member_no', $memberNo);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach($result as $key => $val){
                $return[$val['looked_report_target_member_no']] = $val;
                unset($return[$val['looked_report_target_member_no']]['looked_report_target_member_no']);
            }
            return $return;
        }
    }

    /**
     * 週報最終アクセス日時によりtdの色を変更する
     *
     * @return string css-color
     */
    public static function getLastAccessCheckColor($date)
    {
        $day = ((strtotime(date('Y-m-d')) - strtotime(date('Y-m-d', strtotime($date)))) / 60 / 60 / 24);
        if($day < 4 && $day >= 0){
            return 'white';
        }elseif($day < 6){
            return 'yellow';
        }else{
            return 'rgba(255, 100, 100, 1)';
        }
    }

    /**
     * 社員数の取得
     *
     * @return array(db->member)
     */
    public static function getNumberOfEmployees()
    {
        $status = 'WHERE member_status = 1 ';
        $count = 'COUNT(member_team_type)';
        $debugteam = ' AND member_no < 99999990 ';
        $con = new Db;
        $con->connect();
        $sql  = 'SELECT '
            //取締役数
            . '(SELECT '.$count.' FROM member '
                .$status
                . ' AND member_team_type IN'.MEMBER_EMPLOYEES_GROUP['director']
                .$debugteam
            . ')AS director, '
            //社員数
            . '(SELECT '.$count.' FROM member '
                .$status
                . ' AND member_team_type IN'.MEMBER_EMPLOYEES_GROUP['employee']
                . ' AND member_no NOT IN'.MEMBER_EMPLOYEES_GROUP['parttime']
                .$debugteam
            . ')AS employee, '
            //パート・バイト男性
            . 'COALESCE((SELECT '.$count.' FROM member '
                .$status
                . ' AND member_gender = 1 '
                . ' AND member_team_type IN'.MEMBER_EMPLOYEES_GROUP['parttime']
                .$debugteam
            . '), 0)AS male_parttime, '
            //パート・バイト女性
            . 'COALESCE((SELECT '.$count.' FROM member '
                .$status
                . ' AND member_gender = 2 '
                . ' AND member_team_type IN'.MEMBER_EMPLOYEES_GROUP['parttime']
                .$debugteam
            . '), 0)AS female_parttime, '
            //デバッグメンバー
            . 'COALESCE((SELECT COUNT(*) FROM member '
                .$status
                . ' AND member_team_type NOT IN'.MEMBER_EMPLOYEES_GROUP['debug']
                .$debugteam
            . '), 0)AS all_employee, '
            //男性
            . '(SELECT '.$count.' FROM member '
                .$status
                . ' AND member_gender = 1 '
                . ' AND member_team_type NOT IN'.MEMBER_EMPLOYEES_GROUP['director']
                . ' AND member_team_type NOT IN'.MEMBER_EMPLOYEES_GROUP['parttime']
                . ' AND member_team_type NOT IN'.MEMBER_EMPLOYEES_GROUP['debug']
                .$debugteam
            . ')AS male, '
            //女性
            . '(SELECT '.$count.' FROM member '
                .$status
                . ' AND member_gender = 2 '
                . ' AND member_team_type NOT IN'.MEMBER_EMPLOYEES_GROUP['director']
                . ' AND member_team_type NOT IN'.MEMBER_EMPLOYEES_GROUP['parttime']
                . ' AND member_team_type NOT IN'.MEMBER_EMPLOYEES_GROUP['debug']
                .$debugteam
            . ')AS female '
        ;
        $stmt = $con->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 保存時の不正監視
     *
     * @return boolean
     *
     * @param $methodName
     *
     */
    public static function executeHistoryWriter($methodName)
    {
        if(is_numeric($_SESSION['member_no']) && is_numeric($_REQUEST['member_no'])){
            $judge = 1;
        }
        $con = new Db;
        $con->connect();
        $sql  = 'INSERT INTO execute_history ('
                . ' execute_history_uri '
                . ', execute_history_method_name '
                . ', execute_history_referer '
                . ', execute_history_remote_addr '
                . ', execute_history_ua '
                . ', execute_history_member_no '
                . ', execute_history_target_member_no '
                . ', execute_history_user_auth '
                . ', execute_history_judge'
            . ')VALUES('
                . '  :uri '
                . ', :method '
                . ', :ref '
                . ', :addr '
                . ', :ua '
                . ', :member_no '
                . ', :target_member_no '
                . ', :user_auth '
                . ', ' . ($judge == 1 ? 'true' : 'false')
            . ')'
        ;
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':uri', $_SERVER['REQUEST_URI']);
        $stmt->bindValue(':method', $methodName);
        $stmt->bindValue(':ref', $_SERVER['HTTP_REFERER']);
        $stmt->bindValue(':addr', $_SERVER['REMOTE_ADDR']);
        $stmt->bindValue(':ua', $_SERVER['HTTP_USER_AGENT']);
        $stmt->bindValue(':member_no', $_SESSION['member_no']);
        $stmt->bindValue(':target_member_no', $_REQUEST['member_no']);
        $stmt->bindValue(':user_auth', $_SERVER['PHP_AUTH_USER']);
        $stmt->execute();
        if($judge == 'true'){
            return true;
        }else{
            //exit('不正な値が検出されました。一度ログアウトしてもう一度ログインし直してください。というテスト中です。これ見えてる方ゴメンナサイ一瞬我慢してください。');
        }
    }

    /**
     * uniqueIdのセット
     *
     * @param $memberNo
     */
    public function setUniqueId($memberNo)
    {
        /*if(!harada()){
            $id = uniqid('', true);
            $con = new Db;
            $con->connect();
            $sql  = 'UPDATE member SET member_unique_id = '. Db::dbstring($id). ' WHERE member_no = '. $memberNo;
            $stmt = $con->dbh->query($sql);
        }else{*/
            $id = uniqid('', true);
            $con = new Db;
            $con->connect();
            $sql = 'SELECT member_unique_id FROM member WHERE member_no = ' . $memberNo;
            $stmt = $con->dbh->query($sql);
            if($member_unique_id = $stmt->fetch()['member_unique_id']){
                $sql = 'UPDATE member SET member_unique_id = ' . Db::dbstring($id) . ' WHERE member_no = ' . $memberNo;
                $con->dbh->query($sql);
                $sql = 'INSERT INTO unique_id_history ('
                        . '  member_no '
                        . ', member_unique_id '
                    . ')VALUES('
                        . $memberNo
                        . ', ' . Db::dbstring($member_unique_id)
                    . ')'
                ;
                $con->dbh->query($sql);
            }
        //}
    }

    /**
     * uniqueIdの取得
     *
     * @param $memberNo
     *
     * @return $string
     */
    public static function getUniqueId($memberNo)
    {
        if(!is_numeric($memberNo)){
            return false;
        }
        $con = new Db;
        $con->connect();
        $sql = 'SELECT member_unique_id FROM member WHERE member_no = :member_no';
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':member_no', $memberNo);
        $stmt->execute();
        if(!$result = $stmt->fetch()['member_unique_id']){
            self::setUniqueId($memberNo);
            return $stmt->fetch()['member_unique_id'];
        }
        return $result;
    }

    /**
     * unique_id_historyの取得
     *
     * @param $memberNo
     * @param $uniqueId
     *
     * @return $string
     */
    public static function getUniqueIdHistory($memberNo, $uniqueId)
    {
        if(!is_numeric($memberNo)){
            return false;
        }
        $con = new Db;
        $con->connect();
        $sql = 'SELECT member_unique_id FROM unique_id_history WHERE member_no = :member_no AND member_unique_id = :member_unique_id';
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':member_no', $memberNo);
        $stmt->bindValue(':member_unique_id', $uniqueId);
        $stmt->execute();
        if($stmt->fetch()){
            return true;
        }
    }

    /**
     * 保存が認められている人か確認
     *
     * @param $memberNo
     * @param $uniqueId
     */
    public static function chkUniqueId($memberNo, $uniqueId=null)
    {
        /*if(!harada()){
            if(!is_numeric($memberNo) || is_null($uniqueId)){
                return false;
            }
            if($uniqueId == self::getUniqueId($memberNo)){
                return true;
            }
        }else{*/
            if(!is_numeric($memberNo) || is_null($uniqueId)){
                return false;
            }
            if($uniqueId == self::getUniqueId($memberNo)){
                return true;
            }else{
                //一致しない場合は既にuniqueIdが変更されたか不正アクセス
                if(self::getUniqueIdHistory($memberNo, $uniqueId)){
                    return true;
                }
            }
            return false;
        //}
    }

    /**
     * 保存期間を過ぎた人の救済措置情報を記録する
     *
     * @param $memberNo
     *
     * @return str
     */
    public static function saveDebugPermission($memberNo)
    {
        if(!is_numeric($memberNo)){
            return false;
        }
        $con = new Db;
        $con->connect();
        $sql = 'SELECT save_permission_no FROM save_permission '
            . ' WHERE save_permission_member_no = :member_no '
            . ' AND now() BETWEEN '
                . ' DATE_FORMAT(save_permission_ins_ts, '.Db::dbstring('%Y-%m-%d %H:%i:%s').')'
                . ' AND DATE_FORMAT(save_permission_ins_ts + INTERVAL 7 DAY, '.Db::dbstring('%Y-%m-%d %H:%i:%s').')'
        ;
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':member_no', $memberNo, PDO::PARAM_INT);
        $stmt->execute();
        if(!$hoge = $stmt->fetch()){
            try{
                $sql = 'INSERT INTO save_permission(save_permission_member_no)VALUES(:member_no)';
                $stmt = $con->dbh->prepare($sql);
                $stmt->bindValue(':member_no', $memberNo);
                $stmt->execute();
                $return = '保存できるようになりました。';
            }catch(Exception $e){
                (new Mailer())->catchErrorMail($e);
                $return = 'DBエラーが発生しました。処理は完了していません。';
            }
        }else{
            $return = 'まだ保存期間が残っています。';
        }
        return $return;
    }

    /**
     * add_memberのchecked
     *
     * @param $memberNo
     */
    public static function chkRadioStatus($memberNo, $memberGender)
    {
        if(!is_numeric($memberNo)){
            return false;
        }
        $con = new Db;
        $con->connect();
        $sql = 'SELECT member_gender FROM member '
            . ' WHERE member_no = :member_no '
            . ' AND member_status = true ';
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':member_no', $memberNo, PDO::PARAM_INT);
        $stmt->execute();
        if($stmt->fetch()['member_gender'] == $memberGender){
            echo ' checked';
        }
    }

    /**
     * メンバーの最終週報提出時間の取得
     *
     * @param $memberNo
     */
    public static function getLastSubmissionDate($memberNo)
    {
        if(!is_numeric($memberNo)){
            return false;
        }
        $con = new Db;
        $con->connect();
        $sql = 'SELECT weekly_report_sbm_ts FROM weekly_report '
            . ' WHERE member_no = :member_no '
            . ' AND weekly_report_sbm_ts IS NOT NULL '
            . ' ORDER BY weekly_report_sbm_ts DESC LIMIT 1';
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':member_no', $memberNo, PDO::PARAM_INT);
        $stmt->execute();
        if($date = $stmt->fetch()){
            echo date('Y年m月d日 H時i分s秒', strtotime($date['weekly_report_sbm_ts'])). ' '. DAY_OF_WEEK[date('w', strtotime($date['weekly_report_sbm_ts']))].'曜日';
        }
    }

    /**
     * リーダー以上かどうかを判別
     * @return bool
     */
    public static function chkLeaderPermission($levelType)
    {
        $LEVEL = array(1, 2, 3);
        if($LEVEL[$levelType]){
            return true;
        }
    }

    /**
     * 退職者やテストを除いた全員のnoとname
     * @return array
     */
    public static function getAllMember()
    {
        $con = new Db;
        $con->connect();
        $sql = 'SELECT member_no, member_name FROM member '
            . ' WHERE member_status = true '
            . ' AND member_no < 99999990 '
            . ' AND member_team_type NOT IN '.MEMBER_EMPLOYEES_GROUP['allmember']
        ;
        $stmt = $con->dbh->query($sql);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'member_name', 'member_no');
    }

    /**
     * noから名前を取る
     * @return array
     */
    public static function getMemberName($memberNo)
    {
        if(!is_numeric($memberNo)){
            return false;
        }
        $con = new Db;
        $con->connect();
        $sql = 'SELECT member_name FROM member '
            . ' WHERE member_no = ' . $memberNo
        ;
        $stmt = $con->dbh->query($sql);
        return $stmt->fetch()['member_name'];
    }




    /**
     * ログインユーザーの更新権限の有無の判定
     *
     * @param $createMemberNo 作成者
     * @param $boardCategoryNo カテゴリNo
     * ↑指定することで、カテゴリNoを主キーに持つカテゴリの更新権限の有無を判定
     * （カテゴリはトピック数が1個以上存在する場合は削除出来ないため）
     *
     * @return bool
     */
    public function chkUpdatePermission($createMemberNo, $boardCategoryNo = null)
    {
        $board = new Board;

        // デフォルトカテゴリは削除出来ない
        if(!empty(array_search($board->getBoardCategory($boardCategoryNo)['BOARD_CATEGORY_NAME'], $board::DEFAULT_CATEGORY))){
            return false;
        }

        // 管理者、準管理者は編集可
        if($_SESSION['permission'] == 1 || $_SESSION['permission'] == 2){
            return true;
        }

        // カテゴリNoが引数に入っている場合、トピック数が10個以上存在する場合は削除出来ない
        if(!empty($boardCategoryNo)){

            // カテゴリに紐づくトピック数を検索
            $board_topic_num = count($board->getBoardTopic($boardCategoryNo));

            if($board_topic_num >= $board::DELETABLE_CATEGORY_TOPIC_MAX){
                return false;
            }
        }

        // 作成者本人は編集可
        if($_SESSION['member_no'] == $createMemberNo){
            return true;
        }

        return false;
    }

    /**
     * 上位者か否かの判定
     *
     * @param $memberNo 比較元の社員No
     * @param $targetMemberNo 比較対象の社員No
     *
     * @return bool
     * （比較元の社員が比較対象の社員の上位者ならtrue、
     * そうでない場合はfalseを返す）
     */
    public function chkUpperPosition($memberNo, $targetMemberNo)
    {
        $con = new Db;
        $return = $con->selectNo($targetMemberNo);

        // 結果を初期化
        $result = false;

        // 比較対象の社員がマネージャーである場合までループ処理
        while(!empty($return) && in_array($return['permission'], array(3, 5, 6))){

            // 比較対象の社員の上位者に比較元の社員がいた場合、trueを返してループ処理終了
            if($memberNo == $return['member_belonging']){
                $result = true;
                break;

                // さらに上位者を取得
            }else{
                $return = $con->selectNo($return['member_belonging']);
            }
        }

        return $result;
    }
}

