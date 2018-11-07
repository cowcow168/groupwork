<?php
interface Mail
{
	/*
	 * メーラーはインスタンス化して使用。
	 * function名はメールの種類にして、内容自体は引数で変えられるようにする。
	 * メールが送られる人はgroupwork@xxxcorp.jpとする。
	 * コンストラクタで決定できる部分は決定することとする。
	 * SERVER情報とREQUEST情報は必ず保持すること
	 *
	 * $flgは本番が1、停止が0、テストで宛先変える場合は適当に
	 *
	 */

	//catchされた場合
	public function errorMail($str, $flg);
	//バッチ処理によって変更された点とその結果
	public function batchMail($str, $flg);
	//新着情報のメール
	public function reportBatchMail($str, $flg);
	//処理中で変数の中身が見づらい場合などのデバッグ要素
	public function debugMail($str, $flg);
	//phpのエラーが出た時に誰がいつどのようなエラーを吐いたのかメールが送られるようになれば最高　出来なければコメントアウトしてください原田はやり方わかりません
	public function phpErrorMail($str, $flg);
}

class Mailer implements Mail
{
	//catchされた場合
	public function errorMail($str,$flg){

	}
	//バッチ処理によって変更された点とその結果
	public function batchMail($str,$flg){

	}
	//新着情報のメール
	public function reportBatchMail($str,$flg){

	}
	//処理中で変数の中身が見づらい場合などのデバッグ要素
	public function debugMail($str,$flg){

	}
	//phpのエラーが出た時に誰がいつどのようなエラーを吐いたのかメールが送られるようになれば最高　出来なければコメントアウトしてください原田はやり方わかりません
	public function phpErrorMail($str,$flg){
		
	}
}
