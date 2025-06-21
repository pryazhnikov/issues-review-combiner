.DEFAULT_GOAL := help

.PHONY: install
install:  ## Install project dependencies
	composer install

.PHONY: test
test:  ## Run tests
	./vendor/bin/phpunit

.PHONY: audit
audit:  ## Security audit of installed dependencies
	composer audit

.PHONY: help
help:  ## Show this help message
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'
