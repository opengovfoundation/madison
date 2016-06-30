require 'rvm/capistrano'

set :application, 'madison'
set :repo_url, 'git@github.com:opengovfoundation/madison.git'

# Explicitly ask for which branch to deploy from
ask :branch, `git rev-parse --abbrev-ref HEAD`.chomp

set :scm, :git
set :rvm1_ruby_version, '2.3.0'

set :keep_releases, 5

set :format, :airbrussh

set :format_options,
  command_output: true,
  log_file: 'log/capistrano.log',
  color: :auto,
  truncate: :auto

# Default value for :pty is false
set :pty, true

set :linked_files, fetch(:linked_files, [])
  .push(
    'server/.env'
  )

set :linked_dirs, fetch(:linked_dirs, [])
  .push(
    'server/storage/logs',
    'client/app/locales/custom',
    'client/app/sass/custom'
  )

# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

before 'deploy', 'rvm1:install:rvm'
before 'deploy', 'rvm1:install:ruby'
before 'deploy', 'rvm1:install:gems'

namespace :deploy do

  after :published, :install_dependencies do
    on roles(:all) do |host|
      info 'Running `make deps-production` to install dependencies'
      within release_path do
        execute :make, 'deps-production'
      end
    end
  end

  after :published, :build_client do
    on roles(:all) do |host|
      info 'Running `make build-client` to build client side assets'
      within release_path do
        execute :make, 'build-client'
      end
    end
  end

  after :published, :migrate_database do
    on roles(:all) do |host|
      info 'Running `make db-migrate` to run database migrations'
      within release_path do
        execute :make, 'db-force-migrate'
      end
    end
  end

  after :published, :set_folder_permissions do
    on roles(:all) do |host|
      info 'Ensuring current permissions on shared folders'
      execute "chmod -R 775 #{shared_path}/server/storage"
      execute "touch #{shared_path}/server/storage/laravel.log"
    end
  end

end

task :seed do
  on roles(:all) do |host|
    info "Running database seeders on #{host}"
    within release_path do
      execute :make, 'db-force-seed'
    end
  end
end
