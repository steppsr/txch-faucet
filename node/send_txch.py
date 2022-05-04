import subprocess
import requests
import urllib3
import json
urllib3.disable_warnings()

# These are in mojos for the RPC API
PAYOUT_AMOUNT = 20000000000000
PAYOUT_FEE = 1

# post balance to xchdev faucet 
url = "https://xchdev.com/faucet/api/getrequests/"
headers = {'API-KEY':'4aif*F3tnr#JhCf#9FUJ*OZUAg^7de1GcOpC*G&PsHROQne5I4FScCnX1xw6%7@A'}
results = requests.post(url, data={}, headers=headers)
response = json.loads(results.text)

for sendto in response['data']:

    # call chia rpc api to send to each address
    headers = {'Content-Type': 'application/json'}
    url = "https://localhost:9256/send_transaction"
    data = '{"wallet_id":1,"address":"' + sendto + '","amount":' + str(PAYOUT_AMOUNT) + ',"fee":' + str(PAYOUT_FEE) + ',"memos":"XCHDEV_FAUCET"}'
    cert = ("c:\\users\\steve\\.chia\\mainnet\\config\\ssl\\wallet\\private_wallet.crt", "c:\\users\\steve\\.chia\\mainnet\\config\\ssl\\wallet\\private_wallet.key")
    response = json.loads(requests.post(url, data=data, headers=headers, cert=cert, verify=False).text)
