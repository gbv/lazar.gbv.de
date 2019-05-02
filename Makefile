.PHONY: test style web

# run unit tests
test:
	composer test
	cd xslt && ./runtest

# fix coding-style
style:
	composer style

# start build-in webserver 
web:
	php -S localhost:8008 -t public
