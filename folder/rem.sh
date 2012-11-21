#!/bin/sh

tot=`cal | sed -e '$!N;s/\n/ /' -e '$!N;s/\n/ /' -e '$!N;s/\n/ /' -e '$!N;s/\n/ /' | tail -1 |awk ' {print $NF; exit'}`
today=`date "+%d"`
dif=$(($tot - $today)) 
echo "$dif DAY(S)"  