<?php
namespace Modules\Entity\Model\File;
use Modules\Entity\ModelParent;

class Model extends ModelParent {
    protected $table = 'files';
	//protected $with = ['files'];
	protected $fillable
        = [
            'disk',
            'original_name',
            'extension',
            'mime_type',
            'size',
			'original_path',
			'large_path',
			'medium_path',
			'small_path',
			'description',
			'fileable_type',
			'fileable_id',
        ];
	
	//protected $filter_class = Filter::class; 
    
	
	public function fileable(){
		// Полиморфная связь (указывает, к какой модели прикреплен файл)
		return $this->morphTo();
	}
	
}
