import subprocess
import requests
import urllib3
import json
urllib3.disable_warnings()

# post balance to xchdev faucet 
url = "https://xchdev.com/faucet/api/newday/"
headers = {'API-KEY':'4aif*F3tnr#JhCf#9FUJ*OZUAg^7de1GcOpC*G&PsHROQne5I4FScCnX1xw6%7@A'}
results = requests.post(url, data={}, headers=headers)
response = json.loads(results.text)
