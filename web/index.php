<?php
/*	XCH Dev Faucet																															.
 *																																			.
 *	Web front end for the dev faucet. Allow user to submit there Receive Address for TESTNET. We write there wallet address and IP Address	.
 *	to file called requests. Pull in the Faucet Wallet Balance from walletbalance.php file. This file will be updated from an API that the	.
 *	TESTNET farmer will hit when the wallet balance changes.																				.

* Pull out the API Key info into separate include files. Then these can be excluded from the git repo. Will need to create a sample include instead.
*
 
 * 																																		   */
session_start();
$_SESSION['xchdevresource'] = "faucet";

// Define number of requests per day
DEFINE("MAX_REQUESTS","2");

// Define strings we'll use in the code.
DEFINE("SUBMITTED_MSG","You have submitted a request today.");
DEFINE("NOT_SUBMITTED_MSG","Enter your Testnet Wallet Receive Address above and Submit Request to get some TXCH!");
DEFINE("NO_IP_ADDRESS","Something seemed to go wrong. Sorry about that, maybe try again later?");
DEFINE("NO_SENDTO_ADDRESS","Missing Wallet Address. You must put in a valid Wallet Receive Address for TESTNET.");
DEFINE("INVALID_WALLET_ADDRESS","This is not a valid TESTNET address.");
DEFINE("REQUEST_ACCEPTED","Your request was sent, you should have some TXCH soon.");
DEFINE("APOLOGY","Sorry.");
DEFINE("DEV_FAUCET_WALLET","txch15ef455n0529w57gcc46gtx3xdawyjf8hazrvfne2vfahxr2un5rq3l478h");
DEFINE("TESTNET_UOM","txch");

if(isset($_SESSION['msg']) && strlen($_SESSION['msg']) > 0)
{
	$user_message =  htmlspecialchars($_SESSION['msg']);
} else {
	$user_message = NOT_SUBMITTED_MSG;
}

// default values
$sendto = "";
$ip = "";

