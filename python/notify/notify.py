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
from APNSWrapper import *
import binascii


class APNSNotify:
	def __init__(self):
		self.links = []
		self.db = DB.DB()

	def handle(self, msg):
		tokens = self.db.queryTokens(0,10)
		wrapper = APNSNotificationWrapper('/usr/local/tvie/www/x-team/BK/python/notify/ck.pem', True)
		for item in tokens:
			token = item.get("token", None)
			if token:
				deviceToken = binascii.unhexlify(token)
				# create message
				apns = APNSNotification()
				apns.token(deviceToken)
				apns.badge(1)
				apns.alert(msg)
				apns.sound()
				wrapper.append(apns)

		wrapper.notify()

apns = APNSNotify();
apns.handle(sys.argv[1]) 