<?php
require_once('library/library.php');
if (@$_POST['accept']) {
    Account::login($_REQUEST['login_id'], $_REQUEST['login_pass']) ?
        header("Location:admin_top") : $_REQUEST['err'] = 1;
} else {
    session_destroy();
}

Html::indexHeader($access_device);
Html::body(__FILE__, $access_device);
Html::footer($access_device);
