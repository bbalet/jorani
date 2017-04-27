test: install
	php --version
	vendor/bin/phpunit --no-coverage

coverage: install
	phpdbg --version
	phpdbg -qrr vendor/bin/phpunit

open-coverage:
	open coverage/index.html

lint: test/bin/php-cs-fixer
	test/bin/php-cs-fixer fix --using-cache no

install:
	PHONY_MAKEFILE_INSTALL=1 scripts/composer-install

edge-cases: install
	php --version
	vendor/bin/phpunit --no-coverage test/suite-edge-cases

integration: install
	test/integration/run-all

output-examples: install
	scripts/output-examples

doc-img: install
	scripts/build-doc-img

web: install $(shell find doc assets/web test/fixture/verification)
	make doc-img
	scripts/build-web

open-web:
	open http://localhost:8000/

serve: web
	php -S 0.0.0.0:8000 -t web

publish: web
	@scripts/publish-web

test-fixtures:
	scripts/build-test-fixtures

.PHONY: test coverage open-coverage lint install edge-cases integration output-examples doc-img open-web serve publish test-fixtures

test/bin/php-cs-fixer:
	curl -sSL http://cs.sensiolabs.org/download/php-cs-fixer-v2.phar -o test/bin/php-cs-fixer
	chmod +x test/bin/php-cs-fixer
