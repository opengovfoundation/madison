node default {
    include apt
    include stdlib
    include git

    case $::environment { 
        development: {
            include app::database
            include app::webserver
            include app::codebase
            
            sysctl::value { 'vm.overcommit_memory': value => '1' }
            
        }
        ec2 : {
            include app::codebase
            include app::webserver
            include app::database
            include ec2
        }
    }

    package { 'unzip' :
       ensure => present
    }

    package { 'vim' :
        ensure => present
    }
    
    package { 'autoconf' :
        ensure => present
    }
    
    package { 'make' :
        ensure => present
    }
        
    sysctl::value { 'fs.file-max': value => '100000' }

    exec { "apt-get clean" :
      command => "/usr/bin/apt-get clean"
    }
    
    exec { "apt-update":
      command => "/usr/bin/apt-get update",
      require => [ Exec['apt-get clean'] ]
    }
    
    Exec["apt-update"] -> Package <| |>
}
