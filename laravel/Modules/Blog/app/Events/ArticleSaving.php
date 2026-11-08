<?php

namespace Modules\Blog\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;

class ArticleSaving
{
    use SerializesModels;

    public Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}
