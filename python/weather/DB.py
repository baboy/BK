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
	def addCity(self,a):
		sql = "INSERT INTO wp_weather_city (parent,code,name) VALUES(%s,%s,%s)"
		param = (a.get("parent","0"),
				a.get("code"),
				a.get("name"))
		rowid = 0
		try:
			self.cursor.execute(sql, param)
			self.conn.commit()
			rowid = self.cursor.lastrowid
		except Exception, e:
			print "add item exception:", a.get("title"), e
			rowid = 0
		return rowid

	def close(self):
		self.cursor.close()
		self.conn.close()

#db = DB()
#print db.hasContent(1)