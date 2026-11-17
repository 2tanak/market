<?php
namespace Modules\Chat\Chats\Model;
use Modules\Entity\ModelParent;
use Modules\Entity\Traits\CheckTrans;
use Modules\Entity\Model\File\Model as File;
use Modules\Entity\Observers\NewsObserver;
use Modules\Entity\Model\Translation\Model as Translation;
//use App\Observers\NewsObserver;
use Modules\Auth\Model\Users\Model as User;
use Modules\Entity\Model\Messages\Model as Message;
use Modules\Entity\Model\Product\Model as Product;

class Model extends ModelParent
{
    protected $table = 'chats';
    //protected $with = ['files'];
    protected $fillable = ['product_id', 'buyer_id', 'seller_id', 'created_at', 'updated_at'];

    //protected $filter_class = Filter::class;
    use Presenter;
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
	
	
	
   function files()
    {

        return $this->morphOne(File::class, 'fileable');
    }


    function getNameSpace()
    {
        return __NAMESPACE__;
    }

    function relTrans()
    {
        return $this->morphMany(Translation::class, 'transable');

    }
}