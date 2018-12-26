[![Latest Stable Version](https://poser.pugx.org/efureev/laravel-files/v/stable)](https://packagist.org/packages/efureev/laravel-files)
[![Total Downloads](https://poser.pugx.org/efureev/laravel-files/downloads)](https://packagist.org/packages/efureev/laravel-files)
[![Latest Unstable Version](https://poser.pugx.org/efureev/laravel-files/v/unstable)](https://packagist.org/packages/efureev/laravel-files)

[![Build Status](https://travis-ci.org/efureev/laravel-files.svg?branch=master)](https://travis-ci.org/efureev/laravel-files)

[![Maintainability](https://api.codeclimate.com/v1/badges/6f7ae271de2ad9d33ccd/maintainability)](https://codeclimate.com/github/efureev/laravel-files/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/6f7ae271de2ad9d33ccd/test_coverage)](https://codeclimate.com/github/efureev/laravel-files/test_coverage)

## Information
Add-on to file model for Laravel. Implements work with native files.


## Install
- `composer require efureev/laravel-files`

## Examples
- Add ServiceProvider into your app: `config/app.php` (section: `providers`)
    ```php
        // ...
        Feugene\Files\ServiceProvider::class,
    ```
    or if Laravel >= 5.7 - use service discover.

- Run `php artisan migrate` for add table for file 

