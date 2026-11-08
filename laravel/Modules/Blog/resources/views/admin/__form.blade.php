<div class="admin-form">

@include('admin::components.image',['model'=>$model])

@include('admin::components.input',
	['title' => 'title',
	'value'  =>  $model->title,
	'placeholder' => "Заголовок...",
	
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
