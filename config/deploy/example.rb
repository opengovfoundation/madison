set :branch, :master # Which branch you want to deploy from

server '1.2.3.4', # Server IP address / hostname
  user: 'deploy', # Server user to deploy as
  roles: %w{app}

# If you're using chef to provision, this should match up with the "id"
# attribute in your `data_bages/sites` entry.
set :deploy_to, '/var/www/vhosts/madison' # Folder site will deploy to
