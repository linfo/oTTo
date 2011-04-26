<?php
    define(DIR,'..');
    require_once DIR.'/init.php';

    $sql = "
            SELECT tpl_id, tpl_name FROM `".TABLE_PREFIX."templates`
                WHERE style_id = ".STYLE_ID."
            ";

    $query = $db->query($sql);
    $script = true;
    while ($tpl = $db->fetch_assoc($query))
    {
        eval('$tpl_view .= "'.fetch_template('admin-tpl-view',DIR.'/').'";');
        $script = false;
    }
    echo $tpl_view;
?>