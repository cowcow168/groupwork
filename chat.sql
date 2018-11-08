DROP TABLE if exists PERMISSION;
DROP TABLE if exists GROUP_CHAT;
--20181023 TODO ダイレクトチャットとグループチャットのテーブルを分けて実装するかも？(不要になった際は、下記は、削除)
DROP TABLE if exists GROUP_CHAT_EXCHANGE;
DROP TABLE if exists TO_CHAT;
--20181023 TODO ダイレクトチャットとグループチャットのテーブルを分けて実装するかも？(不要になった際は、下記は、削除)
DROP TABLE if exists DIRECT_CHAT_EXCHANGE;
DROP TABLE if exists LIKE_NUMBER;
DROP TABLE if exists FAVORITE_NUMBER;
DROP TABLE if exists GROUP_CHAT_ATTACHED_FILE;
-- 権限テーブル
CREATE TABLE PERMISSION (
 PERMISSION_NO SERIAL PRIMARY KEY,-- 主キー
 PERMISSION_MEMBER_NO BIGINT(20) UNSIGNED NOT NULL,-- メンバーNo
 PERMISSION_TYPE SMALLINT NOT NULL,-- 権限タイプ
 PERMISSION_IMAGE TEXT,-- メンバー画像
 PERMISSION_INS_TS TIMESTAMP(6) NULL,--  作成日時
 PERMISSION_UPD_TS TIMESTAMP(6) NULL,--  更新日時
 PERMISSION_STATUS BOOLEAN DEFAULT TRUE-- 退社したかしていないか
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--20181018 ダイレクトチャット用に紐付ける必要があるので、テーブルを拡張する
ALTER TABLE PERMISSION ADD GROUP_CHAT_NO BIGINT(20) UNSIGNED COMMENT 'チャット番号' AFTER PERMISSION_NO;
ALTER TABLE `PERMISSION` CHANGE `GROUP_CHAT_NO` `GROUP_CHAT_NO` BIGINT(20) UNSIGNED NOT NULL COMMENT 'チャット番号';
--  グループチャット登録画面,編集画面で使用するテーブル(サイボウズでは、テーマチャット)非活性でやるメンバーの追加削除などは、jqueryなどでやる。グループチャットに削除フラグを入れる。
CREATE TABLE GROUP_CHAT (
 GROUP_CHAT_NO SERIAL PRIMARY KEY,-- 主キー
 GROUP_CHAT_TITLE TEXT NOT NULL,-- グループチャット名
 GROUP_CHAT_MEMO TEXT,-- メモ
 GROUP_CHAT_ATTACHED_FILE TEXT,-- 添付ファイル
 GROUP_CHAT_CREATE_MEMBER_NO BIGINT(20) UNSIGNED,--  作成者No
 GROUP_CHAT_UPDATE_MEMBER_NO BIGINT(20) UNSIGNED,--  更新者No
 GROUP_CHAT_INS_TS TIMESTAMP(6) NULL,--  作成日時
 GROUP_CHAT_UPD_TS TIMESTAMP(6) NULL,--  更新日時
 GROUP_CHAT_STATUS BOOLEAN DEFAULT FALSE -- 削除フラグ
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 20181023 グループチャットで所属しているメンバーが更新できるかどうかを判定するため用のカラムと所属メンバーのカラムを追加(グループチャットから抜けるを押した際は、削除フラグが立つ)
ALTER TABLE GROUP_CHAT ADD GROUP_CHAT_BELONGS_MEMBER_NO BIGINT(20) UNSIGNED COMMENT '所属メンバー番号' AFTER GROUP_CHAT_UPDATE_MEMBER_NO;
ALTER TABLE GROUP_CHAT ADD GROUP_CHAT_UPDATE_PERMISSION_STATUS BOOLEAN DEFAULT FALSE COMMENT '更新権限フラグ' AFTER GROUP_CHAT_UPD_TS;

-- 20181106 上記のようにグループチャット番号に所属しているメンバーのカラムを持っていると同じグループチャットが複数表示されるので下記のテーブルに分けた
ALTER TABLE GROUP_CHAT DROP `GROUP_CHAT_BELONGS_MEMBER_NO`;
ALTER TABLE GROUP_CHAT DROP `GROUP_CHAT_UPDATE_PERMISSION_STATUS`;
-- 20181106 グループチャットで所属しているメンバーがいるときに登録されるテーブルを作成する
CREATE TABLE GROUP_CHAT_BELONG (
 GROUP_CHAT_BELONG_NO SERIAL PRIMARY KEY,-- 主キー
 GROUP_CHAT_NO BIGINT(20) UNSIGNED COMMENT 'チャット番号',-- チャット番号
 GROUP_CHAT_BELONGS_MEMBER_NO BIGINT(20) UNSIGNED COMMENT '所属メンバー番号',-- 所属メンバー番号
 GROUP_CHAT_BELONG_INS_TS TIMESTAMP(6) NULL,--  作成日時
 GROUP_CHAT_BELONG_UPD_TS TIMESTAMP(6) NULL,--  更新日時
 GROUP_CHAT_UPDATE_PERMISSION_STATUS BOOLEAN DEFAULT FALSE COMMENT '更新権限フラグ' -- 更新権限フラグ
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- 20181106 グループチャットの添付ファイルが複数あると考えられるので、カラムを削除
ALTER TABLE `GROUP_CHAT` DROP `GROUP_CHAT_ATTACHED_FILE`;
-- TODO 20181023 グループチャットのやり取りとダイレクトチャットのテーブルを分ける必要があるかもしれない。(実際に返信したりした時の動きを確認してから、実装仮に作成)
-- 20181023 PERMISSION_MEMBER_NO は、ダイレクトチャットでなく、あくまでグループチャットが主なので作成者Noで判定するように設計
CREATE TABLE GROUP_CHAT_EXCHANGE (
 GROUP_CHAT_EXCHANGE_NO SERIAL PRIMARY KEY COMMENT 'グループチャットやり取り番号(主キー)',-- 主キー
 GROUP_CHAT_NO BIGINT(20) UNSIGNED COMMENT 'チャット番号',-- チャット番号
 GROUP_CHAT_EXCHANGE_COMMENT_TEXT TEXT COMMENT 'コメント本文',-- コメント本文
 GROUP_CHAT_EXCHANGE_ATTACHED_FILE TEXT COMMENT '添付ファイル',-- 添付ファイル
 GROUP_CHAT_EXCHANGE_CREATE_MEMBER_NO BIGINT(20) UNSIGNED COMMENT '作成者No',--  作成者No グループチャットに所属している人が入る
 GROUP_CHAT_EXCHANGE_UPDATE_MEMBER_NO BIGINT(20) UNSIGNED COMMENT '更新者No',--  更新者No
 GROUP_CHAT_EXCHANGE_COMMENT_GOOD_NUM BIGINT(20) UNSIGNED COMMENT 'いいね数',-- いいね数
 GROUP_CHAT_EXCHANGE_COMMENT_FAVORITE_NUM BIGINT(20) UNSIGNED COMMENT 'お気に入り数',-- お気に入り数
 GROUP_CHAT_EXCHANGE_PARENT_REPLY_COMMENT_NO BIGINT(20) UNSIGNED COMMENT '返信先の大本のコメントNo', --  返信先の大本のコメントNo
 GROUP_CHAT_EXCHANGE_REPLY_COMMENT_NO BIGINT(20) UNSIGNED COMMENT '返信先のコメントNo', --  返信先のコメントNo
 GROUP_CHAT_EXCHANGE_INS_TS TIMESTAMP(6) NULL COMMENT '作成日時',--  作成日時
 GROUP_CHAT_EXCHANGE_UPD_TS TIMESTAMP(6) NULL COMMENT '更新日時',--  更新日時
 GROUP_CHAT_EXCHANGE_STATUS BOOLEAN DEFAULT FALSE COMMENT '削除フラグ',-- 削除フラグ
 GROUP_CHAT_EXCHANGE_ADD_FAVORITE_STATUS BOOLEAN DEFAULT FALSE COMMENT 'お気に入り登録判定用フラグ' -- お気に入り登録をした時用に必要
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ダイレクトチャット,グループチャット画面で使用するテーブル
CREATE TABLE TO_CHAT (
 TO_CHAT_NO SERIAL PRIMARY KEY,-- 主キー
 GROUP_CHAT_NO BIGINT(20) UNSIGNED,-- チャット番号
 PERMISSION_MEMBER_NO BIGINT(20) UNSIGNED,-- チャット番号
 TO_CHAT_COMMENT_TEXT TEXT,-- コメント本文
 TO_CHAT_ATTACHED_FILE TEXT,-- 添付ファイル
 TO_CHAT_CREATE_MEMBER_NO BIGINT(20) UNSIGNED,--  作成者No
 TO_CHAT_UPDATE_MEMBER_NO BIGINT(20) UNSIGNED,--  更新者No
 TO_CHAT_COMMENT_GOOD_NUM BIGINT(20) UNSIGNED,-- いいね数
 TO_CHAT_COMMENT_FAVORITE_NUM BIGINT(20) UNSIGNED,-- お気に入り数
 TO_CHAT_PARENT_REPLY_COMMENT_NO BIGINT(20) UNSIGNED, --  返信先の大本のコメントNo
 TO_CHAT_REPLY_COMMENT_NO BIGINT(20) UNSIGNED, --  返信先のコメントNo
 TO_CHAT_INS_TS TIMESTAMP(6) NULL,--  作成日時
 TO_CHAT_UPD_TS TIMESTAMP(6) NULL,--  更新日時
 TO_CHAT_STATUS BOOLEAN DEFAULT FALSE,-- 削除フラグ
 TO_CHAT_ADD_FAVORITE_STATUS BOOLEAN DEFAULT FALSE -- お気に入り登録をした時用に必要
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--20180920 ダイレクトチャット時にメンバー毎にグループチャット番号を付与して管理する必要があるので
--PERMISSIONテーブルと紐付けるカラムをTO_CHATテーブルに追加

--20181018 テーブルの名称を変更(栗山さんのBORD_COMMENT_TEXTに合わせて記載する)
ALTER TABLE `TO_CHAT` CHANGE `TO_CHAT_COMMENT_TEXT` `TO_CHAT_TEXT` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;

--20181023 ダイレクトチャットとグループチャット番号のテーブルを分けて作成(マスターは、PERMISSIONテーブルでくくりは、group_chat_noの中のdirect_chatとして、設計)
-- TODO 上記のTO_CHATテーブルとして、動かしてみて動きがまずくなかったら、下記のテーブル(TO_CHATテーブルの名称違いのテーブルを採用して、運用する命名的には、下記の方が分かりやすいと思われる)
CREATE TABLE DIRECT_CHAT_EXCHANGE (
 DIRECT_CHAT_EXCHANGE_NO SERIAL PRIMARY KEY,-- 主キー
 GROUP_CHAT_NO BIGINT(20) UNSIGNED COMMENT 'チャット番号',-- チャット番号(PERMISSIONテーブルのPERMISSION_MEMBER_NOと紐付ける為に必要)
 DIRECT_CHAT_EXCHANGE_COMMENT_TEXT TEXT COMMENT 'コメント本文',-- コメント本文
 DIRECT_CHAT_EXCHANGE_ATTACHED_FILE TEXT COMMENT '添付ファイル',-- 添付ファイル
 DIRECT_CHAT_EXCHANGE_CREATE_MEMBER_NO BIGINT(20) UNSIGNED COMMENT '作成者No',--  作成者No
 DIRECT_CHAT_EXCHANGE_UPDATE_MEMBER_NO BIGINT(20) UNSIGNED COMMENT '更新者No',--  更新者No
 DIRECT_CHAT_EXCHANGE_COMMENT_GOOD_NUM BIGINT(20) UNSIGNED COMMENT 'いいね数',-- いいね数
 DIRECT_CHAT_EXCHANGE_COMMENT_FAVORITE_NUM BIGINT(20) UNSIGNED COMMENT 'お気に入り数',-- お気に入り数
 DIRECT_CHAT_EXCHANGE_PARENT_REPLY_COMMENT_NO BIGINT(20) UNSIGNED COMMENT '返信先の大本のコメントNo', --  返信先の大本のコメントNo
 DIRECT_CHAT_EXCHANGE_REPLY_COMMENT_NO BIGINT(20) UNSIGNED COMMENT '返信先のコメントNo', --  返信先のコメントNo
 DIRECT_CHAT_EXCHANGE_INS_TS TIMESTAMP(6) NULL COMMENT '作成日時',--  作成日時
 DIRECT_CHAT_EXCHANGE_UPD_TS TIMESTAMP(6) NULL COMMENT '更新日時',--  更新日時
 DIRECT_CHAT_EXCHANGE_STATUS BOOLEAN DEFAULT FALSE COMMENT '削除フラグ',-- 削除フラグ
 DIRECT_CHAT_EXCHANGE_ADD_FAVORITE_STATUS BOOLEAN DEFAULT FALSE COMMENT 'お気に入り登録判定用フラグ' -- お気に入り登録をした時用に必要
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 掲示板やダイレクトチャット,グループチャットのいいね数のテーブル(画面表示用)
CREATE TABLE LIKE_NUMBER (
 LIKE_NUMBER_NO SERIAL PRIMARY KEY,-- 主キー
 CHAT_TABLE_TYPE_NO VARCHAR(20) NOT NULL,-- テーブルタイプNo
 LIKE_NUMBER_TARGET_MEMBER_NO BIGINT(20) UNSIGNED,-- いいね押された人番号
 TO_CHAT_COMMENT_GOOD_NUM BIGINT(20) UNSIGNED, -- いいね数
 LIKE_NUMBER_INS_TS TIMESTAMP(6) NULL,--  作成日時
 LIKE_NUMBER_UPD_TS TIMESTAMP(6) NULL,--  更新日時
 LIKE_NUMBER_CONFIRM_STATUS BOOLEAN DEFAULT FALSE -- 確認フラグ
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- お気に入りテーブル
CREATE TABLE FAVORITE_NUMBER (
 FAVORITE_NUMBER_NO SERIAL PRIMARY KEY,-- 主キー
 CHAT_TABLE_TYPE SMALLINT(9) NOT NULL,-- テーブルタイプNo
 FAVORITE_NUMBER_TARGET_MEMBER_NO BIGINT(20) UNSIGNED,-- お気に入り押された人番号
 TO_CHAT_COMMENT_FAVORITE_NUM BIGINT(20) UNSIGNED, -- お気に入り数
 FAVORITE_NUMBER_INS_TS TIMESTAMP(6) NULL,--  作成日時
 FAVORITE_NUMBER_UPD_TS TIMESTAMP(6) NULL,--  更新日時
 FAVORITE_NUMBER_CONFIRM_STATUS BOOLEAN DEFAULT FALSE -- 確認フラグ
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 20181003 テーマチャットを作成する際に画像を上限まで(25MBまで)追加できるので、テーブル拡張
-- テーマチャット画像テーブル(複数画像登録ができるので作成)
CREATE TABLE GROUP_CHAT_ATTACHED_FILE (
 GROUP_CHAT_ATTACHED_FILE_NO SERIAL PRIMARY KEY,-- 主キー
 GROUP_CHAT_NO BIGINT(20) UNSIGNED NOT NULL,-- チャット番号
 GROUP_CHAT_ATTACHED_FILE TEXT,-- 添付ファイル
 GROUP_CHAT_ATTACHED_FILE_INS_TS TIMESTAMP(6) NULL,--  作成日時
 GROUP_CHAT_ATTACHED_FILE_UPD_TS TIMESTAMP(6) NULL,--  更新日時
 GROUP_CHAT_ATTACHED_FILE_STATUS BOOLEAN DEFAULT FALSE -- 削除フラグ
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
