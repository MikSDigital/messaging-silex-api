#!/usr/bin/env bash

DB_NAME=$1
DB_USERNAME=$2
DB_PASSWORD=$3
PROJECT=$4

echo "Updating"
apt-get update

echo "Installing Apache"
apt-get install -y apache2
a2enmod rewrite

echo "Preparing MySQL"
apt-get install debconf-utils -y
debconf-set-selections <<< "mysql-server mysql-server/root_password password $DB_PASSWORD"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $DB_PASSWORD"

echo "Installing MySQL"
apt-get install -y mysql-server libapache2-mod-auth-mysql php5-mysql

echo "Creating database $DB_NAME"
mysql -u$DB_USERNAME -p$DB_PASSWORD -e "CREATE DATABASE $DB_NAME"
mysql -u$DB_USERNAME -p$DB_PASSWORD -e "grant all privileges on $DB_NAME.* to '$DB_USERNAME'@'localhost' identified by '$DB_PASSWORD'"

echo "Installing PHP"
apt-get install -y php5
apt-get install -y libapache2-mod-php5 php5-mcrypt
apt-get install -y php5-cli php5-common php5-dev
apt-get install -y curl php5-curl php5-gd php5-intl

echo "Setting up sync directory"

# Create project directory
if ! [ -L "/var/www/$PROJECT" ]; then
    rm -rf "/var/www/$PROJECT"
    ln -fs /vagrant "/var/www/$PROJECT"
fi

# Move vhost file
mv "/home/vagrant/$PROJECT.conf" /etc/apache2/sites-enabled/

echo "Finishing"
service apache2 restart
