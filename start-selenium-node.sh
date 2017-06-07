#!/bin/bash

if [ ! -f /tmp/.X5-lock ]; then
    /usr/bin/Xvfb :10 -ac -screen 0 1280x800x8 &
fi

# Firefox needs this
export DISPLAY=:10

java -jar /usr/local/bin/selenium-server-standalone-3.4.0.jar -role node -hub http://127.0.0.1:4444/grid/register/ > ./selenium-node.log 2> ./selenium-node.err &
