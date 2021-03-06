<?php
    define(DIR,'../');
    require_once(DIR.'init.php');

    if ($user['tracker_access'])
    {
      checkData($_POST['content'],INT);
      if (is_file(DIR.$config['torrent_folder'].$_POST['content'].'.torrent'))
      {
	  unlink(DIR.$config['torrent_folder'].$_POST['content'].'.torrent');
	  $sql = "
		  SELECT info_hash, videoid FROM `".TABLE_PREFIX."content`
		      WHERE contentid = ".$_POST['content']."
		      LIMIT 0,1
		  ";
	  $content = $db->query_first($sql);
	  $sql = "
		  DELETE FROM `".TABLE_PREFIX."content`
		      WHERE contentid = ".$_POST['content']."
		  ";
	  $db->query($sql);

	  $sql = "update xbt_files set flags = 1 where info_hash = '".$content['info_hash']."'";
	  $db->query($sql);

	  // удаление скриншотов
	  $sql = "select * from `".TABLE_PREFIX."images` where contentid = '".$_POST['content']."'";
	  $query = $db->query($sql);
	  while ($images = $db->fetch_assoc($query))
	  {
	    $imagefile = DIR.$config['image_screenshot'].$images['imageid'].strrchr($images['imagename'],'.');
	    $imagefile_mini = DIR.$config['image_screenshot'].$images['imageid'].'-mini'.strrchr($images['imagename'],'.');
	    if (is_file($imagefile))
	    {
	      unlink($imagefile);
	      unlink($imagefile_mini);
	    }
	  }
	  $sql = "DELETE from `".TABLE_PREFIX."images` where contentid = '".$_POST['content']."'";
	  $db->query($sql);

	  // пересчитаем количество контента
	  $sql = "
		  select count(contentid) as `count`
		    from `".TABLE_PREFIX."content`
		    WHERE videoid = ".$content['videoid']."
		    GROUP BY `episod`, `season`, `all_season`
		  ";
	  $number = $db->query_first($sql);
	  if (!isset($number['count'])) {$number['count'] = 0;}
	  $sql = "
		  UPDATE `".TABLE_PREFIX."video`
		    SET
		      `number_content` = ".$number['count']."
		    WHERE videoid = ".$content['videoid']."
		  ";
	  $db->query($sql);

	  if ($number['count']==0)
	  {
	    $popularity['popularity'] = 0;
	  }
	  else
	  {
	    $sql = "
		      SELECT (sum(tc.downloads) / tv.number_content) as `popularity`
			FROM `".TABLE_PREFIX."content` as tc, `".TABLE_PREFIX."video` as tv
			WHERE tv.videoid = ".$content['videoid']."
			  and tv.videoid = tc.videoid
		    ";
	    $popularity = $db->query_first($sql);
	  }
	  // расчет популярности
	  $sql = "
		  UPDATE `".TABLE_PREFIX."video`
		    SET 
		      popularity = '".$popularity['popularity']."'
		    WHERE videoid = ".$content['videoid']."
		  ";
	  $db->query($sql);
      }
    }
?>