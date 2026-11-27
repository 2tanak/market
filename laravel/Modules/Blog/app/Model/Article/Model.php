<?php

namespace Modules\Blog\Model\Article;

use Modules\Entity\Model\File\Model as File;
use Modules\Entity\ModelParent;

class Model extends ModelParent
{
    use Presenter;

    protected $table = 'articles';

    protected $fillable = ['title', 'description', 'publish'];

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');

    }

    public function transable(): MorphTo
    {

        return $this->morphTo();
    }
}
