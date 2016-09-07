custom_icon=app/favicon.ico
default_icon=app/favicon_default.ico

if [ -f "$custom_icon" ] && [ -s "$custom_icon" ]
then
  cp $custom_icon build/favicon.ico
else
  cp $default_icon build/favicon.ico
fi
