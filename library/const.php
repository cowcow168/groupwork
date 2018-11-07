<?php
//ドメイン・URL
const DOMAIN        = 'xxxcorp.xyz/xxx-groupwork_ushijima';
define('DOCUMENT_ROOT', '/var/www/html/'.DOMAIN.'/');
define('LIBRARY', DOCUMENT_ROOT.'library/');
//変わらないもの
const TAX = 1.08;
const N = "\n";
const NN = "\n\n";
const NNN = "\n\n\n";

//サイト情報
const CORP           = 'xxx株式会社';
const _FOOTER        = '&copy; xxx Inc. 2018';
const SITENAME       = 'グループワーク';
const SITE_OPEN_DATE = '2018/11/11';
const INFO_MAIL      = 'info@xxxcorp.jp';
define('LOG_FILE', DOCUMENT_ROOT.'log/logger.log');

//メンバーの画像データを格納するパス情報
define('MEMBER_IMAGE',DOCUMENT_ROOT.'view/img/memberimg/');

//テーマチャットの作成用の画像データを格納するパス情報
define('THEME_IMAGE',DOCUMENT_ROOT.'view/img/themeimg/');
// データベース系
// todoEba
const MYSQL_HOST   = 'localhost';
const MYSQL_DBNAME = 'todoeba';

//週報システム
const W_MYSQL_HOST   = 'localhost';
const W_MYSQL_DBNAME = 'eba_report';

if($env == 'local'){
    define ('MYSQL_DBUSER' , 'root');
    define ('MYSQL_DBPASS' , '');
} else {
    define ('MYSQL_DBUSER' , 'root');
    define ('MYSQL_DBPASS' , 'GomJR5nwq_lm');
}

define ('MYSQL_DSN' , 'mysql:dbname='.MYSQL_DBNAME.';host='.MYSQL_HOST.';charset=utf8mb4');
define ('W_MYSQL_DSN' , 'mysql:dbname='.W_MYSQL_DBNAME.';host='.W_MYSQL_HOST.';charset=utf8mb4');

// セレクトボックスなど
const WEEK = [
	0 => '日',
	1 => '月',
	2 => '火',
	3 => '水',
	4 => '木',
	5 => '金',
	6 => '土'
];
const DAY = [
	1 => 1,
	2 => 2,
	3 => 3,
	4 => 4,
	5 => 5,
	6 => 6,
	7 => 7,
	8 => 8,
	9 => 9,
	10 => 10,
	11 => 11,
	12 => 12,
	13 => 13,
	14 => 14,
	15 => 15,
	16 => 16,
	17 => 17,
	18 => 18,
	19 => 19,
	20 => 20,
	21 => 21,
	22 => 22,
	23 => 23,
	24 => 24,
	25 => 25,
	26 => 26,
	27 => 27,
	28 => 28,
	29 => 29,
	30 => 30,
	31 => 31,
];
const MONTH = [
	1 => 1,
	2 => 2,
	3 => 3,
	4 => 4,
	5 => 5,
	6 => 6,
	7 => 7,
	8 => 8,
	9 => 9,
	10 => 10,
	11 => 11,
	12 => 12,
];
const AGREEMENT = [
	0 => '再',
	1 => '承認'
];
const MEMBER_TEAM_TYPE = [
	1 => 'webデザイン・フロントエンジニアグループ',
	50 => 'プログラミンググループ',
	//月報提出可否メールでmembrer_team_type < 100
	100 => '取締役CEO',
	101 => '代表取締役',
	110 => '取締役COO',
	120 => '取締役CFO',
	130 => '取締役CIO',
	140 => '取締役CTO',
	150 => '取締役CMO',
	199 => '取締役',
	200 => '営業本部（営業部）',
	300 => '業務本部（総務）',
	301 => '業務本部（総務非正規社員）',
	350 => '業務本部（人事）',
	400 => '業務本部（労務）',
	450 => '業務本部（経理）',
	451 => '業務本部（経理非正規社員）',
	800 => 'トレーナー',
	9999 => 'デバッグ用',
];

