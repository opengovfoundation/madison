defmodule Addict.AddictView do
  use Phoenix.HTML
  use Phoenix.View, root: "web/templates/"
  import Phoenix.Controller, only: [view_module: 1]
  import Madison.Router.Helpers
end
