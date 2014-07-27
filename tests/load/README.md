
This load test was designed with JMeter 2.11
The script depends on JMeter plugins http://jmeter-plugins.org/ (note that the error "empty test" appears if you don't installa the prerequisites).

Prepare the load test environnement

The script use the dataset provided into "Dataset.xlsx" Excel file. Inject the data by using the SQL queries prior starting the load test.
* All users are attached to a contract with an id = 1
* All users have a position with an id = 1
Please check that you have those two object created into the syste prior starting the load test.

Reste the load test environnement

In order to reset the load test environnement, delete all leaves and and overtime requests with an ID > 1000. Then inject again the dataset.
