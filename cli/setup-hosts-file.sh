#!/bin/bash

# Add or remove a vhost ex. myapp.local. This will modify /etc/hosts

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[0;93m'
NC='\033[0m'

ETC_HOSTS=/etc/hosts
IP="127.0.0.1"

source "../.env"

HOSTNAME=$(echo "$DOMAIN")

HOSTS_LINE="$IP\t$HOSTNAME"

if [ -n "$(grep $HOSTNAME /etc/hosts)" ]; then
	echo -e ${YELLOW}"$HOSTNAME already exists: $(grep $HOSTNAME $ETC_HOSTS) ${NC}"
else
	echo -e ${GREEN}"Adding $HOSTNAME to your $ETC_HOSTS ${NC}"
	sudo -- sh -c -e "echo '$HOSTS_LINE' >> /etc/hosts"

	if [ -n "$(grep $HOSTNAME /etc/hosts)" ]; then
		echo -e ${GREEN}"$HOSTNAME was added succesfully \n $(grep $HOSTNAME /etc/hosts) ${NC}"
	else
		echo -e ${RED}"Failed to Add $HOSTNAME, Try again! ${NC}"
	fi
fi
