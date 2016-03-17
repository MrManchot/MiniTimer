# MiniTimer
Simplest way to profile execution time in PHP

```php
MiniTimer::inst()->start('Timer name');
do_something();
MiniTimer::inst()->stop('Timer name');

MiniTimer::inst()->addPoint();
do_something();
MiniTimer::inst()->addPoint();
do_something();
MiniTimer::inst()->addPoint();

# Display result
MiniTimer::inst()->display();
```
