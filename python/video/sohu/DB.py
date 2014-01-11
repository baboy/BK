from constants import DBConstant
import MySQLdb


SQL_INSERT = "INSERT INTO wp_media (appid,module,title,actors,thumbnail,pic,reference_id,director,pubdate,area,duration,score,type) VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
SQL_UPDATE_CONTENT = "UPDATE wp_media set content=%s where reference_id=%s"
SQL_ADD_VIDEO= "insert into wp_media_video(sid,thumbnail,pic,m3u8,sd,high,super,origin,mp4,duration,content, page_url) values(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"

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
		appid = "BK"
		module = "video"
		param = (appid,
				module,
				a.get("title"),
				a.get("actors"),
				a.get("thumbnail"), 
				a.get("pic"),
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
		self.updateVideoContent(a["reference_id"],a["content"])
		global SQL_ADD_VIDEO
		sid = str(a.get("sid"))
		param = (sid,
				a.get("thumbnail"),
				a.get("pic"),
				a.get("sd"),
				a.get("sd"),
				a.get("high"), 
				a.get("super"), 
				a.get("origin"),
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


	def close(self):
		self.cursor.close()
		self.conn.close()

	
