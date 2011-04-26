<?php
  define(DIR,'../');
  require_once(DIR.'init.php');
  if ($user['tracker_access'])
  {
    $view = '';
    checkData($_POST['videoid'],INT);
    checkData($_POST['action'],STR);
    checkData($_POST['message_hash'],STR);

    switch ($_POST['action'])
    {
      case "insert":
	  if (is_file($_FILES['teaser']['tmp_name']))
	  {
	$exif_file = exif_imagetype($_FILES['teaser']['tmp_name']);
	$exif_allow = array(IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG);

	checkData($_FILES['teaser']['name'],STR);

	if (in_array($exif_file,$exif_allow))
	{
	  $ext = strrchr($_FILES['teaser']['name'],'.');
	  $sql = "
		  INSERT INTO `".TABLE_PREFIX."images` 
		      (
		        `imagetype`,
		        `message_hash`,
		        `videoid`,
		        `imagename`,
		        `insertdate`,
		        `insert_user_id`
		        )
		      VALUES
		      (
		        'teaser',
		        '".$_POST['message_hash']."',
		        '".$_POST['videoid']."',
		        '".$_FILES['teaser']['name']."',
		        '".time()."',
		        '".$user['user_id']."'
		      )
		  ";
	  $db->query($sql);
	  $imageid = zerofill($db->insert_id(),10);
	  copy($_FILES['teaser']['tmp_name'],'../'.$config['image_teaser'].$imageid.'-full'.$ext);
	  resizeimage($_FILES['teaser']['tmp_name'],'../'.$config['image_teaser'].$imageid.'-mini'.$ext,checkExif($_FILES['teaser']['tmp_name']),$config['image_teaser_mini_width'],$config['image_teaser_mini_height']);
	  resizeimage($_FILES['teaser']['tmp_name'],'../'.$config['image_teaser'].$imageid.$ext,checkExif($_FILES['teaser']['tmp_name']),$config['image_teaser_norm_width'],$config['image_teaser_norm_height']);
	  list($width, $height, $type, $attr) = getimagesize($_FILES['teaser']['tmp_name']);
	  echo "
	  <img id=imgteaser src='./upload/teaser/".$imageid."-full".$ext."' border=0".(($width>400)?(" width=400"):(""))."><br />
	  <form id='form-teaser' enctype='multipart/form-data' type='POST'>
	  ".((isset($_POST['videoid']))?("<input type=hidden name=videoid value='".$_POST['videoid']."'>"):("<input type=hidden name=message_hash value='".$_POST['message_hash']."'>"))."
	    <input type=hidden name=action value='delete'>
	    <input type=hidden name=type value='teaser'>
	  </form>
	  <a id=\"deleteteaser\" href=\"#\">Удалить</a>
	  <script>
	    $('#deleteteaser').click(function(){
	      $.ajax({
		url: './ajax/teaser.php',
		type: 'POST',
		data: $('#form-teaser').serialize(),
		success: function(data){
		  $('#teaser').html(data);
		}
	      });
	    });
	  </script>
	  ";
	} else {$view = 'upload_file';}
	} else {$view = 'upload_file';}
	break;
      case "delete":
	if (isset($_POST['message_hash'])&&(!empty($_POST['message_hash'])))
	{
	  $condition = "WHERE message_hash = '".$_POST['message_hash']."'";
	} else {
	  $condition = "WHERE videoid = '".$_POST['videoid']."'";
	}
	  $sql = "
		  SELECT imageid,imagename FROM `".TABLE_PREFIX."images`
		      ".$condition."
		      LIMIT 0,1
		  ";
	$image = $db->query_first($sql);
	if (is_file('../upload/teaser/'.$image['imageid'].strrchr($image['imagename'],'.')))
	{
	  unlink('../upload/teaser/'.$image['imageid'].strrchr($image['imagename'],'.'));
	  unlink('../upload/teaser/'.$image['imageid'].'-mini'.strrchr($image['imagename'],'.'));
	  unlink('../upload/teaser/'.$image['imageid'].'-full'.strrchr($image['imagename'],'.'));
	  $sql = "
		  DELETE FROM `".TABLE_PREFIX."images`
		    ".$condition."
		  ";
	  $db->query($sql);
	}
	$view = 'upload_file';
	break;
    }
    if ($view == 'upload_file')
    {
	  echo "
	  <span id='teaser-hidden'>
	    <form id='form-teaser' enctype='multipart/form-data' action='./ajax/teaser.php'>
	    ".((isset($_POST['videoid']))?("<input type=hidden name=videoid value='".$_POST['videoid']."'>"):("<input type=hidden name=message_hash value='".$_POST['message_hash']."'>"))."
	      <input type=hidden name=action value='insert'>
	      <input type=hidden name=type value='teaser'>
	      <input type=file name=teaser id='teaser-file'>
	    </form>
	  </span><span id='teaser-load' style='display:none'>Загрузка...</span>
	  <script>
	  $(function(){
	    $('#form-teaser').submit(function() {
	      $('#teaserbutton').attr('disabled','disabled');
	      $('#form-teaser').ajaxSubmit({
		url: 'ajax/teaser.php',
		type: 'POST',
		data: $('#form-teaser').formSerialize(),
		success: function(data)
		{
		  $('#teaser-load').css('display','none');
		  $('#teaser-hidden').css('display','block');
		  $('#teaser').html(data);
		}
	      });
	      return false;
	    });
	    $('#teaser-file').change(function(){
	      if ($('#teaser-file').val()!='')
	      {
		$('#teaser-load').css('display','block');
		$('#teaser-hidden').css('display','none');
		$('#form-teaser').submit();
	      }
	    })
	  });
	  </script>
	  ";
    }
  }
?>