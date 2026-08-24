install:
	composer install
	patch -d vendor/funct/funct -p1 < patches/funct-php84.patch
validate:
	composer validate
test:
	composer exec --verbose phpunit tests
test-coverage:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-clover=build/logs/clover.xml
test-coverage-text:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-text
demo-stylish:
	./bin/gendiff tests/fixtures/json/file1.json tests/fixtures/json/file2.json
demo-plain:
	./bin/gendiff --format plain tests/fixtures/json/file1.json tests/fixtures/json/file2.json
demo-yaml:
	./bin/gendiff tests/fixtures/yml/file1.yml tests/fixtures/yml/file2.yml
demo-json:
	./bin/gendiff --format json tests/fixtures/json/file1.json tests/fixtures/json/file2.json
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src