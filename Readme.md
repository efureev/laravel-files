[![Latest Stable Version](https://poser.pugx.org/feugene/laravel-files/v/stable)](https://packagist.org/packages/feugene/laravel-files)
[![Total Downloads](https://poser.pugx.org/feugene/laravel-files/downloads)](https://packagist.org/packages/feugene/laravel-files)
[![Latest Unstable Version](https://poser.pugx.org/feugene/laravel-files/v/unstable)](https://packagist.org/packages/feugene/laravel-files)

[![Build Status](https://travis-ci.org/efureev/laravel-files.svg?branch=master)](https://travis-ci.org/efureev/laravel-files)

[![Maintainability](https://api.codeclimate.com/v1/badges/6f7ae271de2ad9d33ccd/maintainability)](https://codeclimate.com/github/efureev/laravel-files/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/6f7ae271de2ad9d33ccd/test_coverage)](https://codeclimate.com/github/efureev/laravel-files/test_coverage)

## Information
Add-on file model for Laravel models. Implements work with native files.


## Install
- `composer require feugene/laravel-files`

## Examples
- Add ServiceProvider into your app: `config/app.php` (section: `providers`)
    ```php
        // ...
        Feugene\Files\ServiceProvider::class,
    ```
    or if Laravel >= 5.7 - use service discover.

- Run `php artisan migrate` for add table for file 

Only simple upload:
```php
public function store()
{
    $list = app(UploadService::class)
        ->upload();

    return [
        'success' => $list->isNotEmpty(),
        'files'   => $list,
    ];
}
```

Upload with wrapped file to model via after actions:

```php
public function store()
{
    $list = app(UploadService::class)
        ->setAfterAction(AfterModelAction::class)
        ->upload();

    return [
        'success' => $list->isNotEmpty(),
        'files'   => $list,
    ];
}
```


Upload with wrapped file to custom model and custom path:

```php
public function store(int $sectionId)
{
    /** @var Section $section */
    $section = Section::findOrFail($sectionId);

    $this->authorize('uploadFile', $section);

    $upload = new Upload($section);
    $path = $upload->getUploadPath();

    $list = app(UploadService::class)
        ->setPath($path)
        ->setAction(BeforeBaseAction::class, 'before')
        ->setAfterAction(AfterModelAction::class)
        ->setAfterAction(function ($file) use ($section) {
            /** @var \Feugene\Files\Models\File $file */
            
            return File::create([
                'section_id' => $section->id,
                'author_id'  => \Auth::id(),
                'name'       => $file->getBaseFile()->getFilename(),
                'file_id'    => $file->getKey()
            ]);
        })
        ->upload();

    return [
        'success' => $list->isNotEmpty(),
        'files'   => $list,
    ];
}
```

