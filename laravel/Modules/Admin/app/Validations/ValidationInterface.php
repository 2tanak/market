<?php

namespace Modules\Admin\Validations;

interface ValidationInterface 
{
    public function rules(array $data, $id = null): array;
    public function messages(): array;
}
