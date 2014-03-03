class app::webserver {
    
    class { 'composer':
        target_dir   => '/usr/local/bin',
        composer_file => 'composer',
    }
    
    class { 'apache': }
    
    class { 'zendserver':
        php_version => $::php_version,
        use_ce => false
    }
    
    file { "/usr/local/bin/pear" : 
        target => '/usr/local/zend/bin/pear',
        ensure => 'link',
        require => [ Class['zendserver'] ]
    }
    
    exec { "install yaml" :
        command => "yes | pecl install yaml",
        require => [ Class['zendserver'], File['/usr/local/bin/pecl'] ]
    }
    
    file { "/usr/local/zend/etc/conf.d/yaml.ini" :
       owner => "root",
       group => "zend",
       content => "extension=yaml.so",
       require => [ Exec['install yaml'] ],
       notify => Service['apache']
    }
    
    exec { "migrate database" :
        command => "php artisan migrate",
        cwd => "/vagrant",
        require => [ Class['zendserver'], Class['::mysql::server'], File['/usr/local/zend/etc/conf.d/yaml.ini'], Exec['install yaml'] ]
    }

    apache::vhost { $::site_domain :
        docroot  => "/vagrant/public",
        ssl      => true,
        priority => '000',
        env_variables => [
            "APPLICATION_ENV $::environment"
        ],
        require => [ Package['apache'] ]
    }
    
    exec { "bootstrap-zs-server" :
        command => "/usr/local/zend/bin/zs-manage bootstrap-single-server --acceptEula TRUE -p 'password'; touch /var/local/zs-bootstrapped",
        cwd => "/usr/local/zend/bin/",
        require => [ Class['zendserver'] ],
        creates => "/var/local/zs-bootstrapped"
    }
    
    file { "/etc/profile.d/server_env.sh" :
        content => "export APPLICATION_ENV=$::environment",
        owner => root,
        group => root,
        mode => 755
    }
    
    # Disable the default (catch-all) vhost
    exec { "disable default virtual host from ${name}":
        command => "a2dissite default",
        onlyif  => "test -L ${apache::params::config_dir}/sites-enabled/000-default",
        notify  => Service['apache'],
        require => Package['apache'],
    }
}
