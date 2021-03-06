<?php

namespace Feugene\Files\Models;

use Feugene\Files\Entities\FileParams;
use Feugene\Files\Exceptions\MissingFilePathException;
use Feugene\Files\Observers\FileObserver;
use Feugene\Files\Traits\BaseFileApply;
use Feugene\Files\Traits\FileTypes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class File
 *
 * @package Feugene\Files\Models
 * @property \Ramsey\Uuid\Uuid|int $id
 * @property \Ramsey\Uuid\Uuid|int $parent_id
 * @property string                $path
 * @property string|null           $ext
 * @property int                   $size
 * @property string|null           $mime
 * @property string                $driver
 * @property string                $key
 * @property FileParams|null       $params
 * @mixin  \Illuminate\Database\Eloquent\Builder
 */
class File extends Model
{
    use BaseFileApply, FileTypes;

    protected $keyType = 'uuid';

    protected $hidden = [
        'parent',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'path',
        'driver',
        'ext',
        'size',
        'mime',
        'key',
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

    public static function boot()
    {
        parent::boot();
        self::observe(FileObserver::class);
    }

    /**
     * @param string $value
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

    /**
     * @return \Php\Support\Components\Params|\Php\Support\Interfaces\Jsonable|null
     */
    public function getParamsAttribute()
    {
        if (!isset($this->attributes['params'])) {
            return $this->attributes['params'] = new FileParams;
        }

        if ($this->attributes['params'] instanceof FileParams) {
            return $this->attributes['params'];
        }

        if (is_array($this->attributes['params'])) {
            return $this->attributes['params'] = (new FileParams)->fromArray($this->attributes['params']);
        }

        return $this->attributes['params'] = FileParams::fromJson($this->attributes['params']);
    }
}
