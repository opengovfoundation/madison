if hash compass 2>/dev/null; then
  compass compile --force
else
  /usr/local/rvm/gems/ruby-2.3.0@global/bin/compass compile --force
fi
