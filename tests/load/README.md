
This load test was designed with JMeter 2.11
The script depends on JMeter plugins http://jmeter-plugins.org/ (note that the error "empty test" appears if you don't install all the prerequisites).
If you get a lot of connection errors, please read this page https://wiki.apache.org/jmeter/JMeterSocketClosed (it may come from the new default settings of JMeter).

## Prepare the load test environnement

The script use the dataset provided into "Dataset.xlsx" Excel file. Inject the data by using the SQL queries prior starting the load test.
* All users are attached to a contract with an id = 1 (you may need to create it).
* All users have a position with an id = 1
* The Benchmark load test requires the OAuth2 server to be activated :
    INSERT INTO oauth_clients (client_id, client_secret, redirect_uri) VALUES ("testclient", "testpass", "http://fake/");

Please check that you have those two object created into the system prior starting the load test.

The load test make use of an agent to collect system metric. You can get this agent here :
http://jmeter-plugins.org/downloads/all/

## Fake SMTP Server

There is a lot of fake SMTP server available on the Internet. If you are using a server running Python, you can trap every mail by using this command line :
<code>sudo python -m smtpd -n -c DebuggingServer localhost:25</code>

## Reset the load test environnement

In order to reset the load test environnement, delete all leaves and and overtime requests with an ID > 1000. Then inject again the dataset.
Or execute the following SQL queries :

    DELETE FROM `leaves` WHERE id >7999;
    UPDATE `leaves` SET status = 2 WHERE id >999;
    DELETE FROM `overtime` WHERE id >1999;
    UPDATE `overtime` SET status = 2 WHERE id >999;
