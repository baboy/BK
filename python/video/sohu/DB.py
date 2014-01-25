#!/usr/bin/env python
#coding=utf-8
from constants import DBConstant
import MySQLdb
import re

import sys 
reload(sys) 
sys.setdefaultencoding('utf-8')


SQL_INSERT = "INSERT INTO wp_media (appid,module,node,title,content,actors,thumbnail,pic,thumbnail_hor,pic_hor,reference_id,director,pubdate,area,duration,score,type,type_name,total_count,update_count) VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
SQL_UPDATE_CONTENT = "UPDATE wp_media set content=%s where reference_id=%s"
SQL_ADD_VIDEO= "insert into wp_media_video(sid,thumbnail,pic,thumbnail_hor,pic_hor,m3u8,sd,high,super,original,mp4,duration,content, page_url) values(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"

SQL_QUERY_RSSSOURCE = "SELECT category,source FROM cms_tvie_rss WHERE status='publish' "
class DB:
	def __init__(self):
		self.conn = MySQLdb.connect(host=DBConstant.DB_HOST, user=DBConstant.DB_USER, passwd=DBConstant.DB_PWD, db=DBConstant.DB_NAME,charset="utf8")
		self.cursor = self.conn.cursor()

	def test(self):
		self.cursor.execute("select version()")
		row = self.cursor.fetchone()
		print "test result:", row
		#self.conn.close()
	#@param a:article
	def addItem(self,a):
		global SQL_INSERT
		t = self.getType("type", a.get("type","其它"))
		area_id = self.getType("area", a.get("area","其它"))
		param = (self.appid,
				self.module,
				a.get("node"),
				a.get("title") if a.get("node")=="CONTENT" else a.get("serial_title"),
				a.get("content") if a.get("node")=="CONTENT" else a.get("serial_content"),
				a.get("actors"),
				a.get("thumbnail"), 
				a.get("pic"),
				a.get("thumbnail_hor"), 
				a.get("pic_hor"),
				a.get("reference_id"),
				a.get("director"),
				a.get("pubdate"),
				str(area_id),
				a.get("duration"),
				a.get("score"),
				str(t),
				a.get("type"),
				str(a.get("total_count")),
				str(a.get("update_count")))
		rowid = 0
		try:
			self.cursor.execute(SQL_INSERT, param)
			self.conn.commit()
			rowid = self.cursor.lastrowid
		except Exception, e:
			print "add item exception:", e
			rowid = 0
		return rowid
	def addAttr(self,sid,key,val):
		sql = "insert into wp_media_attr(sid,`key`,`value`) values(%s,%s,%s)"
		param = (str(sid),key,str(val))
		rowid = 0
		try:
			self.cursor.execute(sql, param)
			self.conn.commit()
			rowid = self.cursor.lastrowid
		except Exception, e:
			print "add attr error",e
			rowid = 0
		return rowid
	def updateVideoContent(self,reference_id,content):
		global SQL_UPDATE_CONTENT
		param = (content, reference_id )
		rowid = 0
		try:
			self.cursor.execute(SQL_UPDATE_CONTENT, param)
			self.conn.commit()
			rowid = self.cursor.lastrowid
		except Exception, e:
			print "updateVideoContent exception:", e
			rowid = 0
		return rowid
	def addVideo(self,a):
		if a.get("content"):
			self.updateVideoContent(a["reference_id"],a["content"])
		global SQL_ADD_VIDEO
		sid = str(a.get("sid"))
		param = (sid,
				a.get("thumbnail"),
				a.get("pic"),
				a.get("thumbnail_hor"), 
				a.get("pic_hor"),
				a.get("sd"),
				a.get("sd"),
				a.get("high"), 
				a.get("super"), 
				a.get("original"),
				a.get("mp4"),
				a.get("duration"),
				a.get("content"),
				a.get("page_url"))
		rowid = 0
		try:
			self.cursor.execute(SQL_ADD_VIDEO, param)
			self.conn.commit()
			rowid = self.cursor.lastrowid
		except Exception, e:
			print "add Video exception:", e,a["sid"]
			rowid = 0
		return rowid
	def addSerialVideo(self,sid,gid,index):
		sql = "insert into wp_media_serial_video(sid,gid,`index`) values(%s,%s,%s)"
		param=(str(sid),str(gid),str(index))
		rowid = 0
		try:
			self.cursor.execute(sql, param)
			self.conn.commit()
			rowid = self.cursor.lastrowid
		except Exception, e:
			print "addSerialVideo Error:", e
			rowid = 0
		return rowid

	def addSerial(self,a):
		sql = "insert into wp_media_serial(title,summary,content,video_update_count,video_total_count,tip,reference_id) values(%s,%s,%s,%s,%s,%s,%s)"
		param = (
				a.get("serial_title"),
				a.get("summary"),
				a.get("serial_desc"),
				a.get("video_update_count"),
				a.get("video_total_count"),
				a.get("tip"),
				a.get("reference_id")
				)
		rowid = 0
		try:
			self.cursor.execute(sql, param)
			self.conn.commit()
			rowid = self.cursor.lastrowid
			
		except Exception, e:
			print "add Serial Exception:",e
			rowid = 0
		return rowid
	def getTypeId(self,key,value):
		value = value.encode('utf-8')
		value = value.strip()
		r1 = re.compile("片$")
		r2 = re.compile("剧$")
		if key == "type":
			value = re.sub(r2,"",value)
		if key == "area" and  value=="内地剧":
			value = re.sub(r2,"",value)
		value = re.sub(r1,"",value)
		if value == "其他":
			value = "其它"
		sql = "select `index` from wp_media_list where gid=%s AND `key`=%s AND value=%s limit 0,1"
		param = (self.module, key, value)
		ret = None
		try:
			count = self.cursor.execute(sql, param)
			if count > 0:
				result = self.cursor.fetchone(); 
				ret = result[0]
			self.conn.commit()
			
		except Exception, e:
			print "add Serial Exception:",e
			ret = None
		return ret
	def getType(self,key,types):
		if types:
			a = types.split(";")
		t = 0
		for i in range(0, len(a) ):
			type_name = a[i]
			tid = self.getTypeId(key, type_name)
			if tid is None:
				print key,types,"select ", type_name, "error"
			else:
				t |= (1<<(tid-1))
		return t


	def close(self):
		self.cursor.close()
		self.conn.close()

#db = DB()
#db.module = "movie"
#types = "战争片;剧情片;传记片;历史片"
#t = db.getType(types)
#print t

