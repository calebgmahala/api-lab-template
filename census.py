import matplotlib.pyplot as plt
import numpy as np
import csv

results = []
county = []
pop = []

with open("census.csv") as csvfile:
	reader = csv.reader(csvfile)
	next(reader, None)
	for row in reader:
		county.append(row[2])
		pop.append(int(row[3]))
plt.bar(county, pop)
plt.xticks(rotation=90)
plt.show()
