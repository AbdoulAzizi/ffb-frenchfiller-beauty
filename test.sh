#!/bin/bash    
HOST="vps720094.ovh.net"
USER="apache"
PASS="AxsBPz6es3EH"
FTPURL="ftp://$USER:$PASS@$HOST"
LCD="/var/www/html/ffb"
RCD="./"
#DELETE="--delete"
lftp -c "set ftp:list-options -a;
open '$FTPURL';
lcd $LCD;
cd $RCD;
mirror --verbose \
--exclude-glob  .git/*
--exclude-glob var/cache/*
"
