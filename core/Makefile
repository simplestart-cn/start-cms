# Generic Makefile for building and packaging a Nextcloud app which uses npm and Composer.
#
# Dependencies:
# * make
# * which
# * curl: used if phpunit and composer are not installed to fetch them from the web
# * tar: for building the archive
# * npm: for building and testing everything JS
#
# The npm command by launches the npm build script:
#
#    npm run build
#
# The npm test command launches the npm test script:
#
#    npm run test
#

app_name=$(notdir $(CURDIR))
build_directory=$(CURDIR)/build
appstore_build_directory=$(CURDIR)/build/$(app_name)
appstore_artifact_directory=$(CURDIR)/build/artifacts
appstore_package_name=$(appstore_artifact_directory)/$(app_name)
npm=$(shell which npm 2> /dev/null)
gcp=$(shell which gcp 2> /dev/null)

ifeq (, $(gcp))
	copy_command=cp
else
	copy_command=gcp
endif


.PHONY: all
all: dev-setup build-svg-sprite build-js-production

# cleanup and generate a clean developement setup
dev-setup: clean clean-dev npm-init

npm-init:
	npm install --registry https://registry.npm.taobao.org

npm-update:
	npm update --registry https://registry.npm.taobao.org

# Runs the development build
build-js:
	npm run dev

# Runs the production build
build-js-production:
	npm run build

# Runs the development build and keep watching
watch-js:
	npm run watch

# Removes the build directory and the compiled files
.PHONY: clean
clean:
	rm -rf $(build_directory)

# Same as clean but also removes dependencies installed by npm
.PHONY: clean-dev
clean-dev:
	rm -rf node_modules

# Builds the source package for the app store
.PHONY: appstore
appstore: clean build-js-production
	mkdir -p $(appstore_build_directory) $(appstore_artifact_directory)
	rsync -av .	$(appstore_build_directory) \
	--exclude=/.git \
	--exclude=/.babelrc \
	--exclude=/.babelrc.js \
	--exclude=/.browserslistrc \
	--exclude=/.codecov.yml \
	--exclude=/.editorconfig \
	--exclude=/.eslintrc \
	--exclude=/.eslintrc.js \
	--exclude=/.eslintignore \
	--exclude=/.gitattributes \
	--exclude=/.gitignore \
	--exclude=/.phpunit.result.cache \
	--exclude=/.gitlab-ci.yml \
	--exclude=/.prettierrc.js \
	--exclude=/.scrutinizer.yml \
	--exclude=/.stylelintrc \
	--exclude=/.travis.yml \
	--exclude=/.env.development \
	--exclude=/.env.production \
	--exclude=/yarn.lock \
	--exclude=/.tx \
	--exclude=/.v8flags*.json \
	--exclude=/build.xml \
	--exclude=/CONTRIBUTING.md \
	--exclude=/issue_template.md \
	--exclude=/gulpfile.js \
	--exclude=/Makefile \
	--exclude=/package.json \
	--exclude=/package-lock.json \
	--exclude=/phpunit.xml \
	--exclude=/phpunit.integration.xml \
	--exclude=/README.md \
	--exclude=/vue.config.js \
	--exclude=/babel.config.js \
	--exclude=/webpack.config.js \
	--exclude=/webpack.prod.js \
	--exclude=/webpack.dev.js \
	--exclude=/build \
	--exclude=/coverage \
	--exclude=/src \
	--exclude=/node_modules \
	--exclude=/apidoc.md \
	--exclude=/apidoc.php \
	--exclude=/apidoc.json \
	--exclude=/test \
	--exclude=/tests
	cd $(appstore_build_directory)/../; \
	zip -r $(appstore_package_name).zip $(app_name)
	tar -czf $(appstore_package_name).tar.gz -C $(appstore_build_directory)/../ $(app_name)

# Command for running VUE tests
.PHONY: test
test:
	$(npm) run test

test-php:
	phpunit -c phpunit.xml
	phpunit -c phpunit.integration.xml

test-php-coverage:
	phpunit -c phpunit.xml --coverage-clover=coverage-unit.xml
	phpunit -c phpunit.integration.xml --coverage-clover=coverage-integration.xml
