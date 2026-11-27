<?php

namespace Modules\Blog\Model\Article;

trait Presenter
{
    public function the_excerpt($text)
    {
        $text = preg_replace('~<img(.*)>~siU', '', $text);

        return substr(strip_tags($text), 0, 150);
    }

    public function getTitleAttribute($v)
    {

        return $this->getTransField('title', $v);
    }

    public function getDescriptionAttribute($v)
    {

        return $this->getTransField('description', $v);
    }
}
