<?php

namespace Feugene\Files\Models;

use Feugene\Files\Exceptions\MissingFilePathException;
use Feugene\Files\Entities\FileParams;
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
 * @property FileParams            $params
 * @mixin  \Illuminate\Database\Eloquent\Builder
 */
class File extends Model
{
    use BaseFileApply;

    protected $keyType = 'uuid';

    protected $casts = [
//        'params' => 'array',
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

    /**
     * @return \Php\Support\Components\Params|\Php\Support\Interfaces\Jsonable|null
     */
    public function getParamsAttribute()
    {
        if ($this->attributes['params'] instanceof FileParams) {
            return $this->attributes['params'];
        }

        return $this->attributes['params'] = FileParams::fromJson($this->attributes['params'] ?? '');
    }
}
