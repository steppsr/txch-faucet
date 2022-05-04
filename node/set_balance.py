import subprocess
import requests
import urllib3
import json
urllib3.disable_warnings()

# call chia rpc api to get the wallet balance of STANDARD_WALLET
headers = {'Content-Type': 'application/json'}
url = "https://localhost:9256/get_wallet_balance"
data = '{"wallet_id":1}'
cert = ("c:\\users\\steve\\.chia\\mainnet\\config\\ssl\\wallet\\private_wallet.crt", "c:\\users\\steve\\.chia\\mainnet\\config\\ssl\\wallet\\private_wallet.key")
response = json.loads(requests.post(url, data=data, headers=headers, cert=cert, verify=False).text)
bal = response['wallet_balance']['confirmed_wallet_balance'] / 1000000000000


# post balance to xchdev faucet 
url = "https://xchdev.com/faucet/api/setbalance/" + str(bal)
headers = {'API-KEY':'4aif*F3tnr#JhCf#9FUJ*OZUAg^7de1GcOpC*G&PsHROQne5I4FScCnX1xw6%7@A'}
response = json.loads(requests.post(url, data={}, headers=headers).text)

exit