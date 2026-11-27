<?php

namespace Modules\Entity;

use Illuminate\Database\Eloquent\Model;
use Modules\Entity\Contracts\UniversalModelInterface;
use Modules\Entity\Traits\FilterModel;
use Modules\Entity\Traits\UniversalLogic;

abstract class ModelParent extends Model implements UniversalModelInterface
{
    use FilterModel, UniversalLogic;
}
