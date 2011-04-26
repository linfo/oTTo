<?php
    define(DIR,'..');
    require_once DIR.'/init.php';

    checkData($_POST['editornote'],STR);
    checkData($_POST['content'],INT);

    if ((!empty($_POST['editornote']))&&($_POST['content']))
    {
        $editornote = $_POST['editornote'];
        $date = date("d.m.Y",time());
        $time = date("H:i",time());
        eval('$editornote = "'.fetch_template('editornote-view','../').'";');

        $bbtext->parse($editornote);
        $editornote_un = $bbtext->get_html();

        $sql = "
                UPDATE `".TABLE_PREFIX."content`
                    SET 
                        `editornote` = CONCAT('".$editornote."',`editornote`),
                        `editornote_un` = CONCAT('".$editornote_un."',`editornote_un`)
                    WHERE contentid = ".$_POST['content']."
                ";
        $db->query($sql);
        echo $editornote_un;
    }
?>