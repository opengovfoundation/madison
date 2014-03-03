
class ec2 {

    file { "/tmp/ec2-api-tools.zip" :
        source => "puppet:///modules/ec2/ec2-api-tools.zip",
        owner => root,
        group => root,
        mode => 775
   }
   
   package { "default-jre":
       ensure => present
   }
   
   exec { "ec2-api-tools-unzip" :
       command => "/usr/bin/unzip /tmp/ec2-api-tools.zip -d /usr/local",
       creates => "/usr/local/ec2-api-tools-1.6.12.0",
       require => [ File['/tmp/ec2-api-tools.zip'], Package['unzip'], Package['default-jre'] ]
   }

   file { "/usr/local/ec2-api-tools" :
       target => "/usr/local/ec2-api-tools-1.6.12.0",
       ensure => 'link',
       require => [ Exec['ec2-api-tools-unzip'] ]
   }
   
       file { "/etc/profile.d/java_env.sh" :
        content => "export JAVA_BIN=`readlink /etc/alternatives/java`\nexport JAVA_HOME=\"\${JAVA_BIN/\\/bin\\/java/}\"",
        owner => root,
        group => root,
        mode => 755,
        require => [ Package['default-jre'] ]
    }
    
    file { "/etc/profile.d/ec2_env.sh" :
        content => "export EC2_INSTANCE_ID=`curl -s http://169.254.169.254/latest/meta-data/instance-id`\nexport EC2_HOME=/usr/local/ec2-api-tools\nexport PATH=\$PATH:\$EC2_HOME/bin",
        owner => root,
        group => root,
        mode => 755,
        require => [ Exec['ec2-api-tools-unzip'], File['/usr/local/ec2-api-tools'] ]
    }
    
    file { "/usr/local/bin/aws_assign_ip.sh" : 
        content => template('ec2/aws_assign_ip.erb'),
        owner => root,
        group => root,
        mode => 744,
        require => [ Exec['ec2-api-tools-unzip'], File['/usr/local/ec2-api-tools'] ]
    }
         
    exec { 'Assign Elastic IP' :
        command => "/bin/bash /usr/local/bin/aws_assign_ip.sh",
        require => [ File['/usr/local/bin/aws_assign_ip.sh'] ]
    }
}
