<div class="admin-form">

	@include('admin::components.select',
	[
	'title' => 'roles[]',
	'value' => $model->getRoleNames()->toArray(),
	'id' => 'roles-select',
	'placeholder' => 'Выберите роли...',
	'multiple' => 'multiple',
	'option' => \Modules\Auth\Model\Role\Model::getRolesAr()
	])


	@include('admin::components.input',
	[
	'title'=>'name',
	'value'=>$model->name
	])
	@include('admin::components.input',
	[
	'title'=>'slug',
	'value'=>$model->slug,
	'maxlength'  => 255,
	])
	@include('admin::components.select',
	[
	'title' => 'guard_name',
	'value' => $model->guard_name,
	'option' => array_combine(['web', 'api'],['web','api'])
	])

</div>

@section('js_block')
@parent
@endsection