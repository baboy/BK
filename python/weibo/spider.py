 # -*- coding:utf-8 -*-
import urllib2, sys
import json
import dateutil.parser
import time
from HTMLParser import HTMLParser
import re
from lxml import etree
import lxml.html.soupparser as soupparser

class WeiboParser( HTMLParser):
	def __init__(self, url):
		self.url = url
		HTMLParser.__init__(self)
		self.links = []


	def handle_starttag(self, tag, attrs):
		if tag=="video":
			print attrs
	
	def parse_statuses(self, statuses):
		if "statuses" in statuses:
			statuses = statuses["statuses"]

		vsrc = None
		vtype = None

		for status in statuses:
			txt = status["text"]
			re_url = r"(http://[0-9a-zA-Z_-]+\.(?:[0-9a-zA-Z-_\.\/]+))"
			ret = re.findall(re_url, txt, re.S)
			if not ret:
				continue
			for url in ret:
				vsrc, vtype = self.download_page(url)
			pic = status["original_pic"] if status.has_key("original_pic") else None
			content = status["text"]
			regex_url = re.compile(re_url);
			regex_search = re.compile(r"#.+#")
			content = regex_url.sub("", content)

			content = regex_search.sub("", content)
			if vsrc and vtype:
				print vsrc, vtype, pic
#print content


	def download_page(self, page_url):
		user_agent = "Mozilla/5.0 (iPhone; CPU iPhone OS 6_0_1 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A523 Safari/8536.25"
		urllib2.socket.setdefaulttimeout(10)
		request = urllib2.Request(page_url)
		request.add_header("User-Agent", user_agent)
		response = urllib2.urlopen(request)
		data = response.read()
		html_tree = etree.HTML(data)
		nodes = html_tree.xpath("//video/source[@src]")
		vsrc = None
		vtype = None
		print response.url
		for node in nodes:
			s = node.get("src")
			t = node.get("type")
			if s and t and t.startswith("video"):
				vsrc,vtype = s,t
				break
		response.close();
		return vsrc, vtype
	
	def parse(self):
		print "rss: ",self.url
		stime = time.time()
		print "<download start> at time: %s"%time.strftime('%Y-%m-%d %H:%M:%S',time.localtime(stime))
		#get rss data
		urllib2.socket.setdefaulttimeout(10)
		response = urllib2.urlopen(self.url)
		print response.headers
		data = response.read() 
		response.close()
		etime = time.time()
		print "<download end> at time: %s cost:%f"%(time.strftime('%Y-%m-%d %H:%M:%S',time.localtime(etime)),etime-stime)
		# parse
		stime = etime
		self.parse_statuses(json.loads(data) );
		etime = time.time()
		print "<parse end> at time: %s cost:%f"%(time.strftime('%Y-%m-%d %H:%M:%S',time.localtime(etime)),etime-stime)

parser = WeiboParser("http://m.tvie.com.cn/mcms/api2/mod/sns/feeds.php?uid=2214257545")
parser = WeiboParser("http://m.tvie.com.cn/mcms/api2/mod/sns/feeds.php?uid=1640601392")
parser.parse()
