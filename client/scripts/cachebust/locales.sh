dt=1462415480
tmp=`mktemp /tmp/app.min.js.XXXXXXXXXX`
mv build/locales build/locales-$dt
sed -e "s/\/locales\//\/locales-$dt\//g" ./build/app.min.js > $tmp
mv $tmp ./build/app.min.js
