<?php
if (!isset($_SESSION)){
    session_start();
}
//エスケープ
require_once 'function.php';
function print_pre($str)
{
    echo '<pre>';
    print_r($str);
    echo '</pre>';
}
print_pre($GLOBALS);
$token = $_POST['token'];
if ($_SESSION['token'] == NULL &&  $_POST['token'] == NULL) {
    echo 'aaa';
    //header('Location: contact.php');
    //exit();
}


$prefecture_h = array(
    '0' => 'お選びください',
    '1' => '北海道',
    '2' => '青森県',
    '3' => '秋田県',
    '4' => '岩手県',
    '5' => '山形県',
    '6' => '宮城県',
    '7' => '新潟県',
    '8' => '福島県',
    '9' => '茨城県',
    '10' => '栃木県',
    '11' => '群馬県',
    '12' => '埼玉県',
    '13' => '東京都',
    '14' => '千葉県',
    '15' => '神奈川県',
    '16' => '静岡県',
    '17' => '山梨県',
    '18' => '愛知県',
    '19' => '長野県',
    '20' => '岐阜県',
    '21' => '富山県',
    '22' => '福井県',
    '23' => '石川県',
    '24' => '京都府',
    '25' => '滋賀県',
    '26' => '奈良県',
    '27' => '和歌山県',
    '28' => '三重県',
    '29' => '大阪府',
    '30' => '兵庫県',
    '31' => '岡山県',
    '32' => '広島県',
    '33' => '島根県',
    '34' => '鳥取県',
    '35' => '山口県',
    '36' => '香川県',
    '37' => '徳島県',
    '38' => '高知県',
    '39' => '愛媛県',
    '40' => '福岡県',
    '41' => '佐賀県',
    '42' => '長崎県',
    '43' => '宮崎県',
    '44' => '大分県',
    '45' => '熊本県',
    '46' => '鹿児島県',
    '47'=> '沖縄県',

);
//sendが押されたら
if (!empty($_POST['send'])){
    //名前 name
    //空白は認めない
    if(empty($_POST['name'])){
        $err['name'] = '未入力です。';
    }elseif (preg_match('/^(\s|　)$/u',$_POST['name'])){
        $err['name'] = '空白は禁止です。';
    }else{
        $name = $_POST['name'];
        echo $name;
    }

    //フリガナ ruby
    //空白は認めない
    //全角カタカナしか認めない→全角カタカナだったら[preg_match]
    if ($_POST['ruby'] == '0'){
        $err['ruby'] ='2文字以上の全角カタカナのみです。';
    }elseif(empty($_POST['ruby'])){
        $err['ruby'] ='未入力です。';
    }elseif (preg_match('/^[ァ-ヴー].+$/u',$_POST['ruby'])){
        $ruby = $_POST['ruby'];
    }else{
        $err['ruby'] ='2文字以上の全角カタカナのみです。';
    }

    //都道府県 prefecture
    //デフォルトは「お選びください」→送信するとエラー
    if ($_POST['prefecture'] == 0){
        $err['prefecture'] = '都道府県を選択してください。';
    }else {
        $prefecture= $_POST['prefecture'];
    }

    //市区町村 cities
    if(empty($_POST['cities'])){
        $err['cities'] = '市区町村が未入力です';
    }else{
        $cities = $_POST['cities'];
        //         echo $cities;//後で消す。
    }
    //それ以降 ex
    if(empty($_POST['ex'])){
        $err['ex'] = '番地が未入力です。';
    }else{
        $ex = $_POST['ex'];
    }
    //マンション名など apartment
    //条件なし
    if(!empty($_POST['apartment'])){
        $apartment = $_POST['apartment'];
    }
    //マンション名など apartment
    //条件なし
    if(!empty($_POST['old'])){
        $old = $_POST['old'];
    }
    //電話番号 tel
    //未入力は認めない
    //ハイフンは不要
    //桁は10桁か11桁以外は認めない
    if (preg_match('/^[0-9]{10,11}$/u',$_POST['tel'])){
        $tel = $_POST['tel'];
    }else{
    }
    //メールアドレス
    //メールアドレス確認
    //アルファベットと数字のみ
    if (empty($_POST['mail'])){
        $err['mail'] = '未入力です。';
    }elseif (empty($_POST['mail_check'])){
        $err['mail_check'] = '未入力です。';
    }elseif ($_POST['mail'] == $_POST['mail_check']){
        $mail = $_POST['mail'];
        $mail_check = $_POST['mail_check'];
    }else {
        $err['mail'] ='アドレスが一致しません。';
        $err['mail_check'] = 'アドレスが一致しません。';
    }
    //お問い合わせ内容
    //改行可
    //20文字以上
    $count = substr_count($_POST['content'], "\n");
    if (empty($_POST['content'])){
        $err['content'] = '未入力です。';
    }elseif (mb_strlen($_POST['content'])<20){
        $err['content'] = '20文字以上で入力してください。';
    }else {
        $content =  $_POST['content'];

    }
    if (!empty($err)){
        require_once 'contact.php';
        exit;

    }

}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title></title>
<link rel="stylesheet" href="css/form.css" type="text/css">

<style type="text/css">
body{
        margin: 0 ;
        clear: both;
     }
