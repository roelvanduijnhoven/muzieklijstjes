# Step 1: clear database and import `./scheme.sql`

rm *.html -f
docker-compose run web php register/imp/imp.lijsten.php > output-lijstenB.html
docker-compose run web php register/imp/imp.recensent.php > output-recensenten.html
docker-compose run web php register/imp/imp.rubrieken.php > output-rubrieken.html
docker-compose run web php register/imp/imp.algemeen.php > output-algemeen.html
docker-compose run web php register/imp/imp.individueel.php > output-lijstenI.html
