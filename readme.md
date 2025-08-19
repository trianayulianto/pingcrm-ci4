# Ping CRM

A demo application to illustrate how Inertia.js works.

![](https://raw.githubusercontent.com/trianayulianto/pingcrm-ci4/master/screenshot.png)

## Installation

Clone the repo locally:

```sh
git clone https://github.com/trianayulianto/pingcrm-ci4.git pingcrm-ci4
cd pingcrm-ci4
```

Install PHP dependencies:

```sh
composer install
```

Install NPM dependencies:

```sh
npm ci
```

Build assets:

```sh
npm run build
```

Run the migrations & seeders:

```sh
php spark migrate
php spark db:seed DatabaseSeeder
```

Run the dev server (the output will give the address):

```sh
php spark serve
```

You're ready to go! Visit Ping CRM in your browser, and login with:

- **Username:** johndoe@example.com
- **Password:** secret

## Running tests

No testing yet
