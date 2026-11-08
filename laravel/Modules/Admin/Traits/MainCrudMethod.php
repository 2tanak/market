<?php

namespace Modules\Admin\Traits;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


trait MainCrudMethod
{
    use MainSystemMethods;
    use MainListMethod;
    use MainCreateMethod;
    use MainUpdateMethod;
    use MainDeleteMethod;
    use MainShowMethod;
    use AuthorizesRequests;
}
