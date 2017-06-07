#!/bin/bash
java -jar /usr/local/bin/selenium-server-standalone-3.4.0.jar -role hub -host 0.0.0.0 > ./selenium-hub.log 2>&1 &
