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
		self.cursor = self.conn.cursor(cursorclass=MySQLdb.cursors.DictCursor)

	def test(self):
		self.cursor.execute("select version()")
		row = self.cursor.fetchone()
		print "test result:", row
		#self.conn.close()
	#@param a:article
	def addItem(self,a):
		sql = "INSERT INTO wp_media (appid,module,node,title,summary,content,author,type,pubdate,update_date,page_url) VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
		param = (self.appid,
				self.module,
				a.get("node"),
				a.get("title"),
				a.get("summary") ,
				a.get("content") ,
				a.get("author"),
				a.get("type"), 
				a.get("pubdate"), 
				a.get("pubdate"), 
				a.get("page_url"))
		rowid = 0
		try:
			self.cursor.execute(sql, param)
			self.conn.commit()
			rowid = self.cursor.lastrowid
		except Exception, e:
			print "add item exception:", e
			rowid = 0
		return rowid
	def where(self,p):
		where = None
		for k in p:
			v = p[k]
			if where is None:
				where = ""
			else:
				where = where + " AND "
			if v is None:
				where = where+" "+k+" is NULL "
			else:
				where = where + " "+k+"='"+str(v)+"' "
		return where
	def getNews(self,p):
		where = self.where(p)

		sql = "select * from wp_media where "+where
		print sql
		news = 0
		try:
			count = self.cursor.execute(sql)
			if count > 0:
				news = self.cursor.fetchall()
			self.conn.commit()
		except Exception, e:
			print "add item exception:", e
			news = None
		return news

	def update(self,param,cond):
		s_set = None
		for k in param:
			v = param[k]
			if s_set is None:
				s_set = ""
			else:
				s_set = s_set + ","
			if v is None:
				v = "NULL"
			s_set = s_set + " "+k+"='"+str(v)+"' "
		s_where = self.where(cond)
		sql = "UPDATE wp_media SET %s WHERE %s" % (s_set, s_where)
		print sql
		ret = 0
		try:
			ret = self.cursor.execute(sql)
			self.conn.commit()
		except Exception, e:
			print "update:",e
			ret = 0
		return True if ret > 0 else False

	def addAttr(self, sid, key, i, val):
		sql = "insert into wp_media_attr(sid,`key`,`index`,`value`) values(%s,%s,%s,%s)"
		param = (str(sid),key,str(i),str(val))
		rowid = 0
		try:
			self.cursor.execute(sql, param)
			self.conn.commit()
			rowid = self.cursor.lastrowid
		except Exception, e:
			print "add attr error",e
			rowid = 0
		return rowid

	def addAttachment(self, param):
		keys = param.keys()
		s_keys = "`"+("`,`".join(keys))+"`"
		placeholders = ["%s" for i in range(len(keys))]
		sql = "insert into wp_media_attachment(%s) values(%s)" % (s_keys, ",".join(placeholders))
		values = tuple(param.values())
		rowid = 0
		try:
			self.cursor.execute(sql, values)
			self.conn.commit()
			rowid = self.cursor.lastrowid
		except Exception, e:
			print "add attr error", e
			rowid = 0
		return rowid

	def close(self):
		self.cursor.close()
		self.conn.close()
