#!/usr/bin/env python
#coding=utf-8
from constants import DBConstant
import MySQLdb
import re

import sys 
reload(sys) 
sys.setdefaultencoding('utf-8')


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
		sql = "INSERT INTO wp_media (appid,module,node,title,summary,author,type,pubdate,update_date,reference_id,page_url) VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
		param = (self.appid,
				self.module,
				a.get("node"),
				a.get("title"),
				a.get("summary") ,
				#a.get("content") ,
				a.get("author"),
				a.get("type"), 
				a.get("pubdate"), 
				a.get("pubdate"), 
				None,#a.get("reference_id"), 
				a.get("page_url"))
		rowid = 0
		try:
			self.cursor.execute(sql, param)
			self.conn.commit()
			rowid = self.cursor.lastrowid
		except Exception, e:
			print "add item exception:", a.get("title"), e
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
	def hasContent(self,sid):
		try:
			print "hasContent:",sid
			sql = "SELECT id FROM wp_media_content WHERE sid='"+str(sid)+"'"
			ret = self.cursor.execute(sql)
			self.conn.commit()
		except Exception, e:
			print "hasContent:",e, sql
			ret = 0
		return True if ret else False
	def updateContent(self,content,sid):

		sql = "UPDATE wp_media_content SET content=%s WHERE sid=%s"
		p = [content,str(sid)]
		if not self.hasContent(sid) :
			sql = "INSERT INTO wp_media_content (content,sid) VALUES(%s,%s)"
		ret = 0
		try:
			ret = self.cursor.execute(sql,tuple(p))
			self.conn.commit()
		except Exception, e:
			print "updateContent:",e
			ret = 0
		return True if ret > 0 else False
	def update(self,param,cond):
		s_set = None
		p = []
		for k in param:
			v = param[k]
			if s_set is None:
				s_set = ""
			else:
				s_set = s_set + ","
			if v is None:
				v = "NULL"
			s_set = s_set + " %s=%%s " % (k,)
			p.append(str(v))
		s_where = self.where(cond)
		sql = "UPDATE wp_media SET %s WHERE %s" % (s_set, s_where)
		ret = 0
		try:
			ret = self.cursor.execute(sql,tuple(p))
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
		sql = "insert into wp_media_attr(%s) values(%s)" % (s_keys, ",".join(placeholders))
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

#db = DB()
#print db.hasContent(1)