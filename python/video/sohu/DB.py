#!/usr/bin/env python
#coding=utf-8
from constants import DBConstant
import MySQLdb


SQL_INSERT = "INSERT INTO wp_media (appid,module,node,title,content,actors,thumbnail,pic,thumbnail_hor,pic_hor,reference_id,director,pubdate,area,duration,score,type) VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
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
				a.get("area"),
				a.get("duration"),
				a.get("score"),
				a.get("type"))
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
	def getTypeId(self,name):
		sql = "select id from wp_media_type where name='"+name+"'"
		param = tuple(name)
		param = None
		ret = None
		try:
			ret = self.cursor.execute(sql, param)
			self.conn.commit()
			
		except Exception, e:
			print "add Serial Exception:",e
			ret = None
		return ret

	def close(self):
		self.cursor.close()
		self.conn.close()


