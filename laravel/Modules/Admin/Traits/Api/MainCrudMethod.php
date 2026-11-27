<?php

namespace Modules\Admin\Traits\Api;

trait MainCrudMethod
{
    use MainCreateMethod;
    use MainDeleteMethod;
    use MainListMethod;
    use MainShowMethod;
    use MainSystemMethods;
    use MainUpdateMethod;
}
