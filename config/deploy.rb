
set :application, 'madison'
set :repo_url, 'git@github.com:opengovfoundation/madison.git'

# Explicitly ask for which branch to deploy from
ask :branch, `git rev-parse --abbrev-ref HEAD`.chomp

set :scm, :git

set :keep_releases, 5

set :format, :airbrussh

set :format_options,
  command_output: true,
  log_file: 'log/capistrano.log',
  color: :auto,
  truncate: :auto

# Default value for :pty is false
# set :pty, true

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

namespace :deploy do

  after :published, :install_dependencies do
    on roles(:all) do |host|
      info 'Running `make deps` to install dependencies'
      within release_path do
        execute :make, :deps
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

end
