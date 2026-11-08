<div class="admin-form">



@include('admin::components.input',
	['title' => 'title',
	'value'  =>  $model->title,
	'placeholder' => "Заголовок...",
	
	])

   @include('admin::components.select',
	[
	'title'  => 'publish',
	'value'  => 1,
	'option' => array_combine([1, 0],['publish','blocked'])
	])
 
    <!-- Поле Description -->
   @include('admin::components.editor',
	[
	'title' => 'description',
	'value' => $model->description,
	'id'   => 'editor'
	])
	


	


	
	
	
	
</div>

@section('js_block')
    @parent
@endsection
