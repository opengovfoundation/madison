#!/usr/bin/env sh

cat node_modules/jquery/dist/jquery.js \
  node_modules/select2/select2.js \
  node_modules/bootstrap-sass/assets/javascripts/bootstrap.js \
  node_modules/pagedown/Markdown.Converter.js \
  node_modules/pagedown/Markdown.Sanitizer.js \
  node_modules/pagedown-editor/Markdown.Editor.js \
  node_modules/underscore/underscore.js \
  node_modules/moment/moment.js \
  node_modules/autolinker/dist/Autolinker.js \
  > build/components.js
