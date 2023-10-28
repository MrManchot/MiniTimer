# MiniTimer

## Description
MiniTimer is a simple and efficient PHP class designed for profiling your code's execution time. It allows you to measure the time spent on different parts of your PHP script and display the results in a readable format.

## Features
- Start and stop timers with unique names.
- Add measurement points to track time between them.
- Display the results in a formatted HTML table.
- Save timer data to a log file.
- Display aggregated and sorted timer data from the log file.

## Installation
Copy the `MiniTimer.php` file into your project directory and include it in your PHP script.

```php
require_once 'MiniTimer.php';
```

## Usage

### Initialization
To initialize MiniTimer, simply create a new instance of the class. You can optionally specify a log file name.

```php
$miniTimer = new MiniTimer();  // Default log file 'timers.log'
// or
$miniTimer = new MiniTimer('custom.log');  // Custom log file
```

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

### Saving Results to a Log File
To save the timer data to a log file, use the save() method.


```php
$miniTimer->save();
```

### Displaying Aggregated Results from Log File
To display aggregated and sorted timer data from the log file, use the displayTotal() method.

```php
$miniTimer->displayTotal();
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

// Save to log file
$miniTimer->save();

// Display the results
$miniTimer->display();

// Display aggregated results from log file
$miniTimer->displayTotal();
```