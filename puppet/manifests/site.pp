info("Configuring '${::fqdn}' (${::site_domain}) using environment '${::environment}'")

# Fix for Puppet working with Vagrants
group { 'puppet': ensure => 'present', }

# Setup global PATH variable
Exec { logoutput => true, path => [
    '/usr/local/bin',
    '/opt/local/bin',
    '/usr/bin',
    '/usr/sbin',
    '/bin',
    '/sbin',
    '/usr/local/zend/bin',
], }

import 'nodes/*.pp'
