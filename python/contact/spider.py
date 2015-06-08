#!/usr/bin/env python
#coding=utf-8
import urllib2, sys
import json
import dateutil.parser
import time
import DB
fieldMap = {"name":"uName","mobile":"phone","business":"business", "keyword":"keyword", "area":"area", "address":"address","company":"company","lat":"lat","lng":"lng"}
class ContactParser( ):
	def __init__(self):
		self.links = []
		self.db = DB.DB()


	
	def parse_json(self,page, jsonData):
		if "data" in jsonData:
			data = jsonData["data"]
		else:
			print "No data..."
			return
		num = 0	
		for d in data:
			item = {}
			for k in fieldMap:
				k2 = fieldMap[k]
				v = d[k2] if k2 in d else None
				if v:
					item[k] = v
			sid = self.db.addItem(item);
			if sid > 0:
				num = num+1
				print "add item sid:",sid,item.get("name"),item.get("mobile")
			else:
				print "add item error:",item.get("name"),item.get("mobile")
		if num > 0:
			self.download_page(page +1)
		else:
			print "quit loop"

	
	def download_page(self,page):
		print "page:",page
		url = "http://123.57.80.206:8080/user/search?pagesize=20&business=&uid=725&industy=&area=&page="+str(page)
		print "url:",url
		stime = time.time()
		print "<download start> at time: %s"%time.strftime('%Y-%m-%d %H:%M:%S',time.localtime(stime))
		#get rss data
		urllib2.socket.setdefaulttimeout(10)
		response = urllib2.urlopen(url)
		print response.headers
		data = response.read() 
		response.close()
		etime = time.time()
		print "<download end> at time: %s cost:%f"%(time.strftime('%Y-%m-%d %H:%M:%S',time.localtime(etime)),etime-stime)
		# parse
		stime = etime
		self.parse_json(page,json.loads(data) );
		etime = time.time()
		print "<parse end> at time: %s cost:%f"%(time.strftime('%Y-%m-%d %H:%M:%S',time.localtime(etime)),etime-stime)
		self.db.close()
	def start(self,page):
		self.download_page(page)

#parser = WeiboParser("http://m.tvie.com.cn/mcms/api2/mod/sns/feeds.php?uid=2214257545")
#parser = WeiboParser("http://m.tvie.com.cn/mcms/api2/mod/sns/feeds.php?uid=1640601392")
page = int(sys.argv[1])
parser = ContactParser()
parser.start(page)
#print sohu.jsonMap
