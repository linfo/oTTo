<?php
    define(DIR,'..');
    require_once(DIR.'/init.php');
    
    checkData($_GET['page'],STR);


	    $sql = "
		    SELECT tc.contentid, tc.episod, tc.episod_end, tc.season, tc.insertdate, tc.all_season,
			    tc.video_quality, tc.sound_quality, tc.translate_group, tv.*,
			    ti.imageid, ti.imagename
			FROM `".TABLE_PREFIX."content` as tc, `".TABLE_PREFIX."video` as tv
			  LEFT JOIN (
			    SELECT imagename, imageid, videoid FROM `".TABLE_PREFIX."images` WHERE imagetype = 'teaser'
			    ) as ti ON ti.videoid = tv.videoid
			WHERE tc.videoid = tv.videoid
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
		eval('$main .= "'.fetch_template('last_10',DIR.'/').'";');
	}
		echo $main;
?>