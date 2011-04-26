<?php
    define(DIR,'../');
    require_once(DIR.'init.php');
    if ($user['tracker_access'])
    {
      $do = "checkedittorrent";

      checkData($_POST['content'],INT);
      $sql = "
	      select tv.name_english, tv.name_russian, tv.seasons, tv.video_type, tc.*
		  from `".TABLE_PREFIX."video` as tv, `".TABLE_PREFIX."content` as tc
		  WHERE tc.contentid = '".$_POST['content']."'
		    and tc.videoid = tv.videoid
	      ";
      $content = $db->query_first($sql);
      if ($content['episod_end']==0) $content['episod_end'] = '';
      $view['serial'] = false;
      $view['playlist'] = false;
      $view['sound'] = $mediatype[$content['video_type']]['view_sound'];
      $view['video'] = $mediatype[$content['video_type']]['view_video'];
      $view['hentai'] = true;
      $view['season_text'] = "Сезон";
      $view['episod_text'] = "Серия";
      switch ($content['video_type'])
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
	      $view['season_text'] = "Том";
	      $view['episod_text'] = "Главы";
	      break;
	  case "animovie":
	  case "movie":
	      break;
	  case "game":
	      break;
	  default:
	      header('location: index.php');
      }
      eval('$contents = "'.fetch_template('form-torrent',DIR).'";');
      echo $contents;
    }
?>