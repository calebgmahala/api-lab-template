import matplotlib
matplotlib.use('Agg')
from flask import Flask, make_response, request, redirect, jsonify
import requests as req
import matplotlib.pyplot as plt
from matplotlib.pyplot import figure, title, axes, legend
import numpy as np
import mpld3
import time
app = Flask(__name__)

def make_api_request(symbol):
	url = "https://www.alphavantage.co/query?function=TIME_SERIES_WEEKLY&symbol=%s&apikey=UWQPK2ZPSPLFDIB5" % symbol
	r = req.get(url).json()
	highs = {
		'value': [],
		'length': []
	}
	count = 0
	for keys, values in r['Weekly Time Series'].items():
		highs['value'].append(float(values.get("2. high")))
		highs['length'].append(count)
		count = count +1
	return highs

@app.route("/", methods=['GET'])
def index():

	apple = make_api_request('AAPL')
	snap = make_api_request('SNAP')
	ea = make_api_request('EA')

	data = {
		'apple': apple['value'],
		'apple_l': apple['length'],
		'snap': snap['value'],
		'snap_l': snap['length'],
		'ea': ea['value'],
		'ea_l': ea['length']
	}

	def xy(value):
		return data.get(value)

	fig = plt.figure(1, figsize=(8, 6))
	plt.plot(xy('apple_l'), xy('apple'), 'r', label="Apple")
	plt.plot(xy('snap_l'), xy('snap'), 'g', label="Snapchat")
	plt.plot(xy('ea_l'), xy('ea'), 'b', label="Electronic Arts")
	plt.xlabel('Weeks')
	plt.ylabel('Dollars')
	plt.legend(bbox_to_anchor=(.5, .5), loc='upper left', borderaxespad=0.)
	return mpld3.fig_to_html(fig, template_type='simple')