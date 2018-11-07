<?php


class Api extends Db{
    /**
     * apiサービスからカレンダー情報を取得し、配列で返す
     * 年||月||日|| 年号||和暦||曜日|| 曜日番号||祝日名||
     *
     * @param $yearMonth
     * @return array
     *
     */
    function getCalender($yearMonth) {
        $year      = preg_replace('/([0-9]{4})-([0-9]{2})/', '$1', $yearMonth);
        $month     = preg_replace('/([0-9]{4})-([0-9]{2})/', '$2', $yearMonth);
        $url       = "http://calendar-service.net/cal?start_year={$year}&start_mon={$month}&end_year=&end_mon=&year_style=normal&month_style=numeric&wday_style=none&format=csv";
        $_monthCsv = file_get_contents($url);
        $monthCsv  = mb_convert_encoding($_monthCsv, "utf8", "auto");
        $lines     = explode("\n", $monthCsv);
        foreach ($lines as $line) {
            $return[] = explode(",", $line);
        }
        return $return;
    }

    /**
     * apiサービスからカレンダー情報を取得し、配列で返す（月曜始まり）
     * 年||月||日|| 年号||和暦||曜日|| 曜日番号||祝日名||
     *
     * @param $yearMonth
     * @return array
     *
     */
    function getCalenderContainNull($yearMonth) {
        $year      = preg_replace('/([0-9]{4})-([0-9]{2})/', '$1', $yearMonth);
        $month     = preg_replace('/([0-9]{4})-([0-9]{2})/', '$2', $yearMonth);
        $url       = "http://calendar-service.net/cal?start_year={$year}&start_mon={$month}&end_year=&end_mon=&year_style=normal&month_style=numeric&wday_style=none&format=csv";
        $_monthCsv = file_get_contents($url);
        $monthCsv  = mb_convert_encoding($_monthCsv, "utf8", "auto");
        $lines     = explode("\n", $monthCsv);
        foreach ($lines as $line) {
            $_calArray[] = explode(",", $line);
        }
        $weekNum = 1;
        $dayType = 1;
        $date    = 1;
        for ($cnt = 1; $cnt <= 40; $cnt++) {
            // 日曜終わりの月の場合、空の週ができないように break
            if (!$_calArray[$date+1]) {
                break;
            }
            // 月曜日から順に、一致確認。月曜日始まりでない場合、空配列が入る。
            if ($_calArray[$date][6] == $dayType) {
                $calender[$weekNum][$cnt] = $_calArray[$date];
                $date++;
            } else {
                $calender[$weekNum][$cnt] = ['', '', '', '', '', '', ''];
            }
            // 次の曜日へ
            $dayType++;
            // 日曜数字は7ではなく0なので、ここで入れ替える
            if ($dayType == 7) {
                $dayType = 0;
            }
            // 日曜を入れ終わったら次の週
            if ($dayType == 1) {
                $weekNum++;
                if (!$_calArray[$date]) {
                    break;
                }
            }
        }
        return $calender;
    }

    /**
     * 曜日により、色を返すメソッド
     *
     * @param $dayOfWeek
     * @param $holiday
     *
     * @return string
     */
    function chkHoliday($dayOfWeek, $holiday) {
        if ($dayOfWeek == 6 || $dayOfWeek == 0 || !empty($holiday)) {
            if ($dayOfWeek == 0) {
                $return = ' bgcolor="#ff5050"';
            } else {
                if ($dayOfWeek != 0 && !empty($holiday)) {
                    $return = ' bgcolor="#efe700"';
                } else {
                    if ($dayOfWeek != 0 && empty($holiday) && $dayOfWeek == 6) {
                        $return = ' bgcolor="#00B0F0"';
                    }
                }
            }
            return $return;
        }
    }

    /**
     * 曜日により、就業基本時間を入力するかしないかを返す
     *
     * @param $dayOfWeek
     * @param $holiday
     *
     * @return 1 or 2
     */
    public static function chkInputTime($dayOfWeek, $holiday) {
        if ($dayOfWeek == 6 || $dayOfWeek == 0 || !empty($holiday)) {
            if ($dayOfWeek == 0) {
                return 1;
            } else {
                if ($dayOfWeek != 0 && !empty($holiday)) {
                    return 1;
                } else {
                    if ($dayOfWeek != 0 && empty($holiday) && $dayOfWeek == 6) {
                        return 1;
                    }
                }
            }
        }
        return 2;
    }

    /**
     *
     *
     * @param $day
     * @param $legal
     * @param $key
     *
     * @return string
     */
    public function chkDay($day, $legal, $key) {
        if (($day == 0 || !empty($legal)) && $key == 16) {
            return ' selected';
        } elseif ($day == 6 && $key == 15) {
            return ' selected';
        }
    }

    /**
     * yyyy-mm 形式を
     * yyyy年mm月 で返す
     *
     * @param $yearMonth
     *
     * @return string
     */
    public static function cnvYearMonth($yearMonth){
        $_yearMonth = explode('-',$yearMonth);
        $year = $_yearMonth[0];
        $month = $_yearMonth[1];
        return $year.'年'.$month.'月';
    }
}