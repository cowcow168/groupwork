<?php
$mente_start = '不具合発生中につきアップデートが完了するまでは閉鎖中';
$mente_end = '終了時間未定の';
?>
<html>
<head>
	<meta charset="utf-8">
	<meta names="robots" contents="noindex">
	<style>
		div {
			position: relative;
		}
		span {
			position: absolute;
			bottom: 36.0%;
			left: 20%;
			font-size: 1.4rem;
		}
	</style>
</head>
<body>
	<div style="display: block; width: 60rem; height: 40rem; margin: 0 auto; text-align: center;">
		<img src="/library/maintenance_img/maintenance_middle.png">
		<span><?=$mente_start?></span>
	</div>
</body>
</html>