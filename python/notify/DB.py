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
	def queryTokens(self,offset,count):
		sql = "select token from wp_ios_device_token order by id asc limit %s, %s" % (str(offset), str(count))
		ret = None
		try:
			count = self.cursor.execute(sql)
			self.conn.commit()
			if count > 0:
				ret = self.cursor.fetchall()
		except Exception, e:
			print "getChannelId:",e
		return ret
	def close(self):
		self.cursor.close()
		self.conn.close()

#db = DB()
#print db.hasContent(1)