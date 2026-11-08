<div class="admin-form">



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
	
@include('admin::components.slider',['model'=>$model])

</div>

@section('js_block')
    @parent
@endsection
