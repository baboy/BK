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
		self.cursor = self.conn.cursor()
		self.uid = "1"

	def test(self):
		self.cursor.execute("select version()")
		row = self.cursor.fetchone()
		print "test result:", row
		#self.conn.close()
	#@param a:article
	def addItem(self,a):
		sql = "INSERT INTO wp_ugc (appid, uid,tags, content, lat, lng, addr) VALUES(%s,%s,%s,%s,%s,%s,%s)"

		
		param = (self.appid,
				self.uid,
				a.get("tags"),
				a.get("content"), 
				a.get("lat"),
				a.get("lng"), 
				a.get("addr"))
		rowid = 0
		try:
			self.cursor.execute(sql, param)
			self.conn.commit()
			rowid = self.cursor.lastrowid
		except Exception, e:
			print "add item exception:", e
			rowid = 0
		return rowid
	def addAttr(self,sid,key,val,group):
		print "addAttr: ",sid,key,val,group
		sql = "insert into wp_media_attr(sid,`key`,`value`,`group`) values(%s,%s,%s,%s)"
		param = (str(sid),key,str(val) if val is not None else None,group)
		rowid = 0
		try:
			self.cursor.execute(sql, param)
			self.conn.commit()
			rowid = self.cursor.lastrowid
		except Exception, e:
			print "add attr error",e
			rowid = 0
		return rowid
	def update(self, param,cond):
		keys = []
		values = []
		for k in param.keys():
			keys.append("`"+k+"`")
			values.append(str(param.get(k)))
		sql = "update wp_ugc set %s where %%s " % ('=%%s , '.join(keys)+"=%%s ")
		keys = []
		for k in cond.keys():
			keys.append("`"+k+"`")
			values.append(str(cond.get(k)))
		sql = sql % ('=%s AND '.join(keys)+"=%s ")
		try:
			param = tuple(values)
			#print sql, param
			ret = self.cursor.execute(sql, param)
			self.conn.commit()
			
		except Exception, e:
			print "update Exception:",e
			ret = None
		print ret


	def addFile(self,a):

		sid = str( a.get("sid") )
		content = a.get("content")
		if content:
			self.update({"content":content},{"id":sid})

		sql = "insert into wp_ugc_file(sid,type,url,thumbnail,duration) values(%s,%s,%s,%s,%s)"

		
		param = (sid,
				a.get("type"),
				a.get("url"),
				a.get("thumbnail"),
				a.get("duration","0"))
		rowid = 0
		try:
			self.cursor.execute(sql, param)
			self.conn.commit()
			rowid = self.cursor.lastrowid
		except Exception, e:
			print "add Video exception:", e,a["sid"]
			rowid = 0
		return rowid
	def close(self):
		self.cursor.close()
		self.conn.close()

