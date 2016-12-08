defmodule Madison.PageController do
  use Madison.Web, :controller

  def index(conn, _params) do
    render conn, "index.html"
  end
end
