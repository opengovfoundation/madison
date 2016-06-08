set :deploy_to, '/var/www/vhosts/production-test'

server 'madison-cap-test',
  user: 'deploy',
  roles: %w{app}
