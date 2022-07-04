var crypt = require("./crypto-js.js")
var bigInt = require("./bigInteger.js")
//

function h_hash2(a, b)
{
      var sha3 = crypt.algo.SHA3.create();

      sha3.update (a);
      sha3.update (b);

      return sha3.finalize(); // check not 0
}

function h_hash3(a, b, c)
{
      var sha31 = crypt.SHA3(a);
      var sha32 = crypt.SHA3(b);
      var sha33 = crypt.SHA3(c);

      return sha31|sha32|sha33; // check not 0
}

var for_hash = (big) => crypt.enc.Hex.parse(big.toString());

var N = bigInt("FFFFFFFFFFFFFFFFC90FDAA22168C234C4C6628B80DC1CD129024E088A67CC74020BBEA63B139B22514A08798E3404DDEF9519B3CD3A431B302B0A6DF25F14374FE1356D6D51C245E485B576625E7EC6F44C42E9A637ED6B0BFF5CB6F406B7EDEE386BFB5A899FA5AE9F24117C4B1FE649286651ECE45B3DC2007CB8A163BF0598DA48361C55D39A69163FA8FD24CF5F83655D23DCA3AD961C62F356208552BB9ED529077096966D670C354E4ABC9804F1746C08CA18217C32905E462E36CE3BE39E772C180E86039B2783A2EC07A28FB5C55DF06F4C52C9DE2BCBF6955817183995497CEA956AE515D2261898FA051015728E5A8AACAA68FFFFFFFFFFFFFFFF", 16);

var g = bigInt(2)


// create login password pair

var login = "login";
var password = "password";

var salt = crypt.lib.WordArray.random(512 / 8);


/***
var sha512 = crypt.algo.SHA512.create();

sha512.update (salt);
sha512.update (password);

var hash = sha512.finalize(); // check not 0
*/
var hash = h_hash2(salt, password); // a.k.a. x

var verifier = g.modPow(bigInt(hash.toString(),16), N); // check not N and not 0

// salt and verifier to server


var k = h_hash2(for_hash(N), for_hash(g));

// client login

var a = crypt.lib.WordArray.random(512 / 8);
var A = g.modPow(bigInt(a.toString(),16), N); // check not 0
// login and A -> server

// server login
var b = crypt.lib.WordArray.random(512 / 8);
b = bigInt(b.toString(), 16);
var B = (bigInt(k.toString(), 16).multiply(verifier)).add(g.modPow(b,N))

// B and salt -> client

var x = h_hash2(salt, password);
var u = h_hash2(for_hash(A),for_hash(B));
u = bigInt(u.toString(), 16);
var sc = bigInt(B).minus(bigInt(k.toString(),16).multiply(g.modPow(bigInt(x.toString(),16),N))).modPow(bigInt(a.toString(),16).add(u.multiply(bigInt(x.toString(),16))), N);

var ss = (A.multiply((verifier.modPow(u,N)))).modPow(b,N);


///////////////////////////////////////

var m1 = crypt.SHA3(for_hash(A.xor(B).xor(sc)))
var m2 = crypt.SHA3(for_hash(A.xor(bigInt(m1.toString(),16)).xor(ss)))








/////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////








// in python
// >>> H = hashlib.sha3_512()
// >>> H.update(bb.to_bytes(256,'big'))
// >>> H.digest().hex()


var crypt = require("./sha3.js")
var bigInt = require("./bigInteger.js")
//

function h_hash2(a, b)
{
      var sha3 = crypt.sha3_512.create();
      sha3.update (a);
      sha3.update (b);

      return sha3.hex(); // check not 0
}

function h_hash3(a, b, c)
{
      var sha31 = crypt.sha3_512(a);
      var sha32 = crypt.sha3_512(b);
      var sha33 = crypt.sha3_512(c);

      return sha31|sha32|sha33; // check not 0
}

var for_hash = function (big_int_num)
{
    var hex = big_int_num.toString(16);
    if (hex.length % 2 !== 0)
    {
        hex = '0' + hex;
    }
    var bytes = [];
    for (var n = 0; n < hex.length; n += 2)
    {
        var code = parseInt(hex.substr(n, 2), 16)
        bytes.push(code);
    }
    return bytes;
}

class WordArray{
    constructor(words, sigBytes)
    {
        this.words = words;
        this.sigBytes = sigBytes;
    }

    toString()
    {
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

var cryptoSecureRandomInt = function () {
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

var secure_random = function (nBytes)
{
    var words = [];

    for (var i = 0; i < nBytes; i += 4) {
        words.push(cryptoSecureRandomInt());
    }

    return new WordArray(words, nBytes);
}


var N = bigInt("FFFFFFFFFFFFFFFFC90FDAA22168C234C4C6628B80DC1CD129024E088A67CC74020BBEA63B139B22514A08798E3404DDEF9519B3CD3A431B302B0A6DF25F14374FE1356D6D51C245E485B576625E7EC6F44C42E9A637ED6B0BFF5CB6F406B7EDEE386BFB5A899FA5AE9F24117C4B1FE649286651ECE45B3DC2007CB8A163BF0598DA48361C55D39A69163FA8FD24CF5F83655D23DCA3AD961C62F356208552BB9ED529077096966D670C354E4ABC9804F1746C08CA18217C32905E462E36CE3BE39E772C180E86039B2783A2EC07A28FB5C55DF06F4C52C9DE2BCBF6955817183995497CEA956AE515D2261898FA051015728E5A8AACAA68FFFFFFFFFFFFFFFF", 16);

var g = bigInt(2)


// create login password pair

var login = "login";
var password = "password";

var salt = secure_random(512 / 8);


/***
var sha512 = crypt.algo.SHA512.create();

sha512.update (salt);
sha512.update (password);

var hash = sha512.finalize(); // check not 0
*/
var hash = h_hash2(salt.words, password); // a.k.a. x

var verifier = g.modPow(bigInt(hash.toString(),16), N); // check not N and not 0

// salt and verifier to server


var k = h_hash2(for_hash(N), for_hash(g));

// client login

var a = secure_random(512 / 8);
a = bigInt(a.toString(),16);
var A = g.modPow(a, N); // check not 0
// login and A -> server

// server login
var b = secure_random(512 / 8);
b = bigInt(b.toString(),16);
var B = (bigInt(k.toString(), 16).multiply(verifier)).add(g.modPow(b,N));

// B and salt -> client

var x = h_hash2(salt.words, password);
x = bigInt(x.toString(),16);
k = bigInt(k.toString(),16)
var u = h_hash2(for_hash(A),for_hash(B));
u = bigInt(u.toString(), 16);
var sc = B.minus(k.multiply(g.modPow(x,N))).modPow(a.add(u.multiply(x)), N);

var ss = (A.multiply((verifier.modPow(u,N)))).modPow(b,N);


///////////////////////////////////////

var m1 = crypt.SHA3(for_hash(A.xor(B).xor(sc)))
var m2 = crypt.SHA3(for_hash(A.xor(bigInt(m1.toString(),16)).xor(ss)))