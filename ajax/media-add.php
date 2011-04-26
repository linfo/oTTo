<?php
  define(DIR,'../');
  require_once(DIR.'init.php');

  if ($user['tracker_access'])
  {
    checkData($_POST['type'],STR);
    
    $view['serial'] = false;
    $view['playlist'] = false;
    $view['hentai'] = true;
    switch ($_POST['type'])
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
    $do = "checkaddmedia";
    $media['hash'] = generateHash();

    eval('$content = "'.fetch_template('form-media',DIR).'";');
    echo $content;
  }
?>