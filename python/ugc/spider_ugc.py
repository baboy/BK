#!/usr/bin/env python
#coding=utf-8
import urllib2, sys
import json
import dateutil.parser
import time
import sohu
import DB

class SohuVideoParser( ):
	def __init__(self):
		self.links = []
		self.db = DB.DB()
		self.db.appid = "XCHANNEL"
		self.db.module = "movie"


	
	def parse_json(self,page, jsonData):
		if "data" in jsonData:
			data = jsonData["data"]
		else:
			print "No data..."
			return
		if "videos" in data:
			videos = data["videos"]
		else:
			print "No Videos..."
			return
		num = 0	
		for video in videos:
			item = {}
			for k in sohu.jsonMap:
				k2 = sohu.jsonMap[k]
				v = video[k2] if k2 in video else None
				if v:
					item[k] = v
			item["node"] = "CONTENT"
			sid = self.db.addItem(item);
			if sid > 0:
				num = num+1
				print "add video sid:",sid
				self.download_video(sid,item["vid"])
			else:
				print "add video error:",item["vid"],item["reference_id"],item["title"]
		if num > 0:
			self.download_page(page -1)
		else:
			print "quit loop"
			


	def download_video(self, sid,vid):
		url = "http://api.tv.sohu.com/v4/video/info/"+str(vid)+".json?site=1&api_key=695fe827ffeb7d74260a813025970bd5&plat=3&partner=1&sver=3.5&poid=1&"
		user_agent = "Mozilla/5.0 (iPhone; CPU iPhone OS 6_0_1 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A523 Safari/8536.25"
		urllib2.socket.setdefaulttimeout(10)
		request = urllib2.Request(url)
		request.add_header("User-Agent", user_agent)
		response = urllib2.urlopen(request)
		data = response.read()
		response.close()
		jsonData = json.loads(data)
		if "data" in jsonData:
			video = jsonData["data"]
		else:
			print "No data..."
			return
		item = {}
		for k in sohu.videoMap:
			k2 = sohu.videoMap[k]
			v = video[k2] if k2 in video else None
			if v:
				item[k] = v
		item["sid"] = sid
		print item.get("original")

		f = {"sid":sid,"url":item.get("pic_hor"),"thumbnail":item.get("pic_hor"),"type":"images","content":item.get("content")}
		self.db.addFile(f)
		f = {"sid":sid,"url":item.get("sd",item.get("mp4")),"thumbnail":item.get("pic_hor"),"type":"videos","content":item.get("content"),"duration":item.get("duration")}
		self.db.addFile(f)
	
	def download_page(self,page):
		if page < 0:
			return
		print "page:",page
		url = "http://api.tv.sohu.com/v4/search/channel/sub.json?subId=22&poid=1&plat=3&api_key=695fe827ffeb7d74260a813025970bd5&sver=3.5&partner=1&cate_code=100&column_id=58&column_type=4&page="+str(page)+"&page_size=30&act=1"
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
parser = SohuVideoParser()
parser.start(page)
#print sohu.jsonMap
