<?php
if (!isset($_SESSION)){
session_start();
}
//エスケープ
require_once ('function.php');
function print_pre($str)
{
    echo '<pre>';
    print_r($str);
    echo '</pre>';
}
print_pre($GLOBALS);
//トークン生成
$token = md5(uniqid().mt_rand());
$_SESSION['token']= $token;
//月の配列
$month_h = array(
    '0' => '---',
    '1' => '1',
    '2' => '2',
    '3' => '3',
    '4' => '4',
    '5' => '5',
    '6' => '6',
    '7' => '7',
    '8' => '8',
    '9' => '9',
    '10' => '10',
    '11' => '11',
    '12' => '12',
);
//日の配列
$days_h = array(
    '0' => '---',
    '1' => '1',
    '2' => '2',
    '3' => '3',
    '4' => '4',
    '5' => '5',
    '6' => '6',
    '7' => '7',
    '8' => '8',
    '9' => '9',
    '10' => '10',
    '11' => '11',
    '12' => '12',
    '13' => '13',
    '14' => '14',
    '15' => '15',
    '16' => '16',
    '17' => '17',
    '18' => '18',
    '19' => '19',
    '20' => '20',
    '21' => '21',
    '22' => '22',
    '23' => '23',
    '24' => '24',
    '25' => '25',
    '26' => '26',
    '27' => '27',
    '28' => '28',
    '29' => '29',
    '30' => '30',
    '31' => '31',
);

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

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title></title>
<link rel="stylesheet" href="css/form.css" type="text/css">
<style type="text/css">



input#submit_button{
		width: 90px;
		height: 30px;
		padding: 5px 15px;
		margin-bottom: 5%;
		background-color: #fff;
        border-color: #000;
        border-width: 3px;


}
#content textarea.text_box{
        border-style: solid;
         border-width: 3px;
         margin-left: 5px;
         margin-bottom: 5%;
         width: 60%;
        height:100px;
    }
#content span{
        margin: 0;
        margin-left: 2%;
        margin-top: 5%;
     }
input.text_box{
         border-style: solid;
         border-width: 3px;
         margin-left: 5px;
         margin-bottom: 5px;
         width: 60%;
}

#text_id{
        width:30%;
        }
ul li{
         list-style: none;

    }
label{
    width:20%;

    }

#form{
     width:100%;
     height: 100%;
    }
span{
    margin-left: 2%;
    }
#btn{
    text-align: center ;
    }
</style>
</head>
<body>
<header></header>

