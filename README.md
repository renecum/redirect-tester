# Redirect Tester
A PHP package that takes a two column CSV file and verifies if URLs in Column 1 are redirected to URLs in Column 2 for a given domain.

Notes:
- The CSV uses only two columns: Source URL, Destination and those have to be relative paths to the given domain.
- The redirects are tested in a given domain.

Quick Usage:
- Run `composer dump` to install dependencies.
- Edit the `examples/index.php` file changing the domain and csv path.
- Run `index.php` in the

Todo:
- Improve error and exception handling.
- Add support for non relative paths.
- Save Report to a file.
- Allow report to be rendered as redirect are tested.
- Multi thread for faster execution. 