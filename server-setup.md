# Behat Selenium 3.4

`apt-get -y update`

`apt-get -y upgrade`

Run steps: https://www.digitalocean.com/community/tutorials/initial-server-setup-with-ubuntu-14-04
Run Steps: https://www.digitalocean.com/community/tutorials/how-to-configure-virtual-memory-swap-file-on-a-vps

`apt-get install -y default-jre`

`apt-get install -y language-pack-en-base`

`LC_ALL=en_US.UTF-8 add-apt-repository ppa:ondrej/php`

`apt-get update`

`apt-get install -y php7.0 php7.0-curl php7.0-json php7.0-gd php7.0-fpm php7.0-cli php-yaml php7.0-mcrypt php-imagick php-ssh2 php7.0-mbstring php7.0-xml`

```
cd ~
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === '669656bab3166a7aff8a7506b8cb2d1c292f042046c5a994c43155c0be6190fa0355160742ab2e1c88d40d5be660b410') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
mv composer.phar /usr/local/bin/composer
apt-get install -y git zip unzip

apt-get install -y libxi6 libgconf-2-4 libxss1 libappindicator1 libindicator7
apt-get install -y openjdk-8-jre-headless xvfb libxi6 libgconf-2-4
```

### As non-root user

## Setup for Selenium

```
cd ~
mkdir behat
cd behat/
curl -J -O -L https://selenium-release.storage.googleapis.com/3.4/selenium-server-standalone-3.4.0.jar
chmod +x selenium-server-standalone-3.4.0.jar
sudo mv -f selenium-server-standalone-3.4.0.jar /usr/local/share
sudo ln -s /usr/local/share/selenium-server-standalone-3.4.0.jar /usr/local/bin/selenium-server-standalone-3.4.0.jar
```

## Setup for Chrome

```
cd ~
wget -N https://chromedriver.storage.googleapis.com/2.29/chromedriver_linux64.zip -P ~/
unzip chromedriver_linux64.zip -d ~/
rm chromedriver_linux64.zip
sudo mv -f ~/chromedriver /usr/local/share
sudo chmod +x /usr/local/share/chromedriver
sudo ln -s /usr/local/share/chromedriver /usr/local/bin/chromedriver
```

```
wget -N https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb -P ~/
sudo dpkg -i --force-depends ~/google-chrome-stable_current_amd64.deb
sudo apt-get -f install -y
sudo dpkg -i --force-depends ~/google-chrome-stable_current_amd64.deb
rm google-chrome-stable_current_amd64.deb
```

## Setup for Firefox

```
cd ~
wget -N 'https://github.com/mozilla/geckodriver/releases/download/v0.16.1/geckodriver-v0.16.1-linux64.tar.gz' -P ~/
tar -xvzf geckodriver-v0.16.1-linux64.tar.gz
rm geckodriver-v0.16.1-linux64.tar.gz
sudo mv -f geckodriver /usr/local/share
sudo chmod +x /usr/local/share/geckodriver
sudo ln -s /usr/local/share/geckodriver /usr/local/bin/geckodriver
```

```
wget -N 'https://download.mozilla.org/?product=firefox-nightly-latest-ssl&os=linux64&lang=en-US' -P ~/
mv index.html\?product\=firefox-nightly-latest-ssl\&os\=linux64\&lang\=en-US firefox-nightly.tar.bz2
bzip2 -d firefox-nightly.tar.bz2
tar -xvf firefox-nightly.tar
rm firefox-nightly.tar
sudo mv firefox /usr/local/
sudo ln -s /usr/local/firefox/firefox /usr/bin/firefox
```

## Scripts to fire things up

`cd ~/behat/`


`nano start-selenium-hub.sh`

Fill with

```
#!/bin/bash
java -jar /usr/local/bin/selenium-server-standalone-3.4.0.jar -role hub -host 0.0.0.0 > ./selenium-hub.log 2>&1 &
```

`nano start-selenium-node.sh`

Fill with

```
#!/bin/bash

if [ ! -f /tmp/.X5-lock ]; then
    /usr/bin/Xvfb :10 -ac -screen 0 1280x800x8 &
fi

export DISPLAY=:10

java -jar /usr/local/bin/selenium-server-standalone-3.4.0.jar -role node -hub http://127.0.0.1:4444/grid/register/ > ./selenium-node.log 2> ./selenium-node.err &
```

`chmod +x start-selenium-hub.sh`

`chmod +x start-selenium-node.sh`

Then the server is set up and it's down to behat settings (see /project)
