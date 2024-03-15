import serial
import requests
import json
import time

endpoint = 'http://robo.local:10003/wp-json/braco-robo/v1/robo/'
ser = serial.Serial('/dev/ttyUSB0', baudrate=9600)
older_response = ''

while True:
    response = requests.get(endpoint)

    data = json.loads(response.text)
    data = json.loads(data)
    data.pop('id', None)
 
    print(data['fisico'])

    if data['fisico'] != "0":
        print("ENTROU")
        data = json.dumps(data).strip()
        if older_response != response.text:
            ser.write(data.replace('"','').encode())
            older_response = response.text
            print(older_response)
        time.sleep(1)

ser.close()
