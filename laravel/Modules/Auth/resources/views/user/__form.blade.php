<div class="admin-form">
  
   @include('admin::components.select',
	[
	'title'           => 'roles[]',
	'value'           =>  $model->getRoleNames()->toArray(),
	'id'              => 'roles-select',
	'placeholder'     => 'Выберите роли...',
	'multiple'        => 'multiple',
	'option'      => \Modules\Auth\Model\Role\Model::getRolesAr() 
	])

   @include('admin::components.input',
	[
	'title'=>'email',
	'value'=>$model->email
	])
	
   @include('admin::components.input',
	[
	'title'=>'first_name',
	'value'=>$model->first_name
	])
	
	@include('admin::components.input',
	[
	'title'=>'last_name',
	'value'=>$model->last_name 
	])
 	 
	@include('admin::components.input',
	['title'     => 'phone',
	'value'      =>  $model->phone,
	'maxlength'  => 18,
	'placeholder' => "+7 (___) ___-__-__",
	'class' => "phone",
	])
	
	@include('admin::components.select',
	[
	'title'  => 'blocked',
	'value'  => $model->blocked ?? 1,
	'option' => array_combine([0, 1],['blocked','activen'])
	])
	
	@include('admin::components.input',
	[
	'title' => 'password',
	'value'  => $model->password,
	'clear'  => ''
	])
	
	@include('admin::components.input',
	[
	'title' => 'password_confirmation',
	'value'  => $model->confirmed,
	'clear'  => ''
	])
	

</div>

@section('js_block')
@parent
@endsection