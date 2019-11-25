from network import Sigfox
from pytrack import Pytrack
#import urequests as requests
from L76GNSS import L76GNSS
import socket
import time
import pycom
import struct

py = Pytrack()
gps = L76GNSS(py, timeout=60)
print("connecting to Sigfox")
# init Sigfox for RCZ1 (Europe)
sigfox = Sigfox(mode=Sigfox.SIGFOX, rcz=Sigfox.RCZ1)
# create a Sigfox socket
s = socket.socket(socket.AF_SIGFOX, socket.SOCK_RAW)
# make the socket blocking
s.setblocking(True)
s.setsockopt(socket.SOL_SIGFOX, socket.SO_RX, False)

# Get coordinates from pytrack
coord = gps.coordinates()
lat, lng = coord

print(str(lat), ":", str(lng))
s.send(struct.pack('f',float(lat)) + struct.pack('f',float(lng)))
