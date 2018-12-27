<?php

namespace Feugene\Files\Models;

use Feugene\Files\Exceptions\MissingFilePathException;
use Feugene\Files\Support\FileParams;
use Feugene\Files\Traits\BaseFileApply;
use Illuminate\Database\Eloquent\Model;

/**
 * Class File
 *
 * @package Feugene\Files\Models
 * @property \Ramsey\Uuid\Uuid|int $id
 * @property \Ramsey\Uuid\Uuid|int $parent_id
 * @property string                $path
 * @property string                $ext
 * @property string                $size
 * @property string                $mime
 * @property string                $driver
 * @property  FileParams           $params
 * @mixin  \Illuminate\Database\Eloquent\Builder
 */
class File extends Model
{
    use BaseFileApply;

    protected $keyType = 'uuid';

    protected $casts = [
        'params' => 'array',
    ];

    protected $hidden = [
        'parent',
    ];

    protected $fillable = [
        'path',
        'driver',
        'ext',
        'size',
        'mime',
        'params',
    ];

    /**
     * File constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->keyType = config('files.table.id', 'uuid');
        $this->table = config('files.table.name', 'files');

        parent::__construct($attributes);
    }


    /**
     * @param $value
     *
     * @throws \Feugene\Files\Exceptions\MissingFilePathException
     */
    public function setPathAttribute($value)
    {
        if (empty($value)) {
            throw new MissingFilePathException;
        }

        if (!isset($this->attributes['path']) || $this->attributes['path'] !== $value) {
            $this->attributes['path'] = $value;
            $this->setBaseFileFromString($value);
        }
    }


    /*  public function __call($method, $arguments)
      {
          if (method_exists($this->getBaseFile(), $method)) {
              return $this->getBaseFile()->$method(...$arguments);
          }

          return parent::__call($method, $arguments);
      }

      public function __sleep()
      {
          return array_keys(array_except(get_object_vars($this), ['baseFile']));
      }

      public function children()
      {
          return $this->hasMany(static::class, 'parent_id', 'id');
      }

      public function parent()
      {
          return $this->belongsTo(static::class, 'parent_id');
      }

      public function getPath()
      {
          if (!$this->isDefined()) {
              return;
          }

          return $this->pathToAbsolute($this->path);
      }

      public function getUrl()
      {
          if (!$this->isDefined()) {
              return '#undefined';
          }

          return '/' . ltrim($this->path, '/');
      }



      public function getUrlAttribute()
      {
          return $this->getUrl();
      }

      public function getBaseFile()
      {
          if ($this->baseFile === null) {
              $this->initBaseFile();
          }

          return $this->baseFile;
      }

      public function initBaseFile(): void
      {
          // @refactor
          $this->baseFile = new BaseFileTrait($this->pathToAbsolute($this->path) ?? '', false);
      }

      public function setBaseFile($file): void
      {
          if ($file instanceof BaseFileTrait) {
              $this->baseFile = $file;
          } elseif (is_string($file)) {
              $this->baseFile = new BaseFileTrait($this->pathToAbsolute($file) ?? '', false);
          }

          $this->updateFileAttributes();
      }

      public function isDefined()
      {
          return !empty($this->path);
      }

      public function isExists()
      {
          return Filesystem::exists($this->getPath());
      }

      public function isJustUploaded()
      {
          return $this->getBaseFile() instanceof UploadedFile;
      }

      public function delete()
      {
          $this->children()->each(function ($file): void {
              $file->delete();
          });

          $this->remove();

          return parent::delete();
      }

      public function copy($filepath)
      {
          $file = new static;

          Filesystem::copy($this->getPath(), $filepath);

          $file->setBaseFile($filepath);

          return $file;
      }

      public function move($newDistanation): void
      {
          if (Filesystem::move($this->getPath(), $newDistanation)) {
              $this->setBaseFile($newDistanation);
          }
      }

      public function remove(): void
      {
          Filesystem::delete($this->getPath());
          $this->setBaseFile(false);
      }

      public function img($attributes = null)
      {
          if (!$this->isImage()) {
              return '';
          }

          return html_tag('img', $this->htmlTagAttributes(), is_array($attributes) ? $attributes : []);
      }

      public function attr(array $attributes = [])
      {
          $attributes = $attributes + $this->htmlTagAttributes();

          return html_tag_attr($attributes);
      }

      public function jsonSerialize()
      {
          return [
                  'url'         => $this->getUrl(),
                  'is_image'    => $this->isImage(),
                  'is_video'    => $this->isVideo(),
                  'is_audio'    => $this->isAudio(),
                  'is_document' => $this->isDocument(),
                  'children'    => $this->children->map(function ($file) {
                      return $file->jsonSerialize();
                  })->toArray(),
              ] + parent::jsonSerialize();
      }

      protected function htmlTagAttributes()
      {
          if ($this->isImage()) {
              return [
                  'src'    => $this->getUrl(),
                  'width'  => $this->width ?: null,
                  'height' => $this->height ?: null,
              ];
          }

          return [];
      }

      protected function updateFileAttributes(): void
      {
          $this->path = $this->pathToRelative($this->getBaseFile()->getPathname());
          $this->ext = $this->getBaseFile()->getExtension();

          if ($this->isExists()) {
              clearstatcache();
              $this->size = $this->getBaseFile()->getSize();
              $this->mime = $this->getBaseFile()->getMimeType();
          }

          if ($this->isJustUploaded()) {
              $settings = $this->settings;

              $this->settings = array_set($settings, 'upload_info', [
                  'extension' => $this->getBaseFile()->clientExtension(),
                  'name'      => $this->getBaseFile()->getClientOriginalName(),
                  'type'      => $this->getBaseFile()->getClientMimeType(),
                  'size'      => $this->getBaseFile()->getClientSize(),
                  'error'     => $this->getBaseFile()->getError(),
              ]);
          }

          if ($this->isImage()) {
              $this->initWidthAndHeight();
          }
      }

      protected function pathToAbsolute($path)
      {
          if (starts_with($path, '/') || str_contains($path, ':\\')) {
              return $path;
          }

          if (starts_with($path, $dir = trim(config('upload.url'), '/'))) {
              return preg_replace('#^' . preg_quote($dir) . '/*#', config('upload.path') . '/', $path);
          }

          return $path === null ? null : public_path($path);
      }

      protected function pathToRelative($path)
      {
          if (starts_with($path, config('upload.path'))) {
              return preg_replace('#^(' . preg_quote(config('upload.path')) . ')/*#', trim(config('upload.url'), '/') . '/',
                  $path);
          }

          if (starts_with($path, public_path())) {
              return preg_replace('#^' . preg_quote(public_path()) . '/*#', '', $path);
          }

          return $path;
      }*/
}
