# Inertia server-side adapter for CodeIgniter 4

Forked from https://github.com/amiranagram/inertia-codeigniter-4

![Tests](https://github.com/amiranagram/inertia-codeigniter-4/workflows/Tests/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/amirami/inertia-codeigniter-4.svg)](https://packagist.org/packages/amirami/inertia-codeigniter-4)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/amirami/inertia-codeigniter-4.svg)](https://packagist.org/packages/amirami/inertia-codeigniter-4)

## Installation

You can install the [original package](https://github.com/amiranagram/inertia-codeigniter-4) via composer:

```bash
composer require amirami/inertia-codeigniter-4
```

Or install its as **ThirdParty**:

```bash
cd app/ThirdParty

git clone https://github.com/trianayulianto/inertia-codeigniter-4.git
```

Set autoload in `app/Config/Autoload.php`

```php
public $psr4 = [
    // others
    'Inertia'     => APPPATH . 'ThirdParty/inertia-codeigniter-4/src'
];
```

## Usage

### Root template
- Make root view named `app.php`
```php
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>CI4-Inertia</title>

    <!-- Styles -->
    <link rel="stylesheet" href="<?php echo base_url('css/app.css') ?>">

    <!-- Scripts -->
    <script src="<?php echo base_url('js/app.js') ?>" defer></script>
</head>
<body>
	<?= inertia()->app($page) ?>
</body>
</html>
```

### Filter

- Make new filter
```bash
php spark make:filter HandleInertiaRequests
```
- Add code same as below
```php
<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware implements FilterInterface
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @param  \CodeIgniter\HTTP\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @param  \CodeIgniter\HTTP\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), []);
    }
}
```

### Creating responses
That's it, you're all ready to go server-side! From here you can start creating Inertia responses.
```php
use Inertia\Inertia;

class EventsController extends Controller
{
    public function show($id)
    {
        $event = Event::find($id);

        return Inertia::render('Event/Show', [
            'event' => $event,
        ]);
    }
}
```

### More
Visit [inertiajs.com](https://inertiajs.com/) to learn more.

## Testing

``` bash
composer test
```

## Roadmap

### Tests

* Controller test
* Helper test. `inertia()` helper.
* Inertia headers test. An Inertia request should return an Inertia response (JSON response).
* Shared data test. It should be accesses in any response.
* Lazy props test.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Amir Rami](https://github.com/amirami)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
