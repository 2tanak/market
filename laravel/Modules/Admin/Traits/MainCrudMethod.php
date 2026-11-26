<?php

namespace Modules\Admin\Traits;

use Illuminate\Http\Request;


trait MainCrudMethod
{
    use MainSystemMethods;
    use MainListMethod;
    use MainCreateMethod;
    use MainUpdateMethod;
    use MainDeleteMethod;
    use MainShowMethod;
    //use Utils;
}
