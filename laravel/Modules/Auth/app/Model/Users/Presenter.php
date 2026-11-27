<?php

namespace Modules\Auth\Model\Users;

trait Presenter
{
    public function the_excerpt($text)
    {
        $text = preg_replace('~<img(.*)>~siU', '', $text);

        return substr(strip_tags($text), 0, 150);
    }

    public function getTextAttribute($v)
    {

        return $this->getTransField('text', $v);
    }
}
