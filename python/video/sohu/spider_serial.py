 # -*- coding:utf-8 -*-
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
		self.db.appid = "BK"
		self.db.module = "serial"


	def getDetail(self,aid):
		url = "http://api.tv.sohu.com/v4/album/info/"+str(aid)+".json?api_key=695fe827ffeb7d74260a813025970bd5&plat=3&partner=1&sver=3.5&poid=1"
		user_agent = "Mozilla/5.0 (iPhone; CPU iPhone OS 6_0_1 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A523 Safari/8536.25"
		urllib2.socket.setdefaulttimeout(20)
		request = urllib2.Request(url)
		request.add_header("User-Agent", user_agent)
		response = urllib2.urlopen(request)
		data = response.read()
		response.close()
		jsonData = json.loads(data)
		video = jsonData.get("data")
		item = {}
		for k in sohu.jsonMap:
			k2 = sohu.jsonMap[k]
			v = video.get(k2)
			if v:
				item[k] = v
		return item

	
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
			aid = item["reference_id"]
			videoInfo = self.getDetail(aid)
			if not videoInfo:
				continue
			# save media table
			videoInfo["node"] = "SERIAL"
			gid = self.db.addItem(videoInfo)
			if gid < 1:
				continue
			#attr_id = self.db.addAttr(gid,"video_total_count", videoInfo["video_total_count"])
			#attr_id2 = self.db.addAttr(gid,"video_update_count", videoInfo["video_update_count"])
			#print "gid", gid,attr_id,attr_id2
			serials = self.download_video(aid)
			for i in range(0,len(serials)):
				v = item
				for k in serials[i]:
					v[k] = serials[i][k]
				v["node"] = "CONTENT"
				v["reference_id"] = str(v["reference_id"])+"_"+str(i)
				sid = self.db.addItem(v)
				if sid < 1:
					break;
				v["sid"] = sid
				self.db.addVideo(v)
				print "sid:",sid, "gid:",gid
				serial_id = self.db.addSerialVideo(sid,gid,i+1)
				print "serial_id:",serial_id
			num = num+1

		if num > 0:
			self.download_page(page -1)
		else:
			print "quit loop"
			


	def download_video(self, aid):
		url = "http://api.tv.sohu.com/v4/album/videos/"+str(aid)+".json?page_size=50&api_key=695fe827ffeb7d74260a813025970bd5&plat=3&partner=1&sver=3.5&poid=1&page=1" 
		user_agent = "Mozilla/5.0 (iPhone; CPU iPhone OS 6_0_1 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A523 Safari/8536.25"
		urllib2.socket.setdefaulttimeout(20)
		request = urllib2.Request(url)
		request.add_header("User-Agent", user_agent)
		response = urllib2.urlopen(request)
		data = response.read()
		response.close()
		jsonData = json.loads(data)
		videos = jsonData.get("data")
		if videos:
			videos = videos.get("videos")
		if not videos:
			print "download video error:",aid
			return None
		items = []
		for i in range(0,len(videos)):
			video = videos[i]
			item = {}
			for k in sohu.videoMap:
				k2 = sohu.videoMap[k]
				v = video[k2] if k2 in video else None
				if v:
					item[k] = v
			items.append(item)
		return items
	
	def download_page(self,page):
		print "#######################page:",page
		url = "http://api.tv.sohu.com/v4/search/channel.json?api_key=695fe827ffeb7d74260a813025970bd5&plat=3&sver=3.5&partner=1&cid=2&page="+str(page)+"&page_size=30" 
		print "url:",url
		stime = time.time()
		print "<download start> at time: %s"%time.strftime('%Y-%m-%d %H:%M:%S',time.localtime(stime))
		#get rss data
		urllib2.socket.setdefaulttimeout(20)
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

page = int(sys.argv[1])
parser = SohuVideoParser()
parser.start(page)
