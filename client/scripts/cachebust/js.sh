hashmark -c build -r -l 6 '*.js' '{dir}/{name}-{hash}{ext}' | sed -e 's/:"/:"/g' > ./build/build.json
replaceinfiles -s 'build/index.html' -d '{dir}/{base}' < ./build/build.json
tmp=`mktemp /tmp/index.html.XXXXXXXXXXXXX`
sed -e 's/\/css\//\/css\//g' build/index.html > $tmp
mv $tmp ./build/index.html
rm ./build/build.json
