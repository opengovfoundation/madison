npm run cachebust:locales
npm run cachebust:templates
npm run cachebust:css
npm run cachebust:js

# reaplceinfiles messes up permissions, this fixes it
chmod 644 ./build/index.html
chmod 644 ./build/app.min-*.js
