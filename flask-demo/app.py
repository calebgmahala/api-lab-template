from flask import Flask, make_response, request, redirect
from flaskext.mysql import MySQL
import simplejson as json
import os
app = Flask(__name__)

app.config['MYSQL_DATABASE_USER'] = 'root'
app.config['MYSQL_DATABASE_DB'] = 'apidb'
app.config['MYSQL_DATABASE_HOST'] = 'localhost'
# app.config['MYSQL_DATABASE_port'] = '3306'
# app.config['MYSQL_DATABASE_CHARSET'] = 'utf-8'
mysql = MySQL()
mysql.init_app(app)

conn =  mysql.connect()
cur = conn.cursor()

@app.route("/people", methods=['GET', 'POST'])
def people():
	if request.method == 'GET':
		cur.execute("SELECT * FROM people")
		people = cur.fetchall()
		resp = json.dumps(people)
		return resp
	elif request.method == 'POST':
		body = json.dumps(request.form)
		cur.execute('INSERT INTO people (name, age, occupation) VALUES(%(name)s, %(age)s, %(occupation)s)', json.loads(body))
		conn.commit()
		return redirect('localhost:5000/people', code=200)

@app.route("/people/<int:id>", methods=['GET', 'DELETE', 'PUT'])
def person(id):
	if request.method == 'GET':
		cur.execute('SELECT * FROM people WHERE id = %d' % id)
		person = cur.fetchall()
		resp = json.dumps(person)
		return resp
	elif request.method == 'PUT':
		body = json.dumps(request.form)
		cur.execute('UPDATE people SET name = %(name)s, age = %(age)s, occupation = %(occupation)s WHERE id = 1', json.loads(body))
		conn.commit()
		return redirect('localhost:5000/people', code=200)

	elif request.method == 'DELETE':
		cur.execute('DELETE FROM people where id= %d' % id)
		conn.commit()
		return redirect('localhost:5000/people', code=200)