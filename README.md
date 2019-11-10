# brainfudge
[![Build Status](https://travis-ci.com/adoxography/brainfudge.svg?branch=master)](https://travis-ci.com/adoxography/brainfudge)

## Installation
You should have [composer](https://getcomposer.org/) installed on your system, as well as PHP >= 7.0.

1. Clone the repository: `git clone https://github.com/adoxography/brainfudge && cd brainfudge`
1. Generate the autoload file: `composer dump-autoload`

## Usage

1. `brainfudge` takes input from `stdin`. To use it interactively, run `bin/brainfudge`. To us it as part of a script, pipe some input in; e.g. `echo '-[------->+<]>-.-[->+++++<]>++.+++++++..+++.' | bin/brainfudge`

## Testing
To test the CLI, run `bin/test_cli`. The only dependency is Bash.

To test the library, install the development dependencies with `composer install`. Then run `bin/test_lib`.

## License
[MIT](LICENSE)
