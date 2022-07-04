<?php
// session_start(["cookie_lifetime" => 3600]);

$db_not_empty = false;
$mode_force = isset($_GET["mode"]) && $_GET["mode"] == "Nare";

if (!$mode_force) {
  class MyDB extends SQLite3
  {
    function __construct()
    {
      $this->open('../public_db/users.db');
    }
  }

  $db = new MyDB();

  $result = $db->query("SELECT * FROM registered");

  if ($result->fetchArray()) {
    $db_not_empty = true;
  }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="idimus">
  <title>Signin</title>

  <link rel="canonical" href="https://idimus.xyz/work/">

  <link href="./css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
  <meta name="theme-color" content="#712cf9">

  <style>
    .bd-placeholder-img {
      font-size: 1.125rem;
      text-anchor: middle;
      -webkit-user-select: none;
      -moz-user-select: none;
      user-select: none;
    }

    @media (min-width: 768px) {
      .bd-placeholder-img-lg {
        font-size: 3.5rem;
      }
    }

    .b-example-divider {
      height: 3rem;
      background-color: rgba(0, 0, 0, .1);
      border: solid rgba(0, 0, 0, .15);
      border-width: 1px 0;
      box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
    }

    .b-example-vr {
      flex-shrink: 0;
      width: 1.5rem;
      height: 100vh;
    }

    .bi {
      vertical-align: -.125em;
      fill: currentColor;
    }

    .nav-scroller {
      position: relative;
      z-index: 2;
      height: 2.75rem;
      overflow-y: hidden;
    }

    .nav-scroller .nav {
      display: flex;
      flex-wrap: nowrap;
      padding-bottom: 1rem;
      margin-top: -1px;
      overflow-x: auto;
      text-align: center;
      white-space: nowrap;
      -webkit-overflow-scrolling: touch;
    }

    .footer_bottom {
      position: fixed;
      width: 100%;
      left: 0;
      bottom: 0;
      flex: 0;
    }
  </style>


  <link href="./css/signin.css" rel="stylesheet">
</head>

<body class="text-center">
  <main class="form-signin w-100 m-auto">
    <form onsubmit="return false;">
      <h1 class="h3 mb-3 fw-normal">Please sign <?php if ($db_not_empty) {
                                                  echo "in";
                                                } else {
                                                  echo "up";
                                                } ?></h1>

      <div class="form-floating">
        <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com" required>
        <label for="floatingInput">Email address</label>
      </div>
      <div class="form-floating<?php if (!$db_not_empty) {
                                  echo " mainpassword";
                                } ?>">
        <input type="password" class="form-control" id="floatingPassword" placeholder="Password" required>
        <label for="floatingPassword">Password</label>
      </div>


      <?php if ($db_not_empty) : ?>
        <button class="w-100 btn btn-lg btn-primary" type="submit" id="sign_in_button" onclick="signIn()">Sign in</button>
      <?php else : ?>
        <div class="form-floating">
          <input type="password" class="form-control" id="floatingPasswordConfirm" placeholder="Confirm password" required>
          <label for="floatingPasswordConfirm">Confirm</label>
        </div>
        <button class="w-100 btn btn-lg btn-primary" type="submit" id="sign_up_button" onclick="signUp()">Sign up</button>
      <?php endif; ?>

    </form>



    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
          <div class="modal-body">
            <div id="alert_holder"></div>
            <div class="spinner-border text-primary" role="status">
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
      Launch static backdrop modal
    </button> -->

  </main>
  <footer class="my-3 footer_bottom">
    <p class="text-center text-muted">&copy;idimus <?php echo date("Y"); ?></p>
  </footer>

  <script type="text/javascript" src="./js/sha3.js"></script>
  <script type="text/javascript" src="./js/bigInteger.js"></script>
  <script type="text/javascript" src="./js/bootstrap.min.js" crossorigin="anonymous"></script>

  <script type="text/javascript">
    //

    (function() {
      'use strict'

      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      var forms = document.querySelectorAll('.needs-validation')

      // Loop over them and prevent submission
      Array.prototype.slice.call(forms)
        .forEach(function(form) {
          form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
              event.preventDefault()
              event.stopPropagation()
            }

            form.classList.add('was-validated')
          }, false)
        })
    })()

    let spinner_modal = new bootstrap.Modal(document.getElementById("staticBackdrop"));

    function h_hash2(a, b) {
      let sha3 = sha3_512.create();
      sha3.update(a);
      sha3.update(b);

      return sha3.hex(); // check not 0
    }

    function h_hash3(a, b, c) {
      let sha31 = sha3_512(a);
      let sha32 = sha3_512(b);
      let sha33 = sha3_512(c);

      return sha31 | sha32 | sha33; // check not 0
    }

    let for_hash = function(big_int_num) {
      let hex = big_int_num.toString(16);
      if (hex.length % 2 !== 0) {
        hex = '0' + hex;
      }
      let bytes = [];
      for (let n = 0; n < hex.length; n += 2) {
        let code = parseInt(hex.substr(n, 2), 16)
        bytes.push(code);
      }
      return bytes;
    }

    class WordArray {
      constructor(words, sigBytes) {
        this.words = words;
        this.sigBytes = sigBytes;
      }

      toString() {
        return [...new Uint32Array(this.words)]
          .map(x => x.toString(16).padStart(8, '0'))
          .join('');
      }
    }

    function buf2hex(buffer) { // buffer is an ArrayBuffer
      return [...new Uint32Array(buffer)]
        .map(x => x.toString(16).padStart(8, '0'))
        .join('');
    }

    let cryptoSecureRandomInt = function() {
      if (crypto) {
        // Use getRandomValues method (Browser)
        if (typeof crypto.getRandomValues === 'function') {
          try {
            return crypto.getRandomValues(new Uint32Array(1))[0];
          } catch (err) {}
        }

        // Use randomBytes method (NodeJS)
        if (typeof crypto.randomBytes === 'function') {
          try {
            return crypto.randomBytes(4).readInt32LE();
          } catch (err) {}
        }
      }

      throw new Error('Native crypto module could not be used to get secure random number.');
    }

    let secure_random = function(nBytes) {
      let words = [];

      for (let i = 0; i < nBytes; i += 4) {
        words.push(cryptoSecureRandomInt());
      }

      return new WordArray(words, nBytes);
    }


    var N = bigInt("FFFFFFFFFFFFFFFFC90FDAA22168C234C4C6628B80DC1CD129024E088A67CC74020BBEA63B139B22514A08798E3404DDEF9519B3CD3A431B302B0A6DF25F14374FE1356D6D51C245E485B576625E7EC6F44C42E9A637ED6B0BFF5CB6F406B7EDEE386BFB5A899FA5AE9F24117C4B1FE649286651ECE45B3DC2007CB8A163BF0598DA48361C55D39A69163FA8FD24CF5F83655D23DCA3AD961C62F356208552BB9ED529077096966D670C354E4ABC9804F1746C08CA18217C32905E462E36CE3BE39E772C180E86039B2783A2EC07A28FB5C55DF06F4C52C9DE2BCBF6955817183995497CEA956AE515D2261898FA051015728E5A8AACAA68FFFFFFFFFFFFFFFF", 16);

    let g = bigInt(2);

    const encodeFormData = (data) => {
      return Object.keys(data)
        .map(key => encodeURIComponent(key) + '=' + encodeURIComponent(data[key]))
        .join('&');
    }

    <?php if ($db_not_empty) : ?>

      function signIn() {
        let login = document.getElementById("floatingInput").value;
        let password = document.getElementById("floatingPassword").value;
        if (login == "" || password == "") {
          return false;
        }
        location.replace("./index.php");

        let a = bigInt(secure_random(512 / 8).toString(),16);
        let A = g.modPow(a, N); // check not 0

        let data = {};
        data['user'] = login;
        data['A'] = A.toString(16);

        postData('./main.php', data)
          .then(scndata => {
            let k = bigInt(h_hash2(for_hash(N), for_hash(g)), 16);
            let salt = bigInt(scndata['salt'],16);
            let x = bigInt(h_hash2(for_hash(salt), password), 16);
            let B = bigInt(scndata['B'],16);
            let u = bigInt(h_hash2(for_hash(A),for_hash(B)), 16);
            var sc = B.minus(k.multiply(g.modPow(x,N))).modPow(a.add(u.multiply(x)), N);

            // let ss = A.multiply(verifier.modPow(u,N)).modPow(b,N);         

            
            let m1 = sha3_512(for_hash(A.xor(B).xor(sc)));

            // let step2data = {};
            // step2data['user'] = login;
            // step2data['m1'] = m1.toString();

            // postData('./main.php', step2data)
            //   .then(data => {

            //   });
          });
      }

    <?php else : ?>

      function signUp() {
        let login = document.getElementById("floatingInput").value;
        let password = document.getElementById("floatingPassword").value;
        let confirm = document.getElementById("floatingPasswordConfirm").value;

        if (login == "" || password == "" || confirm == "") {
          return false;
        } else if (password != confirm) {
          document.getElementById("floatingPasswordConfirm").setCustomValidity("Passwords Don't Match");
          return false;
        }

        var salt = secure_random(512 / 8);
        salt = bigInt(salt.toString(), 16);
        var hash = h_hash2(for_hash(salt), password); // a.k.a. x
        var verifier = g.modPow(bigInt(hash, 16), N); // check not N and not 0 
        let data = {};
        data['user'] = login;
        data['salt'] = salt.toString(16);
        data['verifier'] = verifier.toString(16);
        <?php if ($mode_force) : ?>
          data['mode'] = "NARE";
        <?php endif; ?>
        
        document.getElementById("alert_holder").innerHTML ="";
        spinner_modal.toggle();

        postData('./main.php', data)
          .then(data => {
            setTimeout(() => {
              spinner_modal.toggle();
            }, "2000")
            setTimeout(() => {
              if (data["operation"] != "added")
              {
                document.getElementById("alert_holder").innerHTML =
                  `<div class="alert alert-danger" role="alert">Failed to sign up!</div>`;
              }
            }, "1000")
            if (data["operation"] == "added")
            {
              // location.replace("./index.php")
            }
          });
      }

    <?php endif; ?>

    async function postData(url = './main.php', data = {
      answer: 42
    }) {
      var formData = new FormData();
      for (const item in data) {
        formData.append(item, data[item]);
      }

      let response = await fetch(url, {
        method: 'POST',
        body: formData
      });

      return response.json(); // parses JSON response into native JavaScript objects
    }
  </script>
</body>

</html>



<?php
/**
CREATE TABLE registered (  contact_id INTEGER PRIMARY KEY, email TEXT NOT NULL UNIQUE, salt TEXT NOT NULL, verifier TEXT NOT NULL );
CREATE TABLE vaiting ( contact_id INTEGER PRIMARY KEY, email TEXT NOT NULL UNIQUE, salt TEXT NOT NULL, verifier TEXT NOT NULL );
CREATE TABLE authorized ( contact_id INTEGER UNIQUE, session_key TEXT NOT NULL,  time_stamp TEXT NOT NULL );
 */
?>