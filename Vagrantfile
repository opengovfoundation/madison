# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure("2") do |config|
  # The most common configuration options are documented and commented below.
  # For a complete reference, please see the online documentation at
  # https://docs.vagrantup.com.

  # Every Vagrant development environment requires a box. You can search for
  # boxes at https://atlas.hashicorp.com/search.
  config.vm.box = "ubuntu/xenial64"

  # Disable automatic box update checking. If you disable this, then
  # boxes will only be checked for updates when the user runs
  # `vagrant box outdated`. This is not recommended.
  # config.vm.box_check_update = false

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine. In the example below,
  # accessing "localhost:8080" will access port 80 on the guest machine.
  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.network "forwarded_port", guest: 81, host: 8081

  # Create a private network, which allows host-only access to the machine
  # using a specific IP.
  # config.vm.network "private_network", ip: "192.168.33.10"

  # Create a public network, which generally matched to bridged network.
  # Bridged networks make the machine appear as another physical device on
  # your network.
  # config.vm.network "public_network"

  # Share an additional folder to the guest VM. The first argument is
  # the path on the host to the actual folder. The second argument is
  # the path on the guest to mount the folder. And the optional third
  # argument is a set of non-required options.
  config.vm.synced_folder "./", "/vagrant", group: "www-data", mount_options:["dmode=775,fmode=664"]

  # Provider-specific configuration so you can fine-tune various
  # backing providers for Vagrant. These expose provider-specific options.
  # Example for VirtualBox:
  #
  # config.vm.provider "virtualbox" do |vb|
  #   # Display the VirtualBox GUI when booting the machine
  #   vb.gui = true
  #
  #   # Customize the amount of memory on the VM:
  #   vb.memory = "1024"
  # end

  config.vm.provision "shell", inline: <<-SHELL
    apt-get update
    apt-get install -y nginx make mariadb-server php php-curl php-fpm php-mbstring php-mcrypt php-mysql

    # Dev tools
    apt-get install -y composer nodejs-legacy npm ruby php-xml

    cd /vagrant
    make

    # install nginx config
    cp -f docs/nginx.conf.example /etc/nginx/sites-available/madison
    sed -ri "s|/path/to/madison/project|/vagrant|" /etc/nginx/sites-available/madison
    ln -srf /etc/nginx/sites-available/madison /etc/nginx/sites-enabled/madison
    rm -f /etc/nginx/sites-enabled/default
    systemctl reload nginx

    # Setup db
    # Allow root database user access without sudo, this is not a production configuration
    mysql -u root -e "UPDATE mysql.user SET plugin = 'mysql_native_password' WHERE user = 'root' AND plugin = 'unix_socket'"
    mysql -u root -e "FLUSH PRIVILEGES"
    # Create initial database
    mysql -u root -e "CREATE DATABASE madison"
    systemctl restart mysql
    # Initialize database
    make db-reset

    # Setup queue listener
    cat <<EOF > /etc/systemd/system/madison-queue.service
[Unit]
Description=Run Madison queue listener

[Service]
ExecStart=/usr/bin/php /vagrant/server/artisan queue:listen --sleep=3 --tries=3
Restart=on-failure

[Install]
WantedBy=multi-user.target
EOF
    systemctl enable madison-queue
    systemctl start madison-queue
  SHELL
end
