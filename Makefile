#variables
.PHONY: help app-set-admin-data
.DEFAULT_GOAL= help
PHP = php
BIN_CONSOLE = $(PHP) bin/console

##add default admin for app
app-set-admin-data:
	$(BIN_CONSOLE) app:set-admin-data 'App\Entity\Company' '/DataFixtures/Fixtures/company.yml' --truncate-only
	$(BIN_CONSOLE) app:set-admin-data 'App\Entity\User' '/DataFixtures/Fixtures/user_admin.yml'
	$(BIN_CONSOLE) app:set-admin-data 'App\Entity\Company' '/DataFixtures/Fixtures/company.yml'


# show help message
help:
	@echo "Liste des commande disponibles :"
	@awk '/^##/{c=$$0; getline; printf "%-30s %s\n", c, $$0 }' $(MAKEFILE_LIST) | sort -d