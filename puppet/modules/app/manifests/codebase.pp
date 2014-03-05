class app::codebase {

  info("Deploying Codebase for environment $environment")

  file { "/vagrant/public/.htaccess" :
     group => "www-data",
     owner => "root",
     mode => 775,
     source => "puppet:///modules/app/config/$::environment/public/.htaccess"
  }
  
  file { "/vagrant/app/storage/" : 
     group => "www-data",
     owner => "www-data",
     mode  => 775,
     recurse => true
  }
  
  composer::exec { 'update-codebase' :
    cmd => "update",
    cwd => "/vagrant",
    logoutput => true
  }
  
}
