<?php
$batch = 1;
require_once('/var/www/html/xxx-report.xyz/library/const.php');
require_once('/var/www/html/xxx-report.xyz/library/Model/Db.php');
/**
 * 祝日かどうか判定できるDateTime拡張クラス
 * usage: $holidayDateTime->holiday();
 */
// url : http://qiita.com/chiyoyo/items/539dc2840a1b70a8e2c3
class HolidayDateTime extends DateTime
{
    /** 祝日一覧 */
    // 種別：
    //   fixed=日付固定
    //   happy=指定の週の月曜日
    //   spring=春分の日専用
    //   autumn=秋分の日専用
    private static $holidays = [
        // 種別, 月, 日or週, 開始年, 終了年, 祝日名
        ['fixed',   1,  1, 1949, 9999, '元日'],
        ['fixed',   1, 15, 1949, 1999, '成人の日'],
        ['happy',   1,  2, 2000, 9999, '成人の日'],
        ['fixed',   2, 11, 1967, 9999, '建国記念の日'],
        ['spring',  3,  0, 1949, 9999, '春分の日'],
        ['fixed',   4, 29, 1949, 1989, '天皇誕生日'],
        ['fixed',   4, 29, 1990, 2006, 'みどりの日'],
        ['fixed',   4, 29, 2007, 9999, '昭和の日'],
        ['fixed',   5,  3, 1949, 9999, '憲法記念日'],
        ['fixed',   5,  4, 1988, 2006, '国民の休日'],
        ['fixed',   5,  4, 2007, 9999, 'みどりの日'],
        ['fixed',   5,  5, 1949, 9999, 'こどもの日'],
        ['happy',   7,  3, 2003, 9999, '海の日'],
        ['fixed',   7, 20, 1996, 2002, '海の日'],
        ['fixed',   8, 11, 2016, 9999, '山の日'],
        ['autumn',  9,  0, 1948, 9999, '秋分の日'],
        ['fixed',   9, 15, 1966, 2002, '敬老の日'],
        ['happy',   9,  3, 2003, 9999, '敬老の日'],
        ['fixed',  10, 10, 1966, 1999, '体育の日'],
        ['happy',  10,  2, 2000, 9999, '体育の日'],
        ['fixed',  11,  3, 1948, 9999, '文化の日'],
        ['fixed',  11, 23, 1948, 9999, '勤労感謝の日'],
        ['fixed',  12, 23, 1989, 9999, '天皇誕生日'],
        //以下、1年だけの祝日
        ['fixed',   4, 10, 1959, 1959, '皇太子明仁親王の結婚の儀'],
        ['fixed',   2, 24, 1989, 1989, '昭和天皇の大喪の礼'],
        ['fixed',  11, 12, 1990, 1990, '即位礼正殿の儀'],
        ['fixed',   6,  9, 1993, 1993, '皇太子徳仁親王の結婚の儀'],
    ];

   /**
    * 祝日を取得
    */
   public function holiday()
   {
       // 設定された休日チェック
       $result = $this->checkHoliday();
       if ($result !== false) return $result;

       // 振替休日チェック
       $result = $this->checkTransferHoliday();
       if ($result !== false) return $result;

       // 国民の休日チェック
       $result = $this->checkNationalHoliday();

       return $result;
   }

    /**
     * 設定された休日のみチェック
     * 国民の休日と振替休日はチェックしない
     */
    public function checkHoliday()
    {
        $result = false;

        // 全ての祝日を判定
        foreach(self::$holidays as $holiday) {
            list($method, $month, $day, $start, $end, $name) = $holiday;
            $method .= 'Holiday';
            $result = $this->$method($month, $day, $start, $end, $name);
            if ($result) {
                return $result;
            }
        }
        return $result;
    }

    /**
     * 振替休日チェック
     */
    public function checkTransferHoliday()
    {
        // 施行日チェック
        $d = new DateTime('1973-04-12');
        if ($this < $d) return false;

        // 当日が祝日の場合はfalse
        if ($this->checkHoliday()) return false;

        $num = ($this->year <= 2006) ? 1 : 7; //改正法なら最大7日間遡る

        $d = clone $this;
        $d->modify('-1 day');
        $isTransfer = false;
        for ($i = 0 ; $i < $num ; $i++) {
            if ($d->checkHoliday()) {
                // 祝日かつ日曜ならば振替休日
                if ($d->dayOfWeek == 0) {
                    $isTransfer = true;
                    break;
                }
                $d->modify('-1 day');
            } else {
                break;
            }
        }
        return $isTransfer ? '振替休日' : false;
    }

