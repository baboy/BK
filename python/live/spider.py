 # -*- coding:utf-8 -*-
import urllib2, sys
import json
import dateutil.parser
import time
from HTMLParser import HTMLParser
import re
from lxml import etree
import lxml.html as lhtml
import lxml.html.soupparser as soupparser
import StringIO
import email.utils as eut
import datetime
import DB
import md5
import json
import codecs
class Page():
	def __init__(self):
		self.timeoutInterval = 20

	def download(self,url):
		print "Page.download:",url
		urllib2.socket.setdefaulttimeout(self.timeoutInterval)
		response = urllib2.urlopen(url)
		#print response.headers
		data = response.read() 
		response.close()
		return data

class CNTVApi( HTMLParser):
	def __init__(self, url):
		self.url = url
		HTMLParser.__init__(self)
		self.links = []
		self.db = DB.DB()
		self.db.appid = "BK"
		self.db.module = "news"
	def getLiveUrl(self, api):
		page = Page()
		text = page.download(api)
		data = json.loads(text)
		return data

	def parse(self):
		page = Page()
		text = page.download(self.url)
		data = json.loads(text)
		channels = data.get("data").get("items")
		for item in channels:
			keyMap = {"icon":"channelImg","name":"title","reference_url":"liveUrl"}
			channel = {"rate":"300","source":"CNTV"};
			for k in keyMap:
				k2 = keyMap[k]
				channel[k] = item.get(k2)
			title = item.get("title")
			channel_id = item.get("channelId")
			thumbnail = "http://t.live.cntv.cn/imagehd/"+channel_id+"_01.png"
			print thumbnail
			channel_id = self.db.getChannelId({"name":title})
			print channel_id
			if channel_id:
				self.db.update({"thumbnail":thumbnail},{"channel_id":channel_id})
			#api = self.getLiveUrl(item.get("liveUrl"))
			#channel["live_url"] = api.get("iphone")
			#rowid = self.db.addItem(channel);
			#print rowid

#parser = CNTVApi("http://serv.cbox.cntv.cn/json/zhibo/yangshipindao/ysmc/index.json")
parser = CNTVApi("http://serv.cbox.cntv.cn/json/zhibo/weishipindao/wsmc/index.json")
#parser = CNTVApi("http://serv.cbox.cntv.cn/json/zhibo/difangpindao/dfmc/index.json")
parser.parse()
