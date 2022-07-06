<?php


class MyDB extends SQLite3
{
    function __construct()
    {
        $this->open('../public_db/users.db');
    }
}


function startSession($isUserActivity=true, $prefix=null) {
	$sessionLifetime = 3600;
	$idLifetime = 500;

	if ( session_id() ) return true;
	session_name('MYPROJECT'.($prefix ? '_'.$prefix : ''));
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