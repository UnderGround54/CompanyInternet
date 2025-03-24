# Run this app

### Create the database
```
php bin/console d:d:c
```

### Migrate the database
```
php bin/console d:m:m
```

### Create keypair JWT
```
php bin/console lexik:jwt:generate-keypair
```

### adds default data for the app
```
make app-set-default-data
```

### Access the app
```
symfony serve -d
```
mail: admin.orange@yoopmail.com
password: 123456

## Commandes disponibles for this app
- Pour plus de détails sur les commandes, exécuter `make help`.

Les commandes suivantes sont disponibles :
- `make app-set-admin-data` : ajoute les données de base pour l'application, Admin, Company