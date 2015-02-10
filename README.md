# checkmyws-php

Installation
------------

### Install source from GitHub
To install the source code:

    $ git clone git@github.com:checkmyws/checkmyws-php.git

Init submodules:

    $ git submodule init
    $ git submodule update
    
And include it in your scripts:

    require_once '/path/to/checkmyws-php/library/Client.php';

Testing
-------

To run the test suite, run simply:

    $ cd tests
    $ phpunit
