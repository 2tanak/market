<?php
namespace Modules\Admin\Traits\Api;

use Illuminate\Http\Request;

trait MainCrudMethod  {
    use MainSystemMethods;
    use MainListMethod;
    use MainCreateMethod;
    use MainUpdateMethod;
    use MainDeleteMethod;
    use MainShowMethod;
}

?>
