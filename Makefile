# Common makefile commands & variables between projects
include .make/common.mk

## Not defined? Use default repo name which is the application
ifeq ($(REPO_NAME),)
	REPO_NAME="wordpress-plugin"
endif

## Not defined? Use default repo owner
ifeq ($(REPO_OWNER),)
	REPO_OWNER="tonicpow"
endif

.PHONY: clean

all: ## Runs multiple commands
	#@$(MAKE) test

clean: ## Remove previous builds and any test cache data
	@test $(DISTRIBUTIONS_DIR)
	@if [ -d $(DISTRIBUTIONS_DIR) ]; then rm -r $(DISTRIBUTIONS_DIR); fi