<?php
session_start();


class MyDB extends SQLite3
{
    function __construct() {
		$path = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'].'/../public_db/users.db');
        $this->open($path);
    }
}


function isLoggedIn()
{
	if (!isset($_SESSION["user_name"]))
	{
		return false;
	}
	$sessionLifetime = 3600;
	$t = time();

	
	if ( isset($_SESSION['lastactivity'])
		&& ($t-$_SESSION['lastactivity']) <= $sessionLifetime)
	{
		$db = new MyDB();
		$result = $db->query("SELECT contact_id from registered where email=\"".$_SESSION['user_name']."\"");
		$arr = $result->fetchArray();
		if (!$arr)
		{
			return false;
		}
		$contact_id = $arr["contact_id"];
		
		$result = $db->query("SELECT time_stamp from authorized where session_key=\"".$_SESSION["user_m1"]."\"");
		$arr = $result->fetchArray();

		$saved_time = $arr["time_stamp"];
		if ( (time() - intval($saved_time)) <= 3600)
		{
			return true ;
		}
	}
	return false;
}

function startSession($isUserActivity=true, $prefix=null)
{
	$sessionLifetime = 3600;
	$idLifetime = 500;

	if ( session_id() ) return true;
	session_name('idimus.xyz'.($prefix ? '_'.$prefix : ''));
	ini_set('session.cookie_lifetime', 0);
	if ( ! session_start() ) return false;

	$t = time();

	if ( $sessionLifetime ) {
		if ( isset($_SESSION['lastactivity']) && $t-$_SESSION['lastactivity'] >= $sessionLifetime ) {
			destroySession();
			return false;
		}
		else {
			if ( $isUserActivity ) $_SESSION['lastactivity'] = $t;
		}
	}

	if ( $idLifetime ) {
		if ( isset($_SESSION['starttime']) ) {
			if ( $t-$_SESSION['starttime'] >= $idLifetime ) {
				session_regenerate_id(true);
				$_SESSION['starttime'] = $t;
			}
		}
		else {
			$_SESSION['starttime'] = $t;
		}
	}

	return true;
}

function destroySession() {
	if ( session_id() ) {
		session_unset();
		setcookie(session_name(), session_id(), time()-60*60*24);
		session_destroy();
	}
}


function hash_2($a, $b)
{
    $ctx = hash_init('sha3-512');
    hash_update($ctx, $a);
    hash_update($ctx, $b);
    $mm = hash_final($ctx);
    return $mm;
}

?>