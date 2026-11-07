<?php
namespace Modules\Entity;

use Modules\Entity\Contracts\UniversalModelInterface;
use Modules\Entity\Traits\UniversalLogic;
use Illuminate\Database\Eloquent\Model;
use Modules\Entity\Traits\FilterModel;
abstract class ModelParent extends Model implements UniversalModelInterface {
    use UniversalLogic, FilterModel;
}
