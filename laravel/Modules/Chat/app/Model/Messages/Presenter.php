<?php

namespace Modules\Chat\Model\Messages;

trait Presenter
{
    public function the_excerpt($text)
    {
        $text = preg_replace('~<img(.*)>~siU', '', $text);

        return substr(strip_tags($text), 0, 150);
    }

    public function getGalleryAttribute()
    {
        if (@unserialize($this->gallery_img)) {
            return unserialize($this->gallery_img);
        } else {
            return $this->gallery_img;
        }
    }

    public function getCityAr()
    {
        // return City::pluck('name', 'id')->toArray();
    }

    public function getNameAttribute($v)
    {
        return $this->getTransField('name', $v);
    }

    public function getDescriptionAttribute($v)
    {
        return $this->getTransField('description', $v);
    }
}

