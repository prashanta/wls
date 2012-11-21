# genreport.py: Python script to generate Tomcat & MySQL Server Report
# Usage: python genreport.py [/absolute/path/filename.txt]

import sys
import os
import datetime
import time

filename = "server_report.txt"
if len(sys.argv) == 2:
	filename = sys.argv[1]

labs = [52,54,55,58,59,80]
ports = [55180,55280,55380,-1,55480,55580]
tports = [55381,54123,55123,55481,-1,55282]

labels = ["LAB 52 [Read/Write Server]","LAB 54 [Production Server]","LAB 55 [Development Server]","LAB 58 [Read Only Server]","LAB 59 [Read Only Server]", "LAB 80 [Read Only Server]"]
ret_mysql=[]
ret_tomcat=[]

def parseMysqlRet (ret):
	if ret :
		ret = ret[(ret.find("for ") + 4):]
		ret = ret[:ret.find(". It started")]
		ret = ret.replace(", ", " ")
		ret = ret.replace("and ", " ")	
		return ret
	else :
		return "Response not received!"
	
def parseTomcatRet (ret):
	if ret :
		ret = ret[(ret.find("UP for ") + 7):]
		ret = ret[:ret.find("</span>")]
		return ret	
	else :
		return "Response not received!"

i = 0 
while i < 6 : 
	if ports[i] != -1:
		# different db pwd for LAB-55
		if i == 2:
			cmd = "wget --quiet -O -  http://202.73.13.50:"+str(ports[i])+"/PhpMyAdmin/server_status.php --http-user=fao --http-passwd=faomimos | grep 'This MySQL server has been running for'"
		else:	
			cmd = "wget --quiet -O -  http://202.73.13.50:"+str(ports[i])+"/PhpMyAdmin/server_status.php --http-user=fao --http-passwd=fao | grep 'This MySQL server has been running for'" 	
		c = os.popen(cmd)
		ret = c.readline()
		c.close()
		ret_mysql.append(parseMysqlRet(ret))
		print "LAB " + str(labs[i]) + " MySQL: " + parseMysqlRet(ret)
	else:			
		ret_mysql.append("-")
	i=i+1	
	
i = 0
while i < 6 : 
	if tports[i] != -1:	
		cmd = "wget --quiet -O -  202.73.13.50:"+str(tports[i])+"/probe --http-user=tomcat --http-passwd=tomcat | grep 'UP for'"		
		c = os.popen(cmd)
		ret = c.readline()
		c.close()
		ret_tomcat.append(parseTomcatRet(ret))
		#print "LAB " + str(labs[i]) + " Tomcat: " + parseTomcatRet(ret)
	else:	
		ret_tomcat.append("-")
	i=i+1	
now = datetime.datetime.now()
a = time.strptime( now.strftime('%Y %W 1'), '%Y %W %w')
before = datetime.datetime(a.tm_year, a.tm_mon, a.tm_mday, a.tm_hour, a.tm_min, a.tm_sec, 0)
d1 = before.strftime('%B') + " " + str(before.day) + " " + str(before.year)
d2 = now.strftime('%B') + " " + str(now.day) + " " + str(now.year)
res = "Weekly Server Report [ "+d1+" to " +d2+ " ] \n=================================================== \n"
i = 0
while i < 6 : 
	res = res + "\n" + labels[i] + ":\n"
	if ret_tomcat[i] != "-":
		res = res + " Tomcat : " + ret_tomcat[i] + "\n"		
	if ret_mysql[i] != "-":
		res = res + " MySQL  : " + ret_mysql[i] + "\n"
	i=i+1
#print res
FILE = open(filename,"w")
FILE.writelines(res)
FILE.close()

print "**REPORT CREATED: " + filename