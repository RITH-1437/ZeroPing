# Installation

ZeroPing requires **PHP 8.1+** (tested on PHP 8.5). No Composer dependencies
are needed to run the framework core — only PHPUnit is required for tests.

## Install

```bash
composer create-project rith-1437/zeroping my-app
cd my-app
```

## Run the development server

```bash
php -S localhost:8000 router.php
```

Then open `http://localhost:8000/` to see the welcome page, or
`http://localhost:8000/docs/introduction` for the documentation viewer.
