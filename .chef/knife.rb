cookbook_path    ["config/chef/cookbooks", "config/chef/site-cookbooks"]
node_path        "config/chef/nodes"
role_path        "config/chef/roles"
environment_path "config/chef/environments"
data_bag_path    "config/chef/data_bags"
#encrypted_data_bag_secret "data_bag_key"

knife[:berkshelf_path] = "config/chef/cookbooks"
Chef::Config[:ssl_verify_mode] = :verify_peer if defined? ::Chef
