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
		sql = "INSERT INTO contact (name,mobile,business, keyword, area, address,company,lat,lng) VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s)"
		param = (
				a.get("name"),
				a.get("mobile"),
				a.get("business"),
				a.get("keyword"),
				a.get("area"),
				a.get("address"),
				a.get("company"),
				a.get("lat"),
				a.get("lng"),
				)
		rowid = 0
		try:
			print param
			self.cursor.execute(sql, param)
			self.conn.commit()
			rowid = self.cursor.lastrowid
		except Exception, e:
			print "add item exception:", a.get("name"), e
			rowid = 0
		return rowid
	def close(self):
		self.cursor.close()
		self.conn.close()

#db = DB()
#print db.hasContent(1)