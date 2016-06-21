set :stage, :production
set :branch, :master # Which branch you want to deploy from

server '1.2.3.4', # Server IP address / hostname
  user: 'deploy', # Server user to deploy as
  roles: %w{app}

set :deploy_to, '/var/www/vhosts/madison' # Folder site will deploy to
