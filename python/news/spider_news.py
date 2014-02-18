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
		print response.headers
		data = response.read() 
		response.close()
		return data

class RssParser( HTMLParser):
	def __init__(self, url):
		self.url = url
		HTMLParser.__init__(self)
		self.links = []
		self.db = DB.DB()
		self.db.appid = "BK"
		self.db.module = "news"

	def toDict(self, et):
	    d = {et.tag : map(self.toDict, et.iterchildren())}
	    d.update(('@' + k, v) for k, v in et.attrib.iteritems())
	    d['text'] = et.text
	    return d
	def parse(self):
		page = Page()
		text = page.download(self.url)
		e = etree.parse(StringIO.StringIO(text))
		rss = e.getroot()
		for item in rss.findall('.//item'):
			ar = {"node":"CONTENT"}
			fields = {"title":"title", "link":"link", "summary":"description", "content":"content","author":"author","pubdate":"pubDate","page_url":"guid"}
			for k2 in fields:
				k = fields[k2]
				node = item.find(k)
				if node is not None:
					text = node.text
					if k == "description":
						text = re.sub(r'</?\w+[^>]*>','',text);
					if k == "pubDate":
						t = datetime.datetime(*eut.parsedate(text)[:6])
						text = t.strftime('%Y-%m-%d %H:%M:%S')
					ar[k2] = text
			if ar.get("author") is None:
				ar["author"] = "cnbeta.com"
			ar["type"] = "1"
			if ar.has_key("page_url"):
				m = md5.new()
				m.update(ar.get("page_url"))
				ar["reference_id"] = m.hexdigest()
			#print ar
			rowid = self.db.addItem(ar)
			print ar.get("title"), rowid
class ContentGrabber( HTMLParser):
	def __init__(self):
		HTMLParser.__init__(self)
		self.links = []
		self.db = DB.DB()
		self.db.appid = "BK"
		self.db.module = "news"

	def parseImages(self,s):
		imgs = re.compile(r'<img[^>]*/?>').findall(s)
		if imgs is None:
			return (s,None)
		items = []
		for i in range(0, len(imgs)):
			img = imgs[i]
			p = re.compile(r'(\w+)\s*=\s*(?:(?:(?:["\'])([^"\']*)(?:["\']))|([^\/\s]*))')
			m = p.findall(img)
			item = {"original": img,"index": i}
			if m:
				for attr in m:
					k = attr[0]
					if k in ["alt","title","src","width","height"]:
						item[k] = attr[1]

			s = s.replace(img,"<!--{%d}-->" % (i,))
			items.append(item)
		return (s,items)


	def parseContent(self,doc):
		nodes = doc.xpath("//div[@class='content']")
		c_node = None
		if len(nodes)>0:
			c_node = nodes[0]
		if c_node is None:
			return None

		remove_attribute_tags = ["p","div","span"]
		for tag in remove_attribute_tags:
			nodes = c_node.findall(".//"+tag)
			for node in nodes:
				if node.attrib is not None:
					for k in node.keys():
						node.attrib.pop(k)


		text = lhtml.tostring(c_node, pretty_print=True, encoding='utf-8')
		remove_tags = ["a"]
		for tag in remove_tags:
			text = re.sub(r'</?'+tag+r'+[^>]*>','',text);
		text = re.sub("&#13;","",text)
		#bom = unicode(codecs.BOM_UTF8, "utf8" )
		#text = text.replace(bom,"xxxx")
		#text = text.replace(codecs.BOM,"xxxx")
		text = text.strip(" \n")
		#delete prefix tag
		text = re.sub(r"^<div[^>]*>","",text)
		#delete postfix tag
		text = re.sub(r"</div[^>]*>$","",text)
		# delete empty tag
		text = re.sub(r"<(\w+)[^>/]*>[\s]*</\1>","",text)
		return text,self.parseImages(text)[1]

	def parseSummary(self, doc):
		nodes = doc.xpath("//div[@class='introduction']")
		c_node = None
		if len(nodes)>0:
			c_node = nodes[0]
		if c_node is None:
			return None

		remove_attribute_tags = ["p","div","span"]
		for tag in remove_attribute_tags:
			nodes = c_node.findall(".//"+tag)
			for node in nodes:
				if node.attrib is not None:
					for k in node.keys():
						node.attrib.pop(k)


		text = lhtml.tostring(c_node, pretty_print=True, encoding='utf-8')
		remove_tags = ["a","img"]
		for tag in remove_tags:
			text = re.sub(r'</?'+tag+r'+[^>]*>','',text);
		text = re.sub("&#13;","",text)
		text = text.strip(" \n")
		#delete prefix tag
		text = re.sub(r"^<div[^>]*>","",text)
		#delete postfix tag
		text = re.sub(r"</div[^>]*>$","",text)
		# delete empty tag
		text = re.sub(r"<(\w+)[^>/]*>[\s]*</\1>","",text)
		text = re.sub(r'</?\w+[^>]*>','',text);
		text = text.strip(" \n\r\t")
		return text

	def parse(self):
		news = self.db.getNews({"module":"news"})
		if news is None or len(news)==0:
			return
		for item in news:
			sid = item["id"]
			page_url = item["page_url"]
			page = Page()
			text = page.download(page_url).decode("utf-8")
			doc = etree.HTML(text)
			summary = self.parseSummary(doc)
			#print summary

			text, imgs = self.parseContent(doc)
			if summary is not None:
				text = "<p>"+summary+"</p>"+text
			#print sid, text, imgs
			ret_update_content = self.db.updateContent(text, sid)
			for i in range(len(imgs)):
				img = imgs[i]
				meta = json.dumps(img)
				#meta = re.sub('"','\\"',meta)
				#meta = re.sub("'","\\'",meta)
				attachment = {"type":"image"}
				attachment["metadata"] = meta
				attachment["sid"] = sid
				attachment["description"] = img.get("title") if img.has_key("title") else img.get("alt")
				
				src = img["src"]
				m = md5.new()
				m.update(src)
				attachment["key"] = m.hexdigest()
				#print attachment
				attach_id = self.db.addAttachment(attachment)
				print item["title"], ret_update_content, attach_id


#parser = RssParser("http://cnbeta.feedsportal.com/c/34306/f/624776/index.rss")
#parser = RssParser("http://feed.feedsky.com/cnbeta")
#parser.parse()
grabber = ContentGrabber()
grabber.parse()
