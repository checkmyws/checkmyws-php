checkmyws-php
================

[Check my Website](https://checkmy.ws) client for PHP

    php -a
    php > require_once('library/Client.php');
    php > $client = new CheckmywsClient();
    php > $data = $client->status('3887e18a-28d6-4eac-9eb0-c6d9075e4c7e');
    php > var_dump($data);

Installation
------------

### Install source from GitHub
To install the source code:

    $ git clone git@github.com:checkmyws/checkmyws-php.git

Init submodules:

    $ cd checkmyws-php
    $ git submodule init
    $ git submodule update
    
And include it in your scripts:

    require_once('/path/to/checkmyws-php/library/Client.php');

Testing
-------

To run the test suite, run simply:

    $ cd tests
    $ phpunit
