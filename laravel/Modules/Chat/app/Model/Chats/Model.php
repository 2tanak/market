<?php

namespace Modules\Chat\Model\Chats;

use Modules\Adverts\Model\Product\Model as Product;
use Modules\Auth\Model\Users\Model as User;
use Modules\Chat\Model\Messages\Model as Message;
use Modules\Entity\Model\File\Model as File;
use Modules\Entity\Model\Translation\Model as Translation;
use Modules\Entity\ModelParent;

class Model extends ModelParent
{
    use Presenter;

    protected $table = 'chats';

    protected $fillable = ['listing_id', 'buyer_id', 'seller_id', 'created_at', 'updated_at'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'listing_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'chat_id');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class, 'chat_id')->latestOfMany('id');
    }

    public function files()
    {
        return $this->morphOne(File::class, 'fileable');
    }

    public function getNameSpace()
    {
        return __NAMESPACE__;
    }

    public function relTrans()
    {
        return $this->morphMany(Translation::class, 'transable');
    }
}
