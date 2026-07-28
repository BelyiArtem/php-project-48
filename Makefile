install:
	composer install
validate:
	composer validate
test-coverage:
	composer exec --verbose phpunit tests
gendiff:
	./bin/gendiff
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src