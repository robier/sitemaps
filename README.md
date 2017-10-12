Sitemaps
--------

PHP implementation of [sitemaps.org](https://www.sitemaps.org) protocol. This library was build
using PHP Generators having in mind using as less memory as possible. 


Supporting sitemap formats:

 - XML sitemaps
 - Text sitemaps
 
Library is also supporting GZip compression of generated sitemaps.

### Usage

```php

use Robier\Sitemaps\DataProvider;

class Data implements DataProvider
{
    public function fetch(): \Iterator
    {
        for($i = 1; $i <= 500000; $i++){
            yield new Location('http://example.com/site-' . $i);
        }
    }
}
```

```php

use Robier\Sitemaps\Driver\XML;
use Robier\Sitemaps\Generator;
use Robier\Sitemaps\Processor\GZip;

$writer = new XML('/tmp/', 'http://example.com/');
$generator = new Generator($writer);
$generator->data('sitemap', new Data());

// gzip enable
$generator->processor(new GZip());

foreach($generator as $item){
    // 
    // $item is instance of File/Contract
}
```


### Installation
 
```bash
composer require robier/sitemaps
```

### Docker

For development you can use docker:

```bash
tests/docker/build
tests/docker/run <command that will be send to docker contener>
tests/docker/enter
tests/docker/run xdebug <php script>
```

### Todo

- Implement Atom/RSS format
- write tests
- add better documentation
