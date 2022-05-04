# Claim Rewards from our Pooling Wallet
import subprocess
import requests
import json
import sys

min = 15

# call chia rpc api to get the wallet balance
headers = {'Content-Type': 'application/json'}
url = "https://localhost:9256/get_wallet_balance"
data = '{"wallet_id":6}'
cert = ("c:\\users\\steve\\.chia\\mainnet\\config\\ssl\\wallet\\private_wallet.crt", "c:\\users\\steve\\.chia\\mainnet\\config\\ssl\\wallet\\private_wallet.key")
response = json.loads(requests.post(url, data=data, headers=headers, cert=cert, verify=False).text)
bal = response['wallet_balance']['confirmed_wallet_balance'] / 1000000000000

# if balance is over the minimum then lets claim rewards using the Chia CLI command
if bal > min:
    results = subprocess.run('chia plotnft claim -f 3812331296 -i 6 -m 0.000000000001', capture_output=True, text=True).stdout
    for line in iter(results.splitlines()):
        if line[0:7] == "Do chia":
            f = open("C:\\Users\\steve\\Documents\\ChiaProjects\\xchdev\\faucet\\claim_rewards.log", "w")
            f.write(line[3:115])
            f.close()
            break

exit