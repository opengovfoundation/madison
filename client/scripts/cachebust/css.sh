hashmark -c build -r -l 6 '/css/*.css' '{dir}/{name}-{hash}{ext}' | sed -e 's/"css\//"/g' > ./build/build.json
replaceinfiles -s 'build/**/*.{js,css,html}' -d '{dir}/{base}' < ./build/build.json
replaceinfiles -s './build/index.html' -d '{dir}/{base}' < ./build/build.json
rm ./build/build.json
