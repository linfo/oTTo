<?php
    define(DIR,'..');
    require_once(DIR.'/init.php');
    
    checkData($_POST['bookmark'],INT);

    $sql = "select bookmark_id from `".TABLE_PREFIX."user_bookmarks` where user_id = '".$user['user_id']."' and bookmark_id = '".$_POST['bookmark']."'";
    $query = $db->query($sql);
    if ($db->num_rows($query)>0)
    {
	$sql = "delete from `".TABLE_PREFIX."user_bookmarks` where user_id = '".$user['user_id']."' and bookmark_id = '".$_POST['bookmark']."'";
	$db->query($sql);
    }
?>