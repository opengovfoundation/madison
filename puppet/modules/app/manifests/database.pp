class app::database {

  class { '::mysql::server' :
     root_password => 'password',
     databases => {
          'madison_database' => {
              ensure => 'present',
              charset => 'utf8'
          }
     }
  }
   
}
