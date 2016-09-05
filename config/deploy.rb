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

set :linked_files, fetch(:linked_files, [])
  .push(
    'server/.env'
  )

set :linked_dirs, fetch(:linked_dirs, [])
  .push(
    'server/storage/framework/cache',
    'server/storage/logs',
    'server/storage/db_backups',
    'client/app/locales/custom',
    'client/app/sass/custom'
  )

# Default value for default_env is {}
set :default_env, {
  path: "/usr/local/rvm/gems/ruby-2.3.0/bin:/usr/local/rvm/gems/ruby-2.3.0@global/bin:/usr/local/rvm/rubies/ruby-2.3.0/bin:$PATH"
}

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
      execute "if [[ ! -d #{shared_path}/server/storage/framework ]]; then chmod -R 775 #{shared_path}/server/storage; fi"
      execute "if [[ ! -d #{shared_path}/server/storage/framework ]]; then touch #{shared_path}/server/storage/laravel.log; fi"
    end
  end

end

namespace :db do
  task :seed do
    on roles(:all) do |host|
      info "Running database seeders on #{host}"
      within release_path do
        execute :make, 'db-force-seed'
      end
    end
  end

  task :migrate do
    set(:continue, ask('if you would like to continue (this action is irreversible!)', 'Y/N', echo: false))
    exit if fetch(:continue) != 'Y'
    on roles(:all) do |host|
      info "Running database migrations on #{host}"
      within release_path do
        execute :make, 'db-force-migrate'
      end
    end
  end

  namespace :backup do

    # Creates a backup of the remote database in `server/storage/db_backups`
    task :create do
      on roles(:all) do |host|
        info 'Creating a database backup in app_path/shared/server/storage/db_backups/'
        within current_path do
          execute :make, "db-backup"
        end
      end
    end

    # Fetches the latest database backup file from the remote instance
    task :fetch do
      on roles(:all) do |host|
        create_output = capture "cd #{current_path} && make db-backup"
        file_name = /Backup created:.+db_backups\/(.+)$/.match(create_output)[1]
        folder = "#{shared_path}/server/storage/db_backups"
        download! "#{folder}/#{file_name}", "./#{host.hostname}_#{file_name}"
      end
    end

  end

  task :restore do
    # take input for file name
    raise 'Must provide a file to restore from.' if !ENV['file']
    raise 'No such file.' if !File.exists? ENV['file']

    set(:continue, ask('if you would like to continue (this action is irreversible!)', 'Y/N', echo: false))
    exit if fetch(:continue) != 'Y'

    file_path = ENV['file']
    file_name = Pathname.new(file_path).basename

    on roles(:all) do |host|
      # scp that file to the host in tmp
      upload! file_path, "/tmp/#{file_name}"
      # run the artisan command remotely, passing in filename
      within current_path do
        execute :make, "db-restore file=/tmp/#{file_name}"
      end
    end
  end
end

namespace :client do
  task :rebuild do
    set(:continue, ask('if you would like to continue (this action is irreversible!)', 'Y/N', echo: false))
    exit if fetch(:continue) != 'Y'

    on roles(:all) do |host|
      within current_path do
        execute :make, "build-client"
      end
    end
  end
end
