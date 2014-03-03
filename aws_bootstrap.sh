#!/bin/bash

if [ "$EUID" -ne "0" ] ; then
        echo "Script must be run as root." >&2
        exit 1
fi

if which puppet > /dev/null ; then
        echo "Puppet is already installed"
        exit 0
fi

echo "Updating Aptitude on machine"
aptitude update
echo "Installing Pupppet"
apt-get install -y puppet