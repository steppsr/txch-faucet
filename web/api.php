<?php
/*                                                                                                                                  	.
 * XCH Dev Faucet API																													.
 *																																		.
 * Be sure to hash the API key, we don't want to store the API key on the server.														.
 * Need to figure out how to convert this to a POST API and not GET. We don't want the API Key or it's Hash sent over GET.				.
 *																																	    .
 * Sample endpoint calls																												.
 * ---------------------																												.
 * curl -s -X POST -H "API-KEY: api_key_for_access" "https://xchdev.com/faucet/api/get_transactions"									.
 *																																		.
 * curl -s -X POST -H "API-KEY: api_key_for_access" "http://xchdev.com/faucet/api/set_balance/450.000"									.
 *																																	   */

DEFINE("PREFIX","19752156061");
DEFINE("HASHED_KEY","c99356adac1a2239e8a4a427ae0da1fbf5decc3f43d7b9e27f447944fdaa23dc");
$error_msg = "";

$req = "";
$val = 0;

$is_valid = false;
foreach(getallheaders() as $headername => $headervalue)
{
	// find name = "API-KEY" and make sure it is valid.
	if($headername == "API-KEY")
	{
		if(HASHED_KEY == hash('sha256',PREFIX . "." . $headervalue))
		{
			$is_valid = true;
		} else {
			$error_msg = "Invalid API Key.";
		}
	}
}

if($is_valid)
{
	$req=(isset($_GET['req'])) ? $_GET['req'] : "";
	$val=(isset($_GET['val'])) ? $_GET['val'] : "";

	switch(strtolower($req))
	{
		// GETS
		case "getrequests": send_response(get_requests()); break;

		// SETS
		case "setbalance": $result = ($val != "") ? set_balance($val) : "Error, no changes made."; break;

		case "newday": new_day(); break;
	}
}
else
{
	response(400, "Invalid Request", $error_msg);
	echo "\nName: $name\n";
}

/*                                                                                                                                  	.
 * Functions																															.
 *																																	   */

function get_requests()
{
	$requests = array();
	$fp_reqs = fopen("/var/www/xchdev.com/public_html/faucet/requests.php","r") or die("Unable to get request!");
	$fp_today = fopen("/var/www/xchdev.com/public_html/faucet/today.php","a") or die("Unable to get today!");
	while(!feof($fp_reqs))
	{
		$line = fgets($fp_reqs);
		fwrite($fp_today, $line);
		$fields = explode(" ", $line);
		if($fields[1] != "" && $fields[1] != "\n")
		{
			$requests[] = trim(str_replace("\n","",$fields[1])); // add the sendto address to the array
		}
	}
	fclose($fp_reqs);
	fclose($fp_today);

	// on success lets empty the requests since they've been sent back and put into the today file
	file_put_contents("/var/www/xchdev.com/public_html/faucet/requests.php", "");	// erase the contents of the requests file
	return $requests;
}

function set_balance($balance)
{
	try
	{
		$fp = fopen('/var/www/xchdev.com/public_html/faucet/balance.php','w'); // open file in append mode
		fwrite($fp, "$balance\n");
		fclose($fp);
		return response(200, "Success", NULL);
	}
	catch(Exception $e)
	{
		return response(200, "Exception", $e);
	}
}

function get_balance()
{
	return trim(exec("cat balance.php | tail -n 1"));
}

function new_day()
{
	try
	{
		// loop through the today file and write lines to archive
		$fp_today = fopen("/var/www/xchdev.com/public_html/faucet/today.php","r") or die("Unable to get today!");
		$fp_archive = fopen("/var/www/xchdev.com/public_html/faucet/archive.php","a") or die("Unable to get archive!");
		while(!feof($fp_today))
		{
			$line = fgets($fp_today);
			fwrite($fp_archive,$line);
		}
		fclose($fp_today);
		fclose($fp_archive);

		// empty out today file.
		file_put_contents("/var/www/xchdev.com/public_html/faucet/today.php", "");
		return response(200, "Success", NULL);
		}
	catch(Exception $e)
	{
		return response(200, "Exception", $e);
	}
}

function send_response($result)
{
	if(empty($result))
	{
		response(200, "Server Not Found", NULL);
	}
	else
	{
		response(200, "Server Found", $result);
	}
}

function redirect()
{
	header('Location: http://xchdev.com/faucet/');
}

function response($status,$status_message,$data)
{
	header("HTTP/1.1 ".$status);
	$response['status']=$status;
	$response['status_message']=$status_message;
	$response['data']=$data;
	$json_response = json_encode($response);
	echo $json_response;
}
?>
