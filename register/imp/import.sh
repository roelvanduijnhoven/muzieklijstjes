rm *.html -f
docker-compose exec mysql mysql -u root --password=password -e "SET FOREIGN_KEY_CHECKS = 0; SET GROUP_CONCAT_MAX_LEN=32768; SET @tables = NULL; SELECT GROUP_CONCAT(table_name) INTO @tables FROM information_schema.tables WHERE table_schema = (SELECT DATABASE()); SELECT IFNULL(@tables,'dummy') INTO @tables; SET @tables = CONCAT('DROP TABLE IF EXISTS ', @tables); PREPARE stmt FROM @tables; EXECUTE stmt; DEALLOCATE PREPARE stmt; SET FOREIGN_KEY_CHECKS = 1;" dev
cat ../../schema.sql | docker exec -i $(docker-compose ps -q mysql) mysql -u root --password=password dev # See https://github.com/docker/compose/issues/3352
docker-compose exec web php register/imp/imp.lijsten.php > output-lijstenB.html
docker-compose exec web php register/imp/imp.recensent.php > output-recensenten.html
docker-compose exec web php register/imp/imp.rubrieken.php > output-rubrieken.html
docker-compose exec web php register/imp/imp.algemeen.php > output-algemeen.html
docker-compose exec web php register/imp/imp.individueel.php > output-lijstenI.html
