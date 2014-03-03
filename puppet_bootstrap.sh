#!/bin/bash

if [ -e /var/local/puppet-bootstrapped ] ; then
    echo "Skipping puppet bootstrap as it appears to already have been done."
    exit 0
fi

if [ "$EUID" -ne "0" ] ; then
    echo "Script must be run as root." >&2
    exit 1
fi

wget -O /tmp/puppetlabs-release-precise.deb https://apt.puppetlabs.com/puppetlabs-release-precise.deb
dpkg -i /tmp/puppetlabs-release-precise.deb

apt-get update
apt-get install puppet -y

mkdir -p /etc/puppet/modules

if which puppet > /dev/null ; then

    puppet module install puppetlabs/git
    puppet module install  example42/puppi
    puppet module install  example42/apt
    puppet module install  example42/apache
    puppet module install  example42/sysctl
    puppet module install  example42/vim

    puppet module install  puppetlabs/gcc
    puppet module install  puppetlabs/vcsrepo
    puppet module install  puppetlabs/mysql
    
    puppet module install  maestrodev/wget
    puppet module install  tPl0ch/composer
    puppet module install  evenup/ec2_tools
    puppet module install elasticsearch/elasticsearch
    
fi

touch /var/local/puppet-bootstrapped

