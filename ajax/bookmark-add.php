<?php
    define(DIR,'..');
    require_once(DIR.'/init.php');
    
    checkData($_POST['mediaid'],INT);

    if (($_POST['mediaid']>0)&&($user['user_id']>0))
    {
        $sql = "
            insert into ".TABLE_PREFIX."user_bookmarks (
                `user_id`,
                `media_id`,
                `datetime`
                ) VALUES (
                '".$user['user_id']."',
                '".$_POST['mediaid']."',
                '".date("Y-m-d H:i:s")."'
                )
                ";
        $db->query($sql);
        echo "1";
    } else {
        echo "0";
    }
?>