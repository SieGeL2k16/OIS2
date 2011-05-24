#!/bin/sh
# -o PDF:default
echo "Removing old docs." 
rm -rf ./phpdocs/* 
echo "Creating OIS2 documentation." 
phpdoc \
       -d /html/private/OIS2/ \
       -i *.png,*.gif,*.jpg,*.sh,*.zip,*.pak,*.html,*.css,*.ico,*.gz,*.js,*.txt,*.sql,*.csv,tests/ \
       -t /html/private/OIS2/phpdocs \
       -ti "Oracle Information Site 2 (OIS2)" \
       -dn OIS2

