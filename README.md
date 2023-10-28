# MiniTimer

## Description
MiniTimer is a simple and efficient PHP class for profiling your code's execution time. It allows you to measure the time spent in different parts of your PHP script and display the results in a readable format.

## Features
Start and stop timers with unique names.
Add measurement points to track time between them.
Display the results in a formatted HTML table.

## Installation
Copy the MiniTimer.php file into your project and include it in your PHP script.

```php
require_once 'MiniTimer.php';
```

## Usage

### Starting a Timer
To start a timer, use the start() method with a unique name for the timer.

```php
$miniTimer = new MiniTimer();
$miniTimer->start('Timer name');
```

### Stopping a Timer
To stop a timer, use the stop() method with the same name you used to start the timer.

```php
$miniTimer->stop('Timer name');
```

### Adding a Measurement Point

To add a measurement point, use the addPoint() method.

```php
$miniTimer->addPoint();
```

### Displaying Results
To display the results, use the display() method.

```php
$miniTimer->display();
```

### Complete Example
```php
$miniTimer = new MiniTimer();

// Start a timer
$miniTimer->start('Timer name');

// Do something
do_something();

// Stop the timer
$miniTimer->stop('Timer name');

// Add measurement points
$miniTimer->addPoint();
do_something();
$miniTimer->addPoint();
do_something();
$miniTimer->addPoint();

// Display the results
$miniTimer->display();
```