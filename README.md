# IPBActivityCrawler

Description
---

This PHP script scans the activity page of a IP Board 4 platform and creates a database with the contents to be displayed in another project.

It simply does the hard part for you.

It includes an API which you can access at `GET server.dev/index.php?lastId=0`.

Just pass in the lastId GET parameter to retrieve the latest 20 rows in the database in JSON format.

Instructions
---

```
$ git clone https://github.com/RomanianSecurityTeam/IPBActivityCrawler.git
$ cd IPBActivityCrawler
$ composer install
$ nano .env
```

Inside the .env file, add the following variables and fill them.

```
DB_HOST=localhost
DB_NAME=rst_activity_crawler
DB_USER=root
DB_PASS=neverguess
```

First time installation
---

The first time you install this, you need to run the `includes/install.php` script to create the database schema.

```
$ php includes/install.php
```

You can remove it after the installation is done, although it's not going to do you any harm if you keep it.

Running
---

The way I recommend you run it is as a cron task, but you can run it however you want.

```
$ crontab -e
```

Add a new task which triggers every minute:

```
* * * * * /path/to/RSTActivityCrawler/index.php?run > /dev/null
```

Updates
---

If you haven't made any changes to the tracked files, run:

```
$ git pull
$ composer update
```

If you have made changes, you need to re-do all the steps from the Instructions section below, then re-apply your changes. ALternatively, you can stash your changes, pull then apply the stashde changes and remove any conflicts you might encounter.

```
$ git stash
$ git pull
$ git stash apply
```

Enjoy!

Authors
---
Gecko (http://degecko.com)