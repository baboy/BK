 # -*- coding:utf-8 -*-
import urllib2, sys
import json
from collections import OrderedDict 
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
		self.returnType = "json"

	def download(self,url):
		print "Page.download:",url
		urllib2.socket.setdefaulttimeout(self.timeoutInterval)
		response = urllib2.urlopen(url)
		print response.headers
		data = response.read() 
		response.close()
		if self.returnType == "json" :
			return json.loads(data,object_pairs_hook=OrderedDict)
		return data

class City( HTMLParser):
	def __init__(self):
		HTMLParser.__init__(self)
		self.links = []
		self.db = DB.DB()

	def parse(self,parentCode, level):
		if level > 2:
			return
		page = Page()
		urls = ["http://www.weather.com.cn/data/city3jdata/", "http://www.weather.com.cn/data/city3jdata/provshi/", "http://www.weather.com.cn/data/city3jdata/station/"]
		url = urls[level]+(parentCode if parentCode  else "china")+".html"
		print "url:", url
		cities = page.download(url)
		print cities
		for k in cities:
			code = k
			name = cities[code]
			code = parentCode+code
			print code,":",name
			a = {"code":code,"parent":(parentCode if parentCode else None),"name":name}
			self.db.addCity(a);
			self.parse(code,level+1)
#parser = RssParser("http://cnbeta.feedsportal.com/c/34306/f/624776/index.rss")
#parser = RssParser("http://feed.feedsky.com/cnbeta")
#parser.parse()
grabber = City()
grabber.parse("",0)
