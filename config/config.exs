# This file is responsible for configuring your application
# and its dependencies with the aid of the Mix.Config module.
#
# This configuration file is loaded before any dependency and
# is restricted to this project.
use Mix.Config

# General application configuration
config :madison,
  ecto_repos: [Madison.Repo]

# Configures the endpoint
config :madison, Madison.Endpoint,
  url: [host: "localhost"],
  secret_key_base: "o7r3IMRSeyrinjB+6DI3Qv44iSYuWXYMy0J8Q4YxvFe3d83mNZaF6ymu7wOFwkT0",
  render_errors: [view: Madison.ErrorView, accepts: ~w(html json)],
  pubsub: [name: Madison.PubSub,
           adapter: Phoenix.PubSub.PG2]

# Configures Elixir's Logger
config :logger, :console,
  format: "$time $metadata[$level] $message\n",
  metadata: [:request_id]

# Import environment specific config. This must remain at the bottom
# of this file so it overrides the configuration defined above.
import_config "#{Mix.env}.exs"