<div id="main">
	<h1>お問い合わせフォーム</h1>
	<form action="harada_confirm.php" method="POST">
   	 <div id="form">
  	  	<div id="ul">
            <ul>
                <li>
                    <label for="name">お名前<span style="color: #c9171e">（必須）</span></label>
                </li>
                <li>
                    <input class="text_box" type ="text" name ="name" value="<?php if(isset($_POST['name'])):?><?= h($_POST['name'])?><?php endif;?>" placeholder="" class ="box"><span style="color: #c9171e"><?php if(isset($err['name'])):?><?=h($err['name']) ?><?php endif;?></span>
                </li>
                <li>
                    <label for="ruby">フリガナ<span style="color: #c9171e">（必須）</span></label>
     			</li>
                <li>
                    <input class="text_box" type ="text" name ="ruby" value="<?php if(isset($_POST['ruby'])):?><?= h($_POST['ruby'])?><?php endif;?>" placeholder="" class ="box"><span style="color: #c9171e"><?php if(isset($err['ruby'])):?><?=h($err['ruby']) ?><?php endif;?></span>
                </li>
                <li>
                    <label for="place" >都道府県<span style="color: #c9171e">（必須）</span></label>
               </li>
                <li>
                    <select class="ex" name="prefecture">
            			<?php foreach ($prefecture_h as $key_3 => $val_p):?>
            					<option value="<?=$key_3?>"<?php if(isset($_POST['prefecture'])):?><?=($key_3 == $_POST['prefecture'] ? ' selected' : '') ?><?php endif;?>><?= $val_p?></option>
            			<?php endforeach;?></select>
            			<span style="color: #c9171e"><?php if(isset($err['prefecture'])):?><?=h($err['prefecture']) ?><?php endif;?></span>
                </li>
                <li>
                    <label for="place_2">市区町村<span style="color: #c9171e">（必須）</span></label>
               </li>
                <li>
                <input class="text_box" type ="text" name ="cities" value="<?php if(isset($_POST['cities'])):?><?= h($_POST['cities'])?><?php endif;?>" placeholder="" class ="box"><span style="color: #c9171e"><?php if(isset($err['cities'])):?><?=h($err['cities']) ?><?php endif;?></span>
                </li>
                <li>
                    <label for="ex">番地<span style="color: #c9171e">（必須）</span></label>
                </li>
                <li>
                    <input class="text_box" type ="text" name ="ex" value="<?php if(isset($_POST['ex'])):?><?= h($_POST['ex'])?><?php endif;?>" placeholder="" class ="box"><span style="color: #c9171e"><?php if(isset($err['ex'])):?><?=h($err['ex']) ?><?php endif;?></span>
                </li>
                <li>
                    <label for="apartment">マンション名等</label>
                </li>
                <li>
                    <input class="text_box" type ="text" name ="apartment" value="<?php if(isset($_POST['apartment'])):?><?= h($_POST['apartment'])?><?php endif;?>" placeholder="" class ="box">
                </li>
                <li>
                		<label for="old">年齢</label>
                </li>
                <li>
                	<input class="text_box" type ="text" name ="old" value="<?php if(isset($_POST['old'])):?><?= h($_POST['old']) ?><?php endif;?>" placeholder="" class ="box">
                </li>
                 <li>
                    <label for="tel">電話番号</label>
               </li>
                <li>
                    <input class="text_box" type ="text" name ="tel" value="<?php if(isset($_POST['tel'])):?><?= h($_POST['tel']) ?><?php endif;?>" placeholder="" class ="box"><span style="color: #c9171e"></span>
                </li>
                <li>
                		<label for="mail">メールアドレス<span style="color: #c9171e">（必須）</span></label>
                	</li>
                	<li>
                		<input class="text_box" type = "text" name ="mail" value="<?php if(isset($_POST['mail'])):?><?=h($_POST['mail']) ?><?php endif;?>"><span style="color: #c9171e"><?php if(isset($err['mail'])):?><?=h($err['mail']) ?><?php endif;?></span>
                	</li>
                <li>
                		<label for="mail_check">メールアドレス確認<span style="color: #c9171e">（必須）</span></label>
                	</li>
                	<li>
                		<input class="text_box" type = "text" name ="mail_check" value="<?php if(isset($_POST['mail_check'])):?><?= h($_POST['mail_check']) ?><?php endif;?>"><span style="color: #c9171e"><?php if(isset($err['mail_check'])):?><?=h($err['mail_check']) ?><?php endif;?></span>
                	</li>
                <li>
                		<label for="content">お問い合わせ内容<span style="color: #c9171e">（必須）</span></label>
                	</li>
                	<li>
                		<div id="content">
                			<textarea class="text_box"  name ="content" ><?php if(isset($_POST['content'])):?><?=h($_POST['content']) ?><?php endif;?></textarea><span style="color: #c9171e"><?php if(isset($err['content'])):?><?=h($err['content']) ?><?php endif;?></span>
                		</div>
                	</li>
            </ul>
    		</div>
        <div id="btn">
                    		<input id="submit_button" class="text_box" type= "submit" name="send" value="確認画面へ">
                    		<input type="hidden" name="token" value="<?= h($token)?>">
        </div>
    </div>
    </form>
</div>
<footer>
    <div class="footer">
    <p>
    		<small>Copyrights&copy;2015 ジェルネイルオフランキング,Inc. All Rights Reserved.</small>
    		</p>
    </div>
</footer>
</body>
</html>
