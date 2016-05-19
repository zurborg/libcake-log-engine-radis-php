Radis Logging Engine for CakePHP
================================

Radis is a graylog radio using a Redis DB.

```php
use \Cake\Log\Log;

Log::config('default', [
    'engine' => 'Radis',
    'server' => 'foo:123',
    'queue' => 'test',
]);

Log::info('foobar');
```

## Documentation

Please go to the [API Index](docs/ApiIndex.md).