input#submit_button{
		width: 90px;
		height: 30px;
		padding: 5px 15px;
		background-color: #fff;
        border-color: #000;
        border-width: 3px;
        margin: 0 auto;
        margin-left: 20%;
        margin-top: 2%;
        float:left;
    }
input.text_box{
        clear:both;
         border-style: solid;
         border-width: 3px;
         margin-left: 5px;
         margin-bottom: 5px;
         width: 30%;
     }
#form{
     width:100%;
     margin: 0 auto;
    }
p{
    margin: 0 auto ;
}
</style>
</head>
<body>

<header>お問い合わせフォーム</header>
<div id="main">
<h1>確認画面</h1>
    <form action="done.php" method="POST">
        <div class="form_2">
        <?= h($name).'h1'?>
            <input type="hidden" name="name" value="<?= h($name)?>">
            <input type="hidden" name="ruby" value="<?= h($ruby)?>">
            <input type="hidden" name="prefecture" value="<?= h($prefecture)?>">
            <input type="hidden" name="cities" value="<?= h($cities)?>">
            <input type="hidden" name="ex" value="<?= h($ex)?>">
            <input type="hidden" name="apartment" value="<?= h($apartment)?>">
            <input type="hidden" name="day" value="<?= h($old)?>">
            <input type="hidden" name="tel" value="<?= h($tel)?>">
             <input type="hidden" name="tel" value="<?= h($old)?>">
            <input type="hidden" name="mail" value="<?= h($mail)?>">
            <input type="hidden" name="mail_check" value="<?= h($mail_check)?>">
            <input type="hidden" name="content" value="<?= h($content)?>">
            <input type="hidden" name="token" value="<?= h($token)?>">
      <div id="list">
       		<table  border="1" style="table-layout:fixed;width:100%;">
            <tr>
            <th align="left">名前</th>
            <td><?php if(isset($_POST['name'])):?><?= h($_POST['name'])?><?php endif;?></td>
            </tr>
            <tr>
            <th align="left">フリガナ</th>
            <td align="left"><?php if(isset($_POST['ruby'])):?><?= h($_POST['ruby'])?><?php endif;?></td>
            </tr>
            <tr>
            <th align="left">都道府県</th>
            <td><?php if(isset($_POST['prefecture'])):?><?= h($prefecture_h[$_REQUEST['prefecture']]);?><?php endif;?></td>
            </tr>
            <tr>
            <th align="left">市区町村</th>
            <td><?php if(isset($_POST['cities'])):?><?= h($_POST['cities'])?><?php endif;?></td>
            </tr>
            <tr>
            <th align="left">それ以降</th>
            <td><?php if(isset($_POST['ex'])):?><?= h($_POST['ex'])?><?php endif;?></td>
            </tr>
            <tr>
            <th align="left">マンション等</th>
            <td ><?php if(isset($_POST['apartment'])):?><?= h($_POST['apartment'])?><?php endif;?></td>
            </tr>
            <tr>
    		<th align="left">年齢</th>
    		<td><?php if(isset($_POST['old'])):?><?= h($_POST['old']) ?><?php endif;?></td>
    		</tr>
    		<tr>
            <th align="left">電話番号</th>
            <td><?php if(isset($_POST['tel'])):?><?= h($_POST['tel']) ?><?php endif;?></td>
            </tr>
            <tr>
            <th align="left">メールアドレス</th>
            <td><?php if(isset($_POST['mail'])):?><?=h($_POST['mail']) ?><?php endif;?></td>
            </tr>
            <tr>
            <th align="left">メールアドレス確認</th>
            <td ><?php if(isset($_POST['mail_check'])):?><?= h($_POST['mail_check']) ?><?php endif;?></td>
            </tr>
            <tr>
            <th width="50%" align="left">お問い合わせ内容</th>
            <td width="50%" style="word-wrap:break-word;"><?php if(isset($_POST['content'])):?><?= nl2br(h($_POST['content']))?><?php endif;?></td>
            </tr>
            </table>
                	<p>入力した情報に間違いがなければ送信ボタンを押してください。</p>
        </div>

            	<div class="result">
            		<input id="submit_button" type="submit" name="send" value="送信">
            	</div>
        	</div>
    </form>
    <form action="harada_contact.php" method="POST">
            <input type="hidden" name="name" value="<?= h($name)?>">
            <input type="hidden" name="ruby" value="<?= h($ruby)?>">
            <input type="hidden" name="prefecture" value="<?= h($prefecture)?>">
            <input type="hidden" name="cities" value="<?= h($cities)?>">
            <input type="hidden" name="ex" value="<?= h($ex)?>">
            <input type="hidden" name="apartment" value="<?= h($apartment)?>">
            <input type="hidden" name="old" value="<?= h($old)?>">
            <input type="hidden" name="tel" value="<?= $tel?>">
            <input type="hidden" name="mail" value="<?= h($mail)?>">
            <input type="hidden" name="mail_check" value="<?= h($mail_check)?>">
            <input type="hidden" name="content" value="<?= h($content)?>">
        <div class="result">
            		<input id="submit_button" type= "submit" name="back" value="修正">
        </div>
    </form>
</div>
<footer>
<div class="footer">
<p><small>Copyrights&copy;2015 ジェルネイルオフランキング,Inc. All Rights Reserved.</small></p>
</div>
</footer>
</body>
</html>