const PAGE_NAME = [
	'add_member' => '',
	'admin_top' => '',
	'change_password' => '',
	'club' => '',
	'club_edit' => '',
	'config' => '',
	'contact' => '',
	'contact_list' => '',
	'contact_edit' => '',
	'contact_conf' => '',
	'contact_done' => '',
	'general_affairs_report_list' => '',
	'general_affairs_report_edit' => '',
	'general_affairs_report_conf' => '',
	'general_affairs_report_done' => '',
	'hack_list' => '',
	'help' => '',
	'human' => '',
	'human_list' => '',
	'human_edit' => '',
	'human_conf' => '',
	'human_done' => '',
	'leader_top' => '',
	'mail_destination' => '',
	'manual' => '',
	'pay_off' => '',
	'send_mail' => '',
	'site_info' => '',
	'submit_customer' => '',
	'time_card' => '',
	'total_submission' => '',
	'update_member' => '',
	'weekly_report' => '',
	'welfare' => '',
	'welfare_edit' => '',
];
//Account::getNumberOfEmployees()
const MEMBER_EMPLOYEES_GROUP = [
	'director' => '(100,101,110,120,130,140,150,199)',
	'employee' => '(1,50,200,300,350,400,450,800)',
	'debug' => '(9999)',
	'parttime' => '(301, 451)',
	'allmember' => '(9999)',
];

//週報を確認したかどうかチェックが必要な人リスト
//member_no => member_id
const LOOKCHECK_ADMIN_MEMBER = [
	2 => 'x.xxxxx',
	4 => 'x.xxxxx',
	13 => 'x.xxxx',
];

const ASIDE_LEFT_NAME = [
	1 => '全社員一覧',
	2 => 'チーム社員一覧',
	3 => 'タイムカード',
	4 => '週報',
	5 => '各種精算',
	6 => '現場情報',
	7 => '原田週報',
	8 => '設定',
	50 => '帰社日・懇親会情報',
	51 => '総務お知らせ',
	52 => '人事通信',
	53 => 'リーダーお知らせ',
	100 => 'メール送信先一覧',
	101 => '福利厚生一覧',
	102 => '同好会について',
	103 => 'EBAブログ',
	150 => '困った時は（実装予定）',
	151 => 'マニュアル',
	200 => '帰社日管理',
	201 => '総務お知らせ管理',
	202 => 'リーダーお知らせ管理',
	203 => '福利厚生管理',
	204 => '人事通信管理',
	205 => '同好会管理',
	249 => '週報確認管理',
	250 => 'デバッグモード',
	251 => 'メール送信先生成機',
];

const ASIDE_LEFT_URL = [
	1 => 'admin_top',
	2 => 'leader_top',
	3 => 'time_card',
	4 => 'weekly_report',
	5 => 'pay_off',
	6 => 'site_info',
	7 => 'weekly_report_harada',
	8 => 'config',
	50 => 'contact',
	51 => 'general_affairs_report',
	52 => 'human_report',
	53 => 'leader_report',
	100 => 'mail_destination',
	101 => 'welfare',
	102 => 'club',
	103 => 'http://ebablog.net/" target="_brank',
	150 => 'help',
	151 => 'manual',
	200 => 'contact_list',
	201 => 'general_affairs_report_list',
	202 => 'leader_report_list',
	203 => 'welfare_edit',
	204 => 'human_report_list',
	205 => 'club_edit',
	249 => 'weekly_report_response_list',
	250 => 'hack_list',
	251 => 'send_mail',
];
class Mysql
{
    const SUCCESS = 'データの登録が完了しました。';
    const FAIL    = 'データの登録に失敗しました。';
}

const ERR_MAIL_ADDR_TO = 'a.ushijima@xxxcorp.jp';
