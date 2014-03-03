# = Class: apt::repo::zend
#
# This class installs the zend repo for Zend Server
#
class zendserver::apt::repo::zend {
    case $::operatingsystem {
        Ubuntu: {
            apt::repository { 'zend':
                url        => 'http://repos.zend.com/zend-server/6.0/deb',
                distro     => 'server',
                repository => 'non-free',
                key_url    => 'http://repos.zend.com/zend.key',
                key        => 'F7D2C623',
            }
        }
        Debian: {
            apt::repository { 'zend':
                url        => 'http://repos.zend.com/zend-server/6.0/deb',
                distro     => 'server',
                repository => 'non-free',
                key_url    => 'http://repos.zend.com/zend.key',
                key        => 'F7D2C623',
            }
        }
        default: {}
    }
}
