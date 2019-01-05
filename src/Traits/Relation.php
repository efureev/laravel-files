<?php

namespace Feugene\Files\Traits;

use Feugene\Files\Models\AbstractRelationFile;

/**
 * Trait Relation
 *
 * @package Feugene\Files\Traits
 * @property $this parent
 */
trait Relation
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(static::class, 'parent_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    /**
     * @param \Feugene\Files\Models\AbstractRelationFile $model
     *
     * @return \Feugene\Files\Traits\Relation
     */
    public function setParent(AbstractRelationFile $model): self
    {
        $this->parent_id = $model->getKey();

        return $this;
    }

    /**
     * @return bool|null
     * @throws \Exception
     */
    public function delete()
    {
        $this->children()->each(function ($file): void {
            /** @var AbstractRelationFile $file */
            $file->delete();
        });

        return parent::delete();
    }

}
