# Behat 3,  Selenium 3.4, Firefox nightly and Chrome stable

Line-by-line steps to set up Behat 3.3(-dev), Selenium 3.4.0, Geckodriver v0.16.1, Firefox nightly, Chromedriver 2.29, and Chrome stable on Ubuntu 16.04

### As root

`apt-get -y update`

`apt-get -y upgrade`

Run steps: https://www.digitalocean.com/community/tutorials/initial-server-setup-with-ubuntu-14-04

Run Steps: https://www.digitalocean.com/community/tutorials/how-to-configure-virtual-memory-swap-file-on-a-vps

## Install JRE

`apt-get install -y default-jre language-pack-en-base`

## Install PHP 7

`LC_ALL=en_US.UTF-8 add-apt-repository ppa:ondrej/php`

`apt-get update`

`apt-get install -y php7.0 php7.0-curl php7.0-json php7.0-gd php7.0-fpm php7.0-cli php-yaml php7.0-mcrypt php-imagick php-ssh2 php7.0-mbstring php7.0-xml`

## Install Composer

```
cd ~
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === '669656bab3166a7aff8a7506b8cb2d1c292f042046c5a994c43155c0be6190fa0355160742ab2e1c88d40d5be660b410') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
mv composer.phar /usr/local/bin/composer
apt-get install -y git zip unzip

```

## Extra pieces required for selenium

```
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

## If you wish to be able to auto-pull when a push to a repo is made, we'll need nginx

At the time of writing, nginx installed through default apt-get repos is 1.10. The mainline, with extra bits and pieces for http/2 and bug fixes is 1.13.1. If you're happy with 1.10 then simply `sudo apt-get install nginx` will be just fine. However, I like new things, so...

`sudo nano /etc/apt/sources.list`

Add the following to the bottom of the file;

```
deb http://nginx.org/packages/mainline/ubuntu/ xenial nginx
deb-src http://nginx.org/packages/mainline/ubuntu/ xenial nginx
```

```
cd ~
wget http://nginx.org/keys/nginx_signing.key
sudo apt-key add nginx_signing.key
sudo apt update
```

Now we have the new repo, we can install the nginx mainline. But first you may be told there are other available upgrades. If so, run `sudo apt-get upgrade` first.

```
sudo apt install nginx
```

```
sudo systemctl unmask nginx
sudo systemctl start nginx
sudo systemctl enable nginx
```

```
sudo nano /etc/php/7.0/fpm/pool.d/www.conf
```

Change `user nginx;` to `user www-data;`

```
sudo systemctl reload nginx
```

Vist your machine's IP in a browser and you should see the nginx welcome screen. Hoorah.

Then the server is set up and it's down to behat settings (see /project)

~/behat/project/

bin/ (run `./bin/behat` from the `~/behat/project/` directory)
 - behat
 - phpunit

build/html/behat (This is where the reports go after running tests)
 - assets/
    - index.html
 - sites/
    - sitename.com/
       - firefox/
         - assets/
         - index.html
       - chrome/
         - assets/
         - index.html

configs/
 - sitename.com/
   - behat-config.yml
 - readme.md
 - .git (git repo tzatziki-features)

features/
 - bootstrap/
   - FeatureContext.php
   - Utils.php
 - sites/
   - sitename.com/
     - features/
       - test1.feature
       - test2.feature
   - readne.md
   - .git (git repo tzatziki-configs)

lib/
 - BrowserStackContext.php
 - parallel.php

public/
 - config.json
 - debug.log
 - github-update.sh
 - github-webhook.php
 - index.html
 - index.php

screenshots/
 - failure1.html
 - failure2.html

vendor/
 - whole
 - lotta
 - dependencies

behat.yml
composer.json
composer.lock
