# Sway ExPage
Emulates behaviour of SwayExPage.

This library allows you to show beautiful errors pages against PHP error page.


##Features
* Hides errors for normal users
* Beautiful formatting for cli output
* Customized errors pages
* Logging to files 
* Error output show control

##Installation
```
composer require rev/sway-expage
```

This will install this package at newest version. Composer package manager
is required. 

##Usage
It is very simple:
```php
use Rev\ExPage;

$exPage = new ExPage\Manager(true,[
    'dirname' => __DIR__,
    'filelog' => 'default.log',
    'template' => 'default',
    'separate' => [
        'errors' => 'errors.log',
        'exceptions' => 'exceptions.log'
    ],
    'cli_view' => [
        'error' => [
            'show_file' => true,
            'show_line' => true,
            'show_scope' => true
        ],
        'exception' => [
            'show_file' => true,
            'show_line' => true,
            'show_trace' => true
        ]
    ]
]);
```

Parameter *dirname* points to directory where log files should be stored.
If you want to separate files log to exception logs and errors logs define
look into parameter *separate*. 

