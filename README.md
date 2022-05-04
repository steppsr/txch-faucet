# txch-faucet

#### Node

The ```node``` directory contains python scripts that should be setup as Scheduled tasks on the testnet node computer.

* claim_rewards.py - Will poll the pooling wallet and claim rewards when above the specified amount.

* new_day.py - Will archive the data from todays sent transactions and clear out the today file.

* send_txch.py - Will poll the website for new requests, then send TXCH to each. Write the address into the today file so the website can check addresses for more than the daily limit before accepting the request.

* set_balance.py - poll the standard wallet for the testnode and set the wallet value on the website.

#### Web

The ```web``` directory contains the PHP code that runs the online website for requesting TXCH from the faucet.

* .htaccess - specifies our Rewrite rules to handle REST requests.

* api.php - verifies requests contain a valid API KEY then performs the API request.
    * getrequests - gets all current requests and sends back in a json response.
    * setbalance - sets the current wallet balance.
    * newday - archives the today file, and clears it for a new day.

* index.php - main webpage where user can make a request for TXCH, or get the testnode receive address to make a donation for the faucet.