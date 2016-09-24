see-my-stats
============

This project store stats of people. You can then see your stats or manage them as admin displaying some nice dashboards.

Getting started
---------------


1. Cloning the project
```bash
$ git clone git@github.com:julien-gm/see-my-stats.git
```
2. Installing the assets
```
$ composer install
```
3. Initializing the project
```
$ php bin/console --env prod doctrine:database:create
$ php bin/console --env prod doctrine:schema:create
$ php bin/console --env prod stats:create-admin admin_name
```

4. Starting the server
```
$ php bin/console server:start 192.168.0.1:8080
```

You can now go to http://192.168.0.1:8080/login
