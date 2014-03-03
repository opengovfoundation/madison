# Class: zendserver::params
#
# This class defines default parameters used by the main module class zendserver
# Operating Systems differences in names and paths are addressed here
#
# == Variables
#
# Refer to zendserver class for the variables defined here.
#
# == Usage
#
# This class is not intended to be used directly.
# It may be imported or inherited by other classes
#
class zendserver::params inherits apache::params {
    ### Application specific parameters
    $use_ce            = true
    $php_version       = '5.3'
    $install_extra_ext = true

    ### Application related parameters
    $service = $::operatingsystem ? {
      /(?i:Ubuntu|Debian|Mint)/ => 'zend-server',
      /(?i:SLES|OpenSuSE)/      => 'zend-server',
      default                   => 'zend-server',
    }

    $install_dir = $::operatingsystem ? {
      /(?i:Ubuntu|Debian|Mint)/ => '/usr/local/zend',
      /(?i:SLES|OpenSuSE)/      => '/usr/local/zend',
      default                   => '/usr/local/zend',
    }

    $bin_dir      = "${install_dir}/bin"
    $config_dir   = "${install_dir}/etc"
    $ext_conf_dir = "${install_dir}/etc/conf.d"
}
