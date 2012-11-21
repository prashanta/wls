#! /bin/bash

myen0=`ifconfig en0 | grep "inet " | grep -v 127.0.0.1 | awk '{print $2}'`

if [ "$myen0" != "" ]
then
    echo "Ethernet    : $myen0"
else
    echo "Ethernet    : INACTIVE"
fi

myen1=`ifconfig en1 | grep "inet " | grep -v 127.0.0.1 | awk '{print $2}'`

ext1=`curl --silent http://checkip.dyndns.org | awk '{print $6}' | cut -f 1 -d '<'`

if [ "myen1" != "" ]
then
    echo "AirPort     : $myen1"
else
    echo "Airport     : INACTIVE"
fi
if [ "myen1" != "" ]
then
	echo "External IP : $ext1"
else
	echo "External IP : INACTIVE"
fi	

    