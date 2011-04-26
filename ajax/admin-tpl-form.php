<?php
    define(DIR,'..');
    require_once DIR.'/init.php';
    checkData($_POST['tpl_id'],INT);
    
    if (($_POST['tpl_id']>0)&&($user['tracker_access']))
    {
        $sql = "
                SELECT * FROM `".TABLE_PREFIX."templates`
                    WHERE tpl_id = ".$_POST['tpl_id']."
                ";

        $tpl = $db->query_first($sql);
    }
    if ($user['tracker_access'])
    {
        eval('$tpl_view = "'.fetch_template('admin-tpl-form',DIR.'/').'";');
        echo $tpl_view;
    }
?>