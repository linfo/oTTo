<?php
    define(DIR,'../');
    require_once(DIR.'init.php');

    checkData($_POST['login'],STR);
    checkData($_POST['password'],STR);

    if (isset($_POST['exit']))
    {
      echo "0";
      // убивание кук )
      setcookie('dle_user_id','',0,'/',$config['dle_cookie_domain']);
      setcookie('dle_password','',0,'/',$config['dle_cookie_domain']);
    }
    else
    {
      if ((!empty($_POST['login']))&&(!empty($_POST['password'])))
      {
	$db->select_db($config['dle_database'],$config['db_charset']);// or die("Error DataBase");
	$sql = "
		select user_id,name from `".$config['dle_prefix']."users`
		  where name = '".$_POST['login']."'
		    and password = '".md5(md5($_POST['password']))."'
		";
	$auth = $db->query_first($sql);
	if (isset($auth['user_id']))
	{
	  echo $auth['name'];
	  // добавить куки )
	  setcookie('dle_user_id',$auth['user_id'],(time()+3600*24*365),'/',$config['dle_cookie_domain']);
	  setcookie('dle_password',md5($_POST['password']),(time()+3600*24*365),'/',$config['dle_cookie_domain']);
	}
	else
	{
	  echo "0";
	}
	$db->select_db($config['db_database'],$config['db_charset']);// or die("Error DataBase");
      }
    }
    
?>