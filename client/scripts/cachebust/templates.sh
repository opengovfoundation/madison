hashmark -c build -r -l 6 'templates/**/*.*' '{dir}/{name}-{hash}{ext}' > ./build/build.json
replaceinfiles -s './build/templates/**/*' -d '{dir}/{base}' < ./build/build.json
replaceinfiles -s './build/**/*.js' -d '{dir}/{base}' < ./build/build.json
replaceinfiles -s './build/index.html' -d '{dir}/{base}' < ./build/build.json
rm ./build/build.json
