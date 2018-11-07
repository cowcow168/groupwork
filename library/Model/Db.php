<?php
//2018-05-11権限確認　影響なし
class Db
{
    protected $dbh;

    /**
     * DBへ接続
     */
    public function connect()
    {
        try {
            $this->dbh = new PDO(MYSQL_DSN, MYSQL_DBUSER, MYSQL_DBPASS, [
                PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            $this->dbh->exec('set foreign_key_checks = 1');
        } catch (Exception $e) {
            logger($e);
        }
    }

    /**
     * DBへ接続(truncate使う時)
     */
    public function connectTruncate()
    {
        try {
            $this->dbh = new PDO(MYSQL_DSN, MYSQL_DBUSER, MYSQL_DBPASS, [
                PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            $this->dbh->exec('set foreign_key_checks = 0');
        } catch (Exception $e) {
            logger($e);
        }
    }

    /**
     * 週報のDBへ接続
     */
    public function connectWeeklyReport()
    {
        try {
            $this->dbh = new PDO(W_MYSQL_DSN, MYSQL_DBUSER, MYSQL_DBPASS, [
                PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            $this->dbh->exec('set foreign_key_checks = 1');
        } catch (Exception $e) {
            logger($e);
        }
    }

    /**
     * ID,PW の組み合わせが存在したらそれを返す
     *
     * @param $id
     * @param $pw
     *
     * @return mixed
     */
    public function selectIdPw($id, $pw)
    {
        $this->connect();
        $sql  = "SELECT * FROM member WHERE member_id = '{$id}' AND member_pass = '{$pw}';";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $return = $stmt->fetch();
        return $return;
    }

    /**
     * 週報システムのアカウントを検索
     *
     * @param $id
     *
     * @return mixed
     */
    public function getWeeklyReportAccount($id)
    {
        $con = new Db;
        $con->connectWeeklyReport();
        $sql  = 'SELECT * FROM member WHERE member_id = :id';
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $member = $stmt->fetch();

        // 所属するリーダーを取得
        $sql  = 'SELECT * FROM member WHERE member_no = :num';
        $stmt = $con->dbh->prepare($sql);
        if($member['member_belonging'] != 0){
            $stmt->bindValue(':num', $member['member_belonging']);
            $stmt->execute();
            $member['belonging_leader'] = $stmt->fetch()['member_name'];
        }
        logger($member);
        return $member;
    }

    /**
     * user_id を入力して社員情報を抽出
     *
     * @param $id
     *
     * @return mixed
     */
    public function selectId($id)
    {
        $con = new Db;
        $con->connectWeeklyReport();
        $sql  = 'SELECT * FROM member WHERE member_id = :id';
        $stmt = $con->dbh->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $member = $stmt->fetch();

        // 所属するリーダーを取得
        $sql  = 'SELECT * FROM member WHERE member_no = :num';
        $stmt = $con->dbh->prepare($sql);
        if($member['member_belonging'] != 0){
            $stmt->bindValue(':num', $member['member_belonging']);
            $stmt->execute();
            $member['belonging_leader'] = $stmt->fetch()['member_name'];
        }
        logger($member);
        return $member;
    }

    /**
     * user_id を入力して社員情報を抽出
     *
     * @param $id
     *
     * @return mixed
     */
    public static function allMemberCount()
    {
        $con = new Db;
        $con->connect();
        $sql  = 'SELECT member_no FROM member WHERE member_status = 1 AND member_no < 99999990 ORDER BY member_no DESC LIMIT 1';
        $stmt = $con->dbh->prepare($sql);
        $stmt->execute();
        $member = $stmt->fetch();
        return $member['member_no'];
    }

    /**
     * member_no を入力して社員情報を抽出
     *
     * @param $no
     *
     * @return mixed
     */
    public static function selectNo($no)
    {
        if($_SESSION['permission'] != 1 && $_SESSION['permission'] != 2){
            $and = ' AND member_status = 1 ';
        }
        $con = new Db;
        $con->connectWeeklyReport();
        $sql  = "SELECT member_no, member_id, member_name, member_kana, member_gender, member_level_type, member_team_type, member_belonging, member_start_ts, member_ins_ts, member_upd_ts, member_status FROM member WHERE member_no = '{$no}'".($and ? $and : '');
        $stmt = $con->dbh->prepare($sql);
        $stmt->execute();
        $return = $stmt->fetch(PDO::FETCH_ASSOC);
        return $return;
    }

    /**
     * アカウント状態が有効の社員を全て取得
     *
     * @param $table
     * @param $flg
     *
     * @return mixed
     */
    public function selectAll($table, $flg=null)
    {
        $con = new Db;
        $con->connect();
        if(!$flg){
            $sql  = 'SELECT * FROM '.$table.' WHERE member_status = 1;';
        }else{
            $sql = 'SELECT *, (SELECT @num := @num + 1 as no from (select @num := 0)AS dmy) AS count_number FROM ' .$table. ' WHERE member_status = 1 AND member_no < 99999990 AND member_no <> 1';
        }
        $stmt = $con->dbh->prepare($sql);
        $stmt->execute();
        $return = $stmt->fetchAll();;
        return $return;
    }

    /**
     * アカウント状態関係なく全社員を取得
     *
     * @param $table
     *
     * @return array
     */
    public function selectAbsMember($table)
    {
        $con = new Db;
        $con->connect();
        $sql  = 'SELECT *, (@num := @num + 1)AS count FROM ('
            . ' SELECT *, 1 AS sort FROM '.$table.' WHERE member_status = 1 '
            . ' UNION ALL '
            . ' SELECT *, 2 AS sort FROM '.$table.' WHERE member_status = 0 '
            . ' ORDER BY sort, member_no '
            . ')AS result '
            . ', (SELECT @num := 0) AS count_set '
        ;
        $stmt = $con->dbh->prepare($sql);
        $stmt->execute();
        $return = $stmt->fetchAll();
        return $return;
    }

    /**
     * アカウント状態が有効で、自分に所属しているメンバーを取得
     *
     * @param $table
     * @param $sessionMemberNo
     *
     * @return mixed
     */
    public static function selectAllBelonging($table, $sessionMemberNo)
    {
        $con = new Db;
        $con->connect();
        $sql  = 'SELECT * FROM '.$table.' WHERE member_status = 1 AND member_belonging = '.$sessionMemberNo.';';
        $stmt = $con->dbh->prepare($sql);
        $stmt->execute();
        $return = $stmt->fetchAll();
        return $return;
    }

    /**
     * 自分がサブマネ以上で、自分に所属しているリーダーのメンバーを取得
     *
     * @param $table
     * @param $belonging
     *
     */
    public static function selectAllBelongingMember($table, $belonging)
    {
        $i = 0;
        foreach($belonging as $key => $val){
            //リーダーだったら
            if($val['member_level_type'] == 3){
                $sort[$i] = Db::selectAllBelonging('member', $val['member_no']);
                foreach($sort[$i] as $k => $v){
                    $_REQUEST['members'][] = (array)$v;
                }
                $i++;
                //サブマネだったら
            }//elseif()
        }
    }

    /**
     * 自分がチームメンバーでチーム内週報共有フラグが立っている場合チーム情報を取得
     *
     */
    public static function selectAllTeamMember()
    {
        if($_SESSION['permission'] == 4){
            if($leaderMemberNo = Library::ConfigData()['member_no']){
                $con = new Db;
                $con->connect();
                $sql  = 'SELECT * FROM member '
                    . ' WHERE (member_no = ' . $leaderMemberNo
                    . ' OR member_belonging = '.$leaderMemberNo
                    . ') AND member_status = 1 '
                    . ' ORDER BY member_level_type '
                    . ', member_no';
                $stmt = $con->dbh->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll();

                foreach($result as $key => $val){
                    if($_SESSION['member_no'] != $val['member_no']){
                        $_REQUEST['members'][$key] = (array)$val;
                    }
                }
                $_REQUEST['team_flg'] = true;
            }
        }
    }

    /**
     * テーブル情報を条件付きで抜き出す
     *
     * @param $table
     * @param $col
     * @param $val
     *
     * @return mixed
     */
    public function selectWhere($table, $col, $val)
    {
        $con = new Db;
        $con->connect();
        $sql  = "SELECT * FROM {$table} WHERE {$col} = '{$val}'";
        $stmt = $con->dbh->prepare($sql);
        $stmt->execute();
        $return = $stmt->fetchAll();
        return $return;
    }

    /**
     * インジェクション対策をしない時のクォーテーション付与
     *
     * @param $str
     * @param $quote
     *
     * @return boolean
     */
    public static function dbstring($str, $quote=null)
    {
        if(!$str && !$quote){
            return false;
        }
        if(!$str && $quote){
            return $str;
        }

        if(is_numeric($str)){
            if($str == 0 && $quote){
                return 0;
            }elseif($str && $quote == null){
                return '\''.$str.'\'';
            }elseif($str && $quote){
                return $str;
            }else{
                echo 'err. function dbstring　ERROR_TYPE:A ';
                return false;
            }
        }elseif($str && $quote){
            $str = (int)($str);
            return $str;
        }elseif(!$str && $quote){
            return 0;
        }elseif(is_string($str) && $str != ''){
            return '\''.$str.'\'';
            //$strはここでは文字列の場合のみになる
        }elseif((!$str && !$quote) || $str == ''){
            echo 'err. function dbstring　ERROR_TYPE:B ';
            return false;
        }
    }

    /**
     * トランザクション開始
     *
     */
    public function begin()
    {
        $this->dbh->beginTransaction();
    }

    /**
     * トランザクション確定
     *
     */
    public function commit()
    {
        $this->dbh->commit();
    }

    public function selectHarada($sql)
    {
        $con = new Db;
        $con->connect();
        $stmt = $con->dbh->query($sql);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        return $member;
    }
}
/*
//バッチ処理中にこれらは必要ない
if(empty($batch)){
    // Admin
    require_once(LIBRARY.'Model/Admin.php');
}
*/

// 掲示板関係クラス
require_once(LIBRARY.'Model/Board.php');
//　チャット関係クラス
require_once(LIBRARY.'Model/Chat.php');
