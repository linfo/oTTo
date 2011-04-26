<?php
    define(DIR,'../');
    require_once(DIR.'init.php');

    if ($user['tracker_access'])
    {
      $do = "checkeditmedia";

      checkData($_POST['media'],INT);
      $sql = "
	      SELECT * FROM `".TABLE_PREFIX."video`
		  WHERE videoid = ".$_POST['media']."
	      ";
      $media = $db->query_first($sql);

      $sql = "
	      SELECT * FROM `".TABLE_PREFIX."images`
		  WHERE videoid = ".$_POST['media']."
	      ";
      $teaser = $db->query_first($sql);


    $view['serial'] = false;
    $view['playlist'] = false;
    $view['hentai'] = true;
    switch ($media['video_type'])
    {
        case "aniserial":
        case "serial":
        case "ova":
            $view['serial'] = true;
            $view['serial_text'] = 'сезонов';
            $view['playlist'] = true;
            $view['playlist_text'] = 'серий';
            break;
        case "ost":
            $view['playlist'] = true;
            $view['playlist_text'] = 'треков';
            $view['hentai'] = false;
            break;
        case "manga":
            $view['serial'] = true;
            $view['serial_text'] = 'томов';
            break;
        case "animovie":
        case "movie":
        case "game":
            break;
        default:
            header('location: index.php');
    }
      if (!empty($teaser['imageid']))
      {
          $media['teaser'] = $teaser['imageid']."-full".strrchr($teaser['imagename'],'.');
      }

      eval('$content = "'.fetch_template('form-media',DIR).'";');
      echo $content;
    }
?>