    /**
     * 国民の休日かどうかチェック
     */
    public function checkNationalHoliday()
    {
        // 施行日チェック
        $d = new DateTime('2003-01-01');
        if ($this < $d) return false;

        $before = clone $this;
        $before->modify('-1 day');
        if ($before->checkHoliday() === false) return false;

        $after = clone $this;
        $after->modify('+1 day');
        if ($after->checkHoliday() === false) return false;

        return '国民の休日';
    }

    /**
     * 固定祝日かどうか
     */
    private function fixedHoliday($month, $day, $start, $end, $name)
    {
        if ($this->isWithinYear($start, $end) === false) return false;
        if ($this->month != $month) return false;

        if ($this->day != $day) return false;
        return $name;
    }

   /**
    * ハッピーマンデー
    */
   private function happyHoliday($month, $week, $start, $end, $name)
   {
       if ($this->isWithinYear($start, $end) === false) return false;
       if ($this->month != $month) return false;

       // 第*月曜日の日付を求める
       $w = 1; // 月曜日固定
       $d1 = new HolidayDateTime($this->format('Y-m-1'));
       $w1 = intval($d1->dayOfWeek);
       $day  = $w - $w1 < 0 ? 7 + $w - $w1 : $w - $w1;
       $day++;
       $day = $day + 7 * ($week - 1);

       if ($this->day != $day) return false;
       return $name;
   }

    /**
     * 春分の日
     */
    private function springHoliday($month, $dummy, $start, $end, $name)
    {
        if ($this->isWithinYear($start, $end) === false) return false;
        if ($this->month != $month) return false;

        $year = $this->year;
        $day = floor(20.8431 + 0.242194 * ($year - 1980) - floor(($year - 1980) / 4));

        if ($this->day != $day) return false;
        return $name;
    }

    /**
     * 秋分の日
     */
    private function autumnHoliday($month, $dummy, $start, $end, $name)
    {
        if ($this->isWithinYear($start, $end) === false) return false;
        if ($this->month != $month) return false;

        $year = $this->year;
        $day = floor(23.2488 + 0.242194 * ($year - 1980) - floor(($year - 1980) / 4));

        if ($this->day != $day) return false;
        return $name;
    }

    /**
     * 年が祝日適用範囲内であるか
     */
    private function isWithinYear($start, $end)
    {
        if ($this->year < $start || $end < $this->year) {
            return false;
        }
        return true;
    }

    // 以下Carbonを継承している場合は削除すべし
    public function __get($name)
    {
        switch (true) {
        case array_key_exists($name, $formats = [
            'year' => 'Y',
            'month' => 'n',
            'day' => 'j',
            'hour' => 'G',
            'minute' => 'i',
            'second' => 's',
            'micro' => 'u',
            'dayOfWeek' => 'w',
            'dayOfYear' => 'z',
            'weekOfYear' => 'W',
            'daysInMonth' => 't',
            'timestamp' => 'U',
        ]):
            return (int) $this->format($formats[$name]);
        }
    }
}


$to = '未提出者';
$add = 'From:ses@xxxcorp';
$title = '【月報提出】';

//自動返信メール本文
$message = '未提出者';
$message .= ' さん'."\n\n";
$message .= 'お疲れさまです。'."\n\n";
$message .= '月報の提出が過ぎてます。'."\n";
$message .= 'お忙しいとは思いますが、'."\n";
$message .= '至急ご提出いただけますようお願い致します。'."\n\n";
$message .= '月報の提出は毎月第一営業日午前9時まで';
$message .= ' にご提出いただけますようお願い致します。'."\n\n";
$message .= "\n\n";
$message .= '※当メールは送信専用メールアドレスから送信しております。'."\n";
$message .= '※当メールが行き違いました場合は何卒ご容赦頂ますようお願い申し上げます。'."\n";
$message .= '※当メールにお心当たりのない場合は、誠に恐れ入りますが、破棄していただけますようお願い申し上げます。'."\n";
$message .= "\n\n";
$message .= '*********************************************'."\n\n";
$message .= 'EBA株式会社'."\n";
$message .= 'http://xxxcorp.jp/'."\n\n";
$message .= '*********************************************'."\n";

//メール設定
mb_language("ja");
mb_internal_encoding("UTF-8");



$datetime = new HolidayDateTime();
//$datetime->holiday();

$weekend = data('w');

//自動返信メール送信設定
if( $weekend = ( 0 || 6 ) ){ // 土日だったら
	'送らない';
}elseif( $datetime->holiday() = 'true'){ // 祝日だったら
	'送らない';
}else{
	mb_send_mail($to,$title,$message,$add);
}
