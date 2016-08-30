require 'sass-globbing'

# Require any additional compass plugins here.
add_import_path "node_modules/breakpoint-sass/stylesheets"
add_import_path "node_modules/bootstrap-sass/assets/stylesheets"
add_import_path "node_modules/font-awesome/scss"

# Set this to the root of your project when deployed:
http_path = "/app"
css_dir = "app/css"
sass_dir = "app/sass"
images_dir = "app/img"
javascripts_dir = "app/js"

# You can select your preferred output style here (can be overridden via the command line):
# output_style = :expanded or :nested or :compact or :compressed

# To enable relative paths to assets via compass helper functions. Uncomment:
# relative_assets = true

# To disable debugging comments that display the original location of your selectors. Uncomment:
line_comments = false


# If you prefer the indented syntax, you might want to regenerate this
# project again passing --syntax sass, or you can uncomment this:
# preferred_syntax = :sass
# and then run:
# sass-convert -R --from scss --to sass sass scss && rm -rf sass && mv scss sass
