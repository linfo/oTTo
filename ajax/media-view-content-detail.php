<?php
    define(DIR,'..');
    require_once(DIR.'/init.php');
    
    checkData($_POST['content'],INT);

            $sql = "
                    SELECT tc.*, tv.video_type
                        FROM `".TABLE_PREFIX."content` as tc,
			     `".TABLE_PREFIX."video` as tv
                        WHERE tc.contentid = ".$_POST['content']."
                            and tc.videoid = tv.videoid
                        LIMIT 0,1
                    ";
            $content = $db->query_first($sql);

            if (isset($content['contentid']))
            {
		$view['video'] = $mediatype[$content['video_type']]['view_video'];
		$view['sound'] = $mediatype[$content['video_type']]['view_sound'];

		// привязываение скриншотов
		$sql = "
			SELECT imagename, imageid FROM `".TABLE_PREFIX."images`
			  WHERE contentid = ".$content['contentid']."
			    AND imagetype = 'screenshot'
			";
		$query = $db->query($sql);
		while ($screenshot = $db->fetch_assoc($query))
		{
		  $screenshot['url'] = $config['image_screenshot'].$screenshot['imageid'].strrchr($screenshot['imagename'],'.');
		  $screenshot['url_mini'] = $config['image_screenshot'].$screenshot['imageid'].'-mini'.strrchr($screenshot['imagename'],'.');
		  eval('$screenshots .= "'.fetch_template('content-screenshot','../').'";');
		}

                eval('$view = "'.fetch_template('media-description','../').'";');
                $view = str_replace('\t','',$view);
                $view = str_replace('\r','',$view);
                $view = str_replace('\n','',$view);
                $view = trim($view);
                echo $view;
            }
            else
            {
                echo 'Ошибка запроса';
            }
?>