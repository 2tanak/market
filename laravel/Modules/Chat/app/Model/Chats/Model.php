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
    // protected $with = ['files'];
    protected $fillable = ['product_id', 'buyer_id', 'seller_id', 'created_at', 'updated_at'];
    // protected $filter_class = Filter::class;

    // Связь с товаром
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    // Связь с продавцом (владелец товара)
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // Связь «Один ко многим» со всеми сообщениями в этой комнате
    public function messages()
    {
        return $this->hasMany(Message::class, 'chat_id');
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
