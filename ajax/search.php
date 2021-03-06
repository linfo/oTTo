<?php
  /*----------------------------------------------------------------
  |
  | 	oTTo. Torrent-Catalog. DLE & XBT
  | 		Module: AJAX Search.
  |
  | 	(c) 2011. Copyright LInfo. Licence: GPL
  |		http://otto.21ru.net/
  |
  `---------------------------------------------------------------*/
  define(DIR,'../');
  require_once (DIR.'init.php');
  
  checkData($_GET['term'],STR);
  checkData($_GET['search_text'],STR);
  checkData($_GET['page'],INT);


  if (strlen($_GET['term'])>2)
  {
    $sql = "
	    (
	      SELECT name_english as search_text
		FROM `".TABLE_PREFIX."video`
		WHERE `name_english` LIKE '%".$_GET['term']."%'
		  and number_content > 0
		LIMIT 0,10
	    ) UNION (
	      SELECT name_russian as search_text
		FROM `".TABLE_PREFIX."video`
		WHERE `name_russian` LIKE '%".$_GET['term']."%'
		  and number_content > 0
		LIMIT 0,20
	    ) UNION (
	      SELECT name_japan search_text
		FROM `".TABLE_PREFIX."video`
		WHERE `name_japan` LIKE '%".$_GET['term']."%'
		  and number_content > 0
		LIMIT 0,10
	    )
	    ";
    $query = $db->query($sql);
    echo "[ ";
    $new = true;
    while ($row = $db->fetch_assoc($query))
    {
      if (!$new) echo " , ";
      echo '{ "id": "'.$row['search_text'].'", "label": "'.$row['search_text'].'", "value": "'.$row['search_text'].'" }';
      $new = false;
    }
    echo " ]";
  } elseif (strlen($_GET['search_text'])>2) {
    $sql = "
	    select count(tc.contentid) as `allitem`
		FROM `".TABLE_PREFIX."content` as tc, `".TABLE_PREFIX."video` as tv
		WHERE tc.videoid = tv.videoid
		  and (
			tv.name_english like '%".$_GET['search_text']."%'
		      or tv.name_russian like '%".$_GET['search_text']."%'
		      or tv.name_japan like '%".$_GET['search_text']."%'
		      or tv.description like '%".$_GET['search_text']."%'
		      or tc.episodname_english like '%".$_GET['search_text']."%'
		      or tc.episodname_russian like '%".$_GET['search_text']."%'
		      or tc.note like '%".$_GET['search_text']."%'
		      )
	    ";
    $statistic = $db->query_first($sql);
    $statistic['first_item'] = $_GET['page']*$config['item_by_page']+1;
    $statistic['last_item'] = ($_GET['page']+1)*$config['item_by_page'];
    if ($statistic['allitem']<$statistic['last_item']) $statistic['last_item'] = $statistic['allitem'];
    if ($statistic['allitem']>$config['item_by_page']) $pagelist = true;
    $sql = "
	    SELECT tc.contentid, tc.episod, tc.episod_end, tc.season, tc.insertdate, tc.all_season,
		    tc.video_quality, tc.sound_quality, tc.translate_group, tv.*,
		    ti.imageid, ti.imagename
		FROM `".TABLE_PREFIX."content` as tc, `".TABLE_PREFIX."video` as tv
		  LEFT JOIN (
		    SELECT imagename, imageid, videoid FROM `".TABLE_PREFIX."images` WHERE imagetype = 'teaser'
		    ) as ti ON ti.videoid = tv.videoid
		WHERE tc.videoid = tv.videoid
		  and (
			tv.name_english like '%".$_GET['search_text']."%'
		      or tv.name_russian like '%".$_GET['search_text']."%'
		      or tv.name_japan like '%".$_GET['search_text']."%'
		      or tv.description like '%".$_GET['search_text']."%'
		      or tc.episodname_english like '%".$_GET['search_text']."%'
		      or tc.episodname_russian like '%".$_GET['search_text']."%'
		      or tc.note like '%".$_GET['search_text']."%'
		      )
		ORDER BY tc.insertdate desc
		LIMIT ".($_GET['page']*$config['item_by_page']).",".$config['item_by_page']."
	    ";
    $query = $db->query($sql);
    while ($row = $db->fetch_assoc($query))
    {
      $view['season'] = false;
      $view['episod'] = false;
      switch ($row['video_type'])
      {
	  case "aniserial":
	  case "serial":
	      $view['season'] = true;
	      $view['episod'] = true;
	      $view['season_text'] = "Сезон";
	      $view['episod_text'] = "Эпизод";
	      $view['episods_text'] = "Эпизоды";
	      $view['subtype_text'] = "Сериал";
	      break;
	  case "ova":
	      $view['episod'] = true;
	      $view['episod_text'] = "Эпизод";
	      $view['episods_text'] = "Эпизоды";
	      $view['subtype_text'] = "OVA";
	      break;
	  case "game":
	      $view['subtype_text'] = "Игра";
	      break;
	  case "ost":
	      $view['subtype_text'] = "Музыка";
	      break;
	  case "manga":
	      $view['season'] = true;
	      $view['episod'] = true;
	      $view['season_text'] = "Том";
	      $view['episod_text'] = "Глава";
	      $view['episods_text'] = "Главы";
	      $view['subtype_text'] = "Книга";
	      break;
	  case "movie":
	  case "animovie":
	      $view['subtype_text'] = "Фильм";
	      break;
      }
      $row['insertdate'] = date("d.m.Y H:i",$row['insertdate']);
      $row['season_un'] = zerofill($row['season'],2);
      $row['episod_un'] = zerofill($row['episod'],2);
      $row['image_url'] = $config['image_teaser'].$row['imageid'].'-mini'.strrchr($row['imagename'],'.');
      $row['episod_end_un'] = zerofill($row['episod_end'],2);
      eval('$search .= "'.fetch_template('last_10',DIR).'";');
    }
    if (strlen($search)>0)
    {
      eval('$search = "'.fetch_template('search-list',DIR).'";');
      echo $search;
    } else {
      echo "Ничего не найдено";
    }
    
  } else {
      echo "Слишком маленькое слово для поиска!";
  }
  
  
?>