<?php
// session_start(["cookie_lifetime" => 3600]);

header('Access-Control-Allow-Origin: *'); 
header("Content-Type: application/json; charset=UTF-8");
// echo json_encode($_POST);

function hash_2($a, $b)
{
    $ctx = hash_init('sha3-512');
    hash_update($ctx, $a);
    hash_update($ctx, $b);
    $mm = hash_final($ctx);
    return $mm;
}

class MyDB extends SQLite3
{
    function __construct()
    {
        $this->open('../public_db/users.db');
    }
}

$db = new MyDB();


$result = $db->query("SELECT * FROM registered");

$db_is_empty = true;
if ($result->fetchArray()) {
  $db_is_empty = false;
}

if ($db_is_empty)
{
    if (!isset($_POST['user']) || !isset($_POST['salt']) || !isset($_POST['verifier']))
    {
        echo '{"operation":"failed"}';
        die;
    }
    $stmt = $db->prepare("INSERT INTO registered (email, salt, verifier) VALUES (:email, :salt, :verifier)");
    $stmt->bindParam(':email', $_POST['user'], SQLITE3_TEXT);
    $stmt->bindParam(':salt', $_POST['salt'], SQLITE3_TEXT);
    $stmt->bindParam(':verifier', $_POST['verifier'], SQLITE3_TEXT);
    $stmt->execute();
    echo '{"operation":"added"}';
    die;
}
else if (isset($_POST["mode"]) && $_POST["mode"] == "NARE")
{
    if (!isset($_POST['user']) || !isset($_POST['salt']) || !isset($_POST['verifier']))
    {
        echo '{"operation":"failed"}';
        die;
    }

    $result = $db->query("SELECT email FROM registered WHERE email=\"".$_POST['user']."\"");
    if ($result->fetchArray())
    {   
        echo '{"operation":"failed"}';
        die;
    }

    $result = $db->query("SELECT email FROM waiting WHERE email=\"".$_POST['user']."\"");
    if ($result->fetchArray())
    {
        echo '{"operation":"failed"}';
        die;
    }

    $stmt = $db->prepare("INSERT INTO waiting (email, salt, verifier) VALUES (:email, :salt, :verifier)");
    $stmt->bindParam(':email', $_POST['user'], SQLITE3_TEXT);
    $stmt->bindParam(':salt', $_POST['salt'], SQLITE3_TEXT);
    $stmt->bindParam(':verifier', $_POST['verifier'], SQLITE3_TEXT);
    $stmt->execute();
    echo '{"operation":"added"}';
    die;
}
else if (isset($_POST['user']) && isset($_POST['A'])) //trying to authentificate
{
    $verifier = gmp_init(0);
    $salt = gmp_init(0);

    $email = $_POST['user'];
    $A = gmp_init("0x".$_POST['A']);

    $result = $db->query("SELECT verifier, salt FROM registered WHERE email=\"".$email."\"");

    while ($res = $result->fetchArray())
    {
        $verifier = gmp_init("0x".$res["verifier"]);
        $salt = gmp_init("0x".$res["salt"]);
        break;
    }

    if ((gmp_cmp ($verifier, 0) == 0) || (gmp_cmp($salt, 0) == 0))
    {
        echo '{"operation":"failed"}';
        die;
    }

    $N = gmp_init("0xFFFFFFFFFFFFFFFFC90FDAA22168C234C4C6628B80DC1CD129024E088A67CC74020BBEA63B139B22514A08798E3404DDEF9519B3CD3A431B302B0A6DF25F14374FE1356D6D51C245E485B576625E7EC6F44C42E9A637ED6B0BFF5CB6F406B7EDEE386BFB5A899FA5AE9F24117C4B1FE649286651ECE45B3DC2007CB8A163BF0598DA48361C55D39A69163FA8FD24CF5F83655D23DCA3AD961C62F356208552BB9ED529077096966D670C354E4ABC9804F1746C08CA18217C32905E462E36CE3BE39E772C180E86039B2783A2EC07A28FB5C55DF06F4C52C9DE2BCBF6955817183995497CEA956AE515D2261898FA051015728E5A8AACAA68FFFFFFFFFFFFFFFF");

    $g = gmp_init(2);
    $k = gmp_init("0x".(hash_2(gmp_export($N), gmp_export($g))));
    $b = gmp_random_range("0x00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000", "0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF");
    // var B = (bigInt(k.toString(), 16).multiply(verifier)).add(g.modPow(b,N))

    $B = gmp_add(gmp_mul($k,$verifier), gmp_powm($g,$b,$N));

    $u = gmp_init("0x".(hash_2(gmp_export($A),gmp_export($B))));
    // var ss = (A.multiply((verifier.modPow(u,N)))).modPow(b,N);

    $ss = gmp_powm(gmp_mul($A,gmp_powm($verifier, $u, $N)), $b, $N);
    $m1 = hash("sha3-512",gmp_export(gmp_xor(gmp_xor($A, $B), $ss)));

    $_SERVER["user_m1"] = $m1;


    echo '{"operation":"authorized", "salt":"'.gmp_strval($salt,16).'", "B":"'.gmp_strval($B,16).'"}';

    // echo '{"operation":"authorized", "salt":"'.gmp_strval($salt,16).'", "B":"'.gmp_strval($B,16).'", "ss":"'.gmp_strval($ss,16).'","b":"'.gmp_strval($b,16).'","verifier":"'.gmp_strval($verifier,16).'"}';
}
else if (isset($_POST['user']) && isset($_POST['m1'])) //trying to authentificate
{
    if ($_POST['user'] == $_SESSION["user_email"])
    {
        echo $_SERVER["user_m1"];
    }
}
else
{
    echo '{"operation":"failed"}';
    die;

}
?>
