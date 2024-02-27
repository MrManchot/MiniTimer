# MiniTimer

MiniTimer is a lightweight PHP library designed to facilitate tracking and displaying the execution time of different parts of your code. It is particularly useful for debugging and performance optimization by measuring the execution time of specific code blocks.

## Features

- Measures the execution time of specific tasks within your application.
- Supports task hierarchy for detailed analysis.
- Displays results in an easy-to-read format.
- Implements the Singleton pattern for easy use and global access.

## Installation

Copy the `MiniTimer.php` file into your project and include it in your PHP scripts where you wish to use MiniTimer.

## Usage

Here's how you can use MiniTimer in your project:

### Starting and Stopping a Timer

To measure the execution time of a part of your code, surround it with the `start` and `stop` methods:

```php
require_once 'MiniTimer.php';

// Start the timer
MiniTimer::inst()->start('YourTaskName');

// Your code here

// Stop the timer
MiniTimer::inst()->stop('YourTaskName');
```

### Displaying the Results
To display the results of the measurements:

```php
MiniTimer::inst()->display();
```