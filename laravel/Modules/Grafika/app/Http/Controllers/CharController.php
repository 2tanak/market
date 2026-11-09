<?php

namespace Modules\Grafika\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Events\NewEvent;
class CharController extends Controller
{
  protected $vars = array();
	  public function __construct() {
		
    	$this->template = 'index';
			
	}
	
	
	
	
	
	
	/*----------------------обновление графика в режиме realtime-------------------------*/	
	public function grafikRealtime(Request $request){
		
	  $content = view('char.charrealtime')->render();
		$this->vars['content']= $content;
		return view($this->template)->with($this->vars);
	}
	
	
	public function grafikRealtimeAjax(Request $request){
		
		$result = [
		'labels'=>['январь','февраль','март','апрель','май'],
		'datasets' => array(
		
		[
		  'label' => 'серебро',
		  'backgroundColor' => 'blue',
		  'data' => [150,50,100,300,320],
		]
		)
		];
        
		if($request->has('label')){
			//return $request->all();
		   $result['labels'][] = $request->label;
		  $result['datasets'][0]['data'][] = (integer)$request->sale;
		    if($request->realtime == 'true'){
			
		   		   event(new NewEvent($result));
                  }
		        }else{
					return $result;
				}
	  
	
	  
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
/*----------------------обновление графика по событию-------------------------*/	
	public function charDunam(){
		
	  $content = view('char.chardunam')->render();
		$this->vars['content']= $content;
		return view($this->template)->with($this->vars);
	}
	
	
	public function charViewDunam(){
				header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: *');
header('Content-Type: application/json');

	
		$result = [
		'labels'=>['январь','февраль','март','апрель','май'],
		'datasets' => array(
		
		[
		  'label' => 'Золото',
		  'backgroundColor' => 'blue',
		  'data' => [rand(0,150),rand(0,450),rand(0,500),rand(0,600),rand(0,620)],
		],
		[
		  'label' => 'Серебро',
		  'backgroundColor' => 'red',
		  'data' => [rand(0,150),rand(0,450),rand(0,500),rand(0,600),rand(0,620)],
		]
		)
		];
        return $result;
		/*
		if($request->has('label')){
		   $result['labels'][] = $request->label;
		  $result['datasets'][0]['data'][] = (integer)$request->sale;
		    if($request->realtime == 'true'){
			
		   		   event(new NewEvent($result));
                  }
		        }else{
					return $result;
				}
				*/
	  
	}
	
	
	
	
	
	
	
	
	/*----------------------круговая диаграмма-------------------------*/	
	public function charkrug(){
		return view('grafika::char.charviewkrug');
	}
	
	public function charviewkrug(){
		return [
		'labels'=>['январь','февраль','март','апрель'],
		'datasets' => array([
		  'label' => 'продам',
		  //'backgroundColor' => 'red',
		   'backgroundColor' => ['red','green','yellow','blue'],
		   //'borderColor'=>'blue',
		  'data' => [15000,5000,10000,30000],
		  //'borderWidth' => '5' // назначаем ширину линий
		  //'datalabels'=>['color'=>'black','labels'=>['value'=>['color'=>'red']],'title'=>['font'=>'bold','size'=>20,'color'=>'green']],
        //'color'=>'#FFCE56'
      
		])
		];
	}
/*----------------------линейный график-------------------------*/	
	public function charline(){
		
		return view('grafika::char.char');
	}
	
	
	public function char(){
		return [
		'labels'=>['январь','февраль','март','апрель','май'],
		'datasets' => array([
		  'label' => 'продам',
		  'backgroundColor' => 'green',
		  'data' => [15,5,10,30,80],
		])
		];
		
		
		
		
		
		
	}
	
	
	public function krug(){
		return view('grafika::char.charviewkrug');
	}
	
}










