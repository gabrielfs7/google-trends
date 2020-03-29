# How to Contribute

## Pull Requests

1) Fork the repository
2) Create a new branch for each feature or improvement
3) Send a pull request from each feature branch

### Rules:

- Please, separate new features or improvements into separate feature branches and send a
pull request for each branch.
- Make sure all code is covered with tests.
- As we still do not have functional tests, please do a manual test also.


## Style Guide

Make sure your code adheres to the [PSR-12 standard](https://www.php-fig.org/psr/psr-12/).

### Auto fixing code

```shell script
composer phpcbf
```
and 
```shell script
composer php-cs-fixer
```

### Running Tests and checking coverage

```shell script
composer test-report
```