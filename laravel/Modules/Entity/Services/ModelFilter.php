<?php
namespace Modules\Entity\Services;

use Illuminate\Http\Request;

class ModelFilter{
    protected $query = false;
    protected $request = false;
    protected $authid = false;
	
    public function __construct($query, Request $request){

        $this->query = $query;
        $this->request = $request;
		$this->authid = 155;
    }

    function getQuery(){
        return $this->query;
    }
    
}
