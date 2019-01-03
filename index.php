
<?php
/*
Copyright 2014 Daniel Esteban

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

// Open AntumID/ DigiID is required for login (do not modify)
// DAO could be replace by your CMS/FRAMEWORK database classes
require_once dirname(__FILE__) . "/config.php";
require_once dirname(__FILE__) . "/DigiID.php";
require_once dirname(__FILE__) . "/DAO.php";
require_once dirname(__FILE__) . "/AidTools.php";
$digiid = new DigiID();
// generate a nonce
$nonce = $digiid->generateNonce();
// build uri with nonce, nonce is optional, but we pre-calculate it to avoid extracting it later
$digiid_uri = $digiid->buildURI(SERVER_URL . 'callback.php', $nonce);

// Insert nonce + IP in the database to avoid an attacker go and try several nonces
// This will only allow one nonce per IP, but it could be easily modified to allow severals per IP
// (this is deleted after an user successfully log in the system, so only will collide if two or more users try to log in at the same time)
$dao = new DAO();
$result = $dao->insert($nonce, get_client_ip());
if(!$result)
{
	echo "<pre>";
	echo "Database failer\n";
	//var_dump($dao); //uncomment for debug only
	die();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Open AntumID</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.css">
    <link rel="stylesheet" href="assets/css/Login-Form-Clean.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="bounce animated login-clean">
        <form method="post">
            <h2 class="sr-only">Open AntumID</h2>
            <a href="#" class="forgot">
            <img class="justify-content-center align-items-center align-content-center align-self-center visible" src="assets/img/digibytelogin.png" width="50%" height="50%" data-bs-hover-animate="pulse" style="width:105px;">
            <br>Use the Digi-ID function of the DigiByte Wallet or Tap on the QR.<br><br>
            </a>
	    <a href="<?php echo $digiid_uri; ?>"><img id="antumIDqr" align="center" alt="Click on QRcode to activate compatible desktop wallet" border="0" width="250" height="250" data-aos="fade"/></a>
      	
            <center><img class="justify-content-center align-items-center align-content-center align-self-center visible" src="assets/img/digibytelogin3.png" data-bs-hover-animate="pulse" style="width:146px;"></center>
            <br><br><br>
            </a>
        </form>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bs-animation.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.js"></script>
<script src="digiQR.min.js"></script>
<script type="text/javascript">
    document.getElementById("antumIDqr").src=DigiQR.id("<?php echo $digiid_uri; ?>",300,6,0.5);//(digiid_uri,width,logo style(0-7),radius(0.0-1.0))
    setInterval(function() {
        var r = new XMLHttpRequest();
        r.open("POST", "<?php echo SERVER_URL; ?>ajax.php", true);
        r.onreadystatechange = function () {
            if (r.readyState != 4 || r.status != 200) return;
            if(r.responseText!='false') {
                window.location = '<?php echo SERVER_URL; ?>user.php';
            }
        };
        r.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        r.send("nonce=<?php echo $nonce; ?>");
    }, 3000);
</script>


</body>
</html>
