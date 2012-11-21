#!/bin/sh

# Network Interface
echo "\033[1;33mNetwork:\033[37m \n"

en0=`ifconfig en0 | grep "inet " | grep -v 127.0.0.1 | awk '{print $2}'`
en1=`ifconfig en1 | grep "inet " | grep -v 127.0.0.1 | awk '{print $2}'`

if [ "$en0" != "" ]
then
    echo "Ethernet    : $en0\n"
else
    echo "Ethernet    : INACTIVE\n"
fi

ext1=`curl --silent http://checkip.dyndns.org | awk '{print $6}' | cut -f 1 -d '<'`

str=`airport -I`

if [ "$str" != "AirPort: Off" ]
then
    sid=`airport -I | tail -3 | head -1 | sed -e 's/^[ t]*//'`
	rssi=`airport -I | head -1 | sed -e 's/^[ t]*//'`
	noise=`airport -I | tail -13 | head -1 | sed -e 's/^[ t]*//'`
	ch=`airport -I | tail -1 | sed -e 's/^[ t]*//'`
	lr=`airport -I | tail -9 | head -1 | sed -e 's/^[ t]*//'`
	mr=`airport -I | tail -8 | head -1 | sed -e 's/^[ t]*//'`
	state=`airport -I | tail -11 | head -1 | sed -e 's/^[ t]*//'`
	en1=`ifconfig en1 | grep "inet " | grep -v 127.0.0.1 | awk '{print $2}'`
	 	
	echo "AIRPORT     :${state#*:}"
	echo "WiFi        :${sid#*:}"
	echo "RSSI        :${rssi#*:} dbm"
	echo "NOISE       :${noise#*:} dbm"
	echo "Tx RATE     :${lr#*:}"
	echo "MAX RATE    :${mr#*:}"
	echo "CHANNEL     :${ch#*:}\n"
	echo "IP          :$en1"
	echo "External IP :$ext1\n"	
	
else
    echo "AIRPORT is turned OFF!!"
fi

