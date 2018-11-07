<?php

class Html
{
    /**
     * ページ共通ヘッド(ログインページ除く）
     */
    public static function header($device='pc')
    {
        require_once($_SERVER['DOCUMENT_ROOT'] . 'view/'.$device.'/header.html');
    }

    /**
     * ログインページ用ヘッド
     *
     */
    public static function indexHeader($device='pc')
    {
        require_once($_SERVER['DOCUMENT_ROOT'] . 'view/'.$device.'/index_header.html');
    }

    /**
     * footer を表示
     *
     */
    public static function footer($device='pc')
    {
        require_once($_SERVER['DOCUMENT_ROOT'] . 'view/'.$device.'/footer.html');
    }

    /**
     * body 部分を表示
     *
     * @param $request_file_name
     */
    public static function body($requestFileName, $device='pc')
    {
        if(preg_match('/[a-z]\/([a-z_]*)\.[html|php]/', $requestFileName, $file_name)){
            require_once($_SERVER['DOCUMENT_ROOT'] . "view/{$device}/{$file_name[1]}.html");
        }else{
            require_once($_SERVER['DOCUMENT_ROOT'] . "view/{$device}/404.html");
        }
    }

    /**
     * html特殊文字をエスケープ
     *
     * @param $str
     *
     * @return string
     */
    static public function h($str)
    {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    /**
     * 管理者権限がない場合、週報ページへ
     *
     * @param $member_no
     */
    public static function chkRedirect($memberNo)
    {
        if ($memberNo) {
            header('Location:weekly_report', true, 307);
        }
    }

    /**
     * 管理者用
     * 社員の週報を見る際にリクエストが正常でない場合エラーとして返す。
     *
     * @param $member_no
     * @param $yearMonth
     * @param $view_kind
     * @param $edit
     *
     * @return string
     */
    public static function chkRequest($member_no, $yearMonth, $viewKind, $edit)
    {
        if(LOOKCHECK_ADMIN_MEMBER[$_SESSION['member_no']] && is_numeric($_REQUEST['member_no'])){
            Account::setLookedReportTs($_SESSION['member_no'], $member_no);
        }
        // 週報分岐
        if (!empty($member_no) && !empty($yearMonth) && $viewKind == 1) {
            header("Location:weekly_report?member_no={$member_no}&weekly_report_year_month={$yearMonth}&edit={$edit}&team_flg={$_REQUEST['team_flg']}", true, 307);
        } elseif (!empty($member_no) && !empty($yearMonth) && $viewKind == 2) {
            header("Location:pay_off?member_no={$member_no}&time_card_year_month={$yearMonth}&edit={$edit}", true, 307);
        } elseif (!empty($member_no) && !empty($yearMonth) && $viewKind == 3) {
            header("Location:time_card?member_no={$member_no}&time_card_year_month={$yearMonth}&edit={$edit}", true, 307);
        } elseif (!empty($member_no) && !empty($yearMonth) && $viewKind == 4) {
            header("Location:site_info?member_no={$member_no}&site_info_year_month={$yearMonth}&edit={$edit}", true, 307);
        } elseif (empty($member_no) && empty($yearMonth) && empty($viewKind)) {
            return '';
        } else {
            return '年月を指定してください。';
        }
    }

    /**
     * 時間区切りのコロンを配列化
     *
     * @param $str
     *
     * @return array
     */
    public static function colonSeparate($str)
    {
        $return = explode(':', $str);
        return $return;
    }

    /**
     * トークンをセッションにセット
     */
    public static function getToken()
    {
        $token = hash('sha256', uniqid(mt_rand(), true));
        return $token;
    }

    /**
     * トークンをセッションから取得
     */
    public static function checkToken()
    {
        //セッションが空か生成したトークンと異なるトークンでPOSTされたときは不正アクセス
        if (empty($_SESSIOIN['token']) || ($_SESSION['token'] != $_POST['token'])) {
            echo '不正なPOSTが行われました', PHP_EOL;
            exit;
        }
    }

    /**
     * アクセス方法により条件分岐
     */
    public static function checkMethod()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            setToken();
        } else {
            checkToken();
        }
    }
}