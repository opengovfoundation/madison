# -*- mode: ruby -*-
# vi: set ft=ruby :

# Usage: ENV=staging vagrant up

VAGRANTFILE_API_VERSION = "2"

require 'json'

localConf = JSON.parse(File.read('VagrantConfig.json'))

environment = "development"
if ENV["ENV"] && ENV["ENV"] != ''
    environment = ENV["ENV"].downcase
end

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
    config.vm.provision :shell, :path => "puppet_bootstrap.sh"
    
    if environment == 'development'
      config.vm.box = "precise64"
      config.vm.box_url = "http://files.vagrantup.com/precise64.box"
    
      config.vm.network :forwarded_port, guest: 80,    host: 10080    # apache http
      config.vm.network :forwarded_port, guest: 3306,  host: 3308  # mysql
      config.vm.network :forwarded_port, guest: 10081, host: 10081 # zend http
      config.vm.network :forwarded_port, guest: 10082, host: 10082 # zend https
    
      config.vm.network :private_network, ip: localConf['ipAddress']
    
        config.vm.provider :virtualbox do |vb, override|
            
            vb.gui = false
            vb.customize ["modifyvm", :id, "--memory", localConf['vmMemory']]
            vb.customize ["setextradata", :id, "VBoxInternal2/SharedFoldersEnableSymlinksCreate/vagrant-root", "1"]
            
            config.vm.synced_folder ".", "/vagrant", :group => "www-data", :mount_options => [ "dmode=775", "fmode=775" ]
        end
    
        config.vm.provision :puppet do |puppet|
            puppet.options        = "--verbose --debug"
            puppet.manifests_path = "puppet/manifests"
            puppet.module_path    = "puppet/modules"
            puppet.manifest_file  = "site.pp"
            puppet.facter         = {
                "vagrant"     => true,
                "environment" => environment,
                "site_domain" => localConf['siteDomain'],
                "role"        => "local",
                "awsAccessKey" => localConf['aws']['accessKey'],
                "awsSecretKey" => localConf['aws']['secretKey'],
                "php_version" => localConf['phpVersion']
            }
        end
    end
    
    if environment == 'ec2'
        config.vm.provision :shell, :path => "aws_bootstrap.sh"
        config.vm.box = "dummy"

        config.vm.provider :aws do |aws, override|
            aws.access_key_id     = localConf['aws']['accessKey']
            aws.secret_access_key = localConf['aws']['secretKey']
            aws.instance_type     = localConf['aws']['instanceType']
            aws.region            = localConf['aws']['region']
            aws.security_groups   = localConf['aws']['securityGroups']
            aws.tags              = {
                "environment" => environment,
                "elastic_ip"  => localConf['aws']['elasticIP'],
                "Name"        => localConf['aws']['name']
            }

            aws.region_config localConf['aws']['region'] do |region|
                region.ami          = localConf['aws']['ami']
                region.keypair_name = localConf['aws']['keyPair']
            end

            override.ssh.username         = "ubuntu"
            override.ssh.private_key_path = "~/.ssh/appdemos.pem"
        end

        config.vm.provision :puppet do |puppet|
            puppet.options        = "--verbose --debug"
            puppet.manifests_path = "puppet/manifests"
            puppet.module_path    = "puppet/modules"
            puppet.manifest_file  = "site.pp"
            puppet.facter         = {
                "site_domain" => localConf['siteDomain'],
                "environment" => environment,
                "aws_access_key" => localConf['aws']['accessKey'],
                "aws_secret_key" => localConf['aws']['secretKey'],
                "php_version" => localConf['phpVersion']
            }
        end
    end
end