// add code in for the form submission
if(isset($_POST['submit']) && $_POST['submit'] === 'Submit' && $_POST['sendto-address'] != "")
{	
	// lets get the user IP address
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	if($ip == "")
	{
		$user_message = NO_IP_ADDRESS;

	} else {
		$_SESSION['ip'] = $ip;
		
		// lets get the wallet address
		if(isset($_POST['sendto-address']))
		{
			$sendto = $_POST['sendto-address'];

			// validate the wallet address is a testnet address
			if(strtolower(substr($sendto,0,4)) != TESTNET_UOM || strlen($sendto) != 63 || strtolower($sendto) == DEV_FAUCET_WALLET)
			{
				$user_message = INVALID_WALLET_ADDRESS;
			} else {

				$_SESSION['sendto'] = $sendto;
				// valid testnet net address. check if the user has already made a request today
				if( exec("grep " . escapeshellarg($ip) . " ./requests.php | wc -l") + exec("grep " . escapeshellarg($ip) . " ./today.php | wc -l") >= MAX_REQUESTS )
			    {
			        $user_message = APOLOGY . " " . SUBMITTED_MSG;
			    } else {
					if( exec("grep " . escapeshellarg($sendto) . " ./requests.php | wc -l") + exec("grep " . escapeshellarg($sendto) . " ./today.php | wc -l") >= MAX_REQUESTS )
					{
						$user_message = APOLOGY . " " . SUBMITTED_MSG;
					} else {
							$fp = fopen('/var/www/xchdev.com/public_html/faucet/requests.php','a'); // open file in append mode
							fwrite($fp, "$ip $sendto " . date("Y-m-d H:i:s") . "\n");
							fclose($fp);
							$user_message = REQUEST_ACCEPTED;
					}
			    }
			}
		} else {
			$user_message = NO_SENDTO_ADDRESS;
		}
	}
	$_SESSION['msg'] = $user_message;

} else {

//lets grab the faucet wallet balance now so we can use down below.
$txch = exec("head -n 1 balance.php");
$mojo = sprintf("%.0f",$txch * 1000000000000 );

// if form not submitted then output the regular web page.
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>XCH Dev Faucet</title>
		<meta http-equiv='refresh' content='60'>
			<link rel="icon" type="image/png" href="favicon.png">
		<style>
			body,div { font-family: "Arial Narrow"; font-size; 2.0rem; font-weight:normal; color: #99cc00; background: #363636; }
			.content { width: 1100px; height: 700px; margin: auto; }
			.header { width: 1100px; font-family: "Luckiest Guy"; font-size: 2.3rem; font-weight: normal; color: #99cc00; 
				text-shadow: -5px 0 #000000, 0 9px #000000, 5px 0 #000000, 0 -5px #000000; line-height: 75px; }
			.get { padding: 20px; }
			.give { padding: 20px; }
			div .get { width: 600px; height: 200px; outline: 6px solid; padding: 20px; float: left; background: #000000; }
			div .faucet { width: 400px; height: 200px; outline: 6px solid; padding: 20px; float: left; background: #000000; }
			div .give { width: 1040px; height: 300px; outline: 6px solid; padding: 20px; float: left; background: #000000; }
			div .block h2 { margin-top: 0px; background: #000000; }
			a,.social-icon { color: #FFFF99; text-decoration: none; }
			.faucet-balance { text-align: right; font-size: 2.6rem; background: #000000; }
			.thankyou { font-size: 1.4rem; }
			.disclaimer { width: 1100px; height: 50px; margin:auto; font-weight: normal; font-family: "Arial Narrow"; text-align: center; position:absolute; bottom: 20px; }
			.maintenance { color: #FF0000; width: 1100px; height: 50px; margin:auto; font-weight: normal; font-family: "Arial Narrow"; text-align: center; position:absolute; bottom: 60px; }
			.footer { margin:auto; text-align: center; font-weight: normal; font-family: "Arial Narrow"; font-size: 1.0rem; 
		          width: 1056px; height: 25px; border: 1px solid #99cc00; padding-top: 4px;
		          position: absolute; bottom: 4px; background: #000000; padding-left: 10px; padding-right: 10px; 
		        }
			.user { float:left; }
			.social { font-size:1.0rem; font-weigth: normal; }
			.affiliation { float: right; }
			code,pre { background: #000000; padding:7px; margin-left:25px; margin-right:25px; }
			.highlight { color: #ffffffff; font-weight: bold; }
			input { padding: 5px; margin: 5px; }
			.strike { text-decoration: line-through; }
			.limited { font-size: 0.8rem;  }
			.title { background: #000000; padding:10px; font-size: 1.6rem; margin: 0px 0px 0px 0px; }
		</style>
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v5.13.0/js/all.js" crossorigin="anonymous"></script>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed&family=Dosis:wght@200&family=Luckiest+Guy&family=Passion+One:wght@400;700&display=swap" rel="stylesheet">
	</head>
	<body>

		<div class='content'>
			
			<div class='header'><h1>XCH Dev Faucet</h1></div>

			<div class='get block'>
				<p class='title'>Get TXCH</p>
				<p>Developers, grab some TXCH to use while you develop & test.
				<br>You can get TXCH <span class='strike'>once a day.</span> <?=MAX_REQUESTS?> times a day. <sup class='limited'>Limited time</sup></p>
				<form action="" method="POST">
				<input type='text' name='sendto-address' value='' placeholder='Your TESTNET Wallet Receive Address' size='64'>&nbsp;&nbsp;</input>
				<input type='submit' name='submit' value='Submit'></input><br>
				<?= $_SESSION['msg']; ?>
			</div> <!-- get -->

			<div class='faucet block'>
				<p class='title'>Faucet Wallet Balance<p>
				<div class='faucet-balance highlight'>
					<span>
						<?=$txch;?> txch<br>
						<?=$mojo;?> mojo
					</span>
				</div>

			</div> <!-- faucet -->
			
			<div class='give block'>
				<p class='title'>Give TXCH</p>
				<p>If you have TXCH you are willing to give to a developer to use while building new cool stuff. Here is the address of the faucet wallet for donations:</p>
				<pre class='highlight'>txch15ef455n0529w57gcc46gtx3xdawyjf8hazrvfne2vfahxr2un5rq3l478h</pre>
				<i>This faucet is for TESTNET only. Only send TXCH from a TESTNET wallet.</i>
				<p>Can you use the GUI or here is <u>How to send some TXCH from the CLI</u><br>
				<pre class='highlight'>chia wallet send -f your_fingerprint -i wallet_id -a your_amount -e "your_memo" -m 0.000000000001 -t send_to_address -o</pre>
				You should be able to use 1 mojo for a fee on TESTNET. &nbsp;&nbsp; -o says override unusual amount and send anyway.</p>

				<span class='thankyou'>Thank You</span> • Thank you for your generosity and support of the Chia community.
			</div> <!-- give -->

			<!-- <div class='maintenance'>Site is undergoing maintenance, payouts may be slower than normal.</div> -->
			
			<div class='disclaimer'>Disclaimer: For educational purposes only.</div>

			<div class='footer'>
		        <span class='user'>Steve Stepp • @steppsr • steve@xchdev.com </span>
		        <span class='social'>
			        <a class="social-icon" href="https://twitter.com/steppsr"><i class="fab fa-twitter"></i>&nbsp;&nbsp; https://twitter.com/steppsr</a>&nbsp;&nbsp;&nbsp;&nbsp;
			        <a class="social-icon" href="https://github.com/steppsr"><i class="fab fa-github"></i>&nbsp;&nbsp; https://github.com/steppsr</a>
		        </span>
		        <span class='affiliation'>No affiliation with Chia Network.</span>
			</div> <!-- footer -->

		</div> <!-- content -->

	</body>
</html>
<?php
}

$SESSION['msg'] = (strlen($user_message) > 0) ? $user_message : "";
header("Location: index.php");

?>