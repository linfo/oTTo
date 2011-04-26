<?php
  define(DIR,'../');
  require_once DIR.'init.php';
  checkData($_POST['image'],STR);

  if ($user['tracker_access'])
  {
    $sql = "
	    select imagename, imageid from `".TABLE_PREFIX."images`
	      where imageid = '".$_POST['image']."'
	    ";
    $image = $db->query_first($sql);
    if (is_file('../'.$config['image_screenshot'].$image['imageid'].strrchr($image['imagename'],'.')))
    {
      unlink('../'.$config['image_screenshot'].$image['imageid'].strrchr($image['imagename'],'.'));
      unlink('../'.$config['image_screenshot'].$image['imageid'].'-mini'.strrchr($image['imagename'],'.'));
      $sql = "
	      DELETE FROM `".TABLE_PREFIX."images`
		where imageid = '".$image['imageid']."'
	      ";
      $db->query($sql);
    }
  }
?>