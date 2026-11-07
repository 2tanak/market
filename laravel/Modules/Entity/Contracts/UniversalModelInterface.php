<?php

namespace Modules\Entity\Contracts;

interface UniversalModelInterface {
    public function relTrans();
    public function setLocale($locale = false);
  
}
