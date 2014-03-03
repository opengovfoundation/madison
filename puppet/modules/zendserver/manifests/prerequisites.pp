# Class: zendserver::prerequisites
#
# This class installs zendserver prerequisites
#
# == Variables
#
# Refer to zendserver class for the variables defined here.
#
# == Usage
#
# This class is not intended to be used directly.
# It's automatically included by zendserver if the parameter
# install_prerequisites is set to true
# Note: This class may contain resources available on the
# Example42 modules set
#
class zendserver::prerequisites {
    case $::operatingsystem {
        redhat,centos,scientific,oraclelinux : {
            require zendserver::yum::repo::zend
        }
        ubuntu,debian : {
            require zendserver::apt::repo::zend
        }
        default: { }
    }
}
