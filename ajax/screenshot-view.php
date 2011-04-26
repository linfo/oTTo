<?php
  define(DIR,'../');
  require_once DIR.'init.php';
  checkData($_POST['contenthash'],STR);
  checkData($_POST['content'],INT);

  if ($user['tracker_access'])
  {
    $screengallery = '';
    if (!empty($_POST['contenthash']))
    {
      $sql = "
	      select * 
		from `".TABLE_PREFIX."images` 
		where message_hash = '".$_POST['contenthash']."'
		  and imagetype = 'screenshot'
	      ";
    }
    if (!empty($_POST['content']))
    {
      $sql = "
	      select * 
		from `".TABLE_PREFIX."images` 
 		where contentid = '".$_POST['content']."'
		  and imagetype = 'screenshot'
	      ";
    }
    if (!empty($sql))
    {
      $view['script'] = true;
      $query = $db->query($sql);
      while ($screen = $db->fetch_assoc($query))
      {
	$screen['delete'] = false;
	$screen['url'] = $config['image_screenshot'].$screen['imageid'].strrchr($screen['imagename'],'.');
	$screen['url_mini'] = $config['image_screenshot'].$screen['imageid'].'-mini'.strrchr($screen['imagename'],'.');
	eval('$screengallery .= "' . fetch_template('screen-gallery-miniimage','../') .'";');
	$view['script'] = false;
      }
    }
    echo $screengallery;
  }
?>