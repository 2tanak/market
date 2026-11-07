<div class="sidebar sidebar-main sidebar-default">
	<div class="sidebar-content">

		<div class="sidebar-user">
			<div class="category-content">
				<div class="media">
					<a href="#" class="media-left">

					</a>
					<div class="media-body">
						<span class="media-heading text-semibold">
							Логин:&nbsp&nbsp {{ Auth::user()->name }}</span>
						<div class="text-size-mini text-muted">
							<!----{{ Auth::user()->type_name }}---->
						</div>
					</div>
					<div class="media-body">
						<span class="media-heading text-semibold"></span>
						<div class="text-size-mini text-muted">

						</div>
					</div>
				</div>
			</div>
		</div>



		<div class="sidebar-category sidebar-category-visible">
			<div class="category-content no-padding">
				<ul class="navigation navigation-main navigation-accordion">

  <li style="">
       <a   style="" href="#">
	   <i class="icon-city"></i>
	   <span> @lang('sidebar.blog.title')
	   </span></a>
	     <ul class="hidden-ul">
		
	      <li>
            <a href="{{route('blog.index')}}">
	          <span>@lang('sidebar.blog.name') </span>
	        </a>
	      </li>
		 
          </ul>
       </li>

 <li style="">
       <a   style="" href="#">
	   <i class="icon-city"></i>
	   <span>Объявления</span></a>
	     <ul class="hidden-ul">
		
	      <li>
            <a href="{{route('adverts.index')}}">
	          <span>Объявления</span>
	        </a>
	      </li>
		 
          </ul>
       </li>
					<li style="">
						<a style="" href="#">
							<i class="icon-city"></i>
							<span>графика
							</span></a>
						<ul class="hidden-ul">

							<li>
								<a href="{{route('charline')}}">
									<span>линейная </span>
								</a>
							</li>

							<li>
								<a href="{{route('charkrug')}}">
									<span>круговая </span>
								</a>
							</li>


						</ul>
					</li>

					<li style="">
						<a href="{{route('chat')}}">
							<i class="icon-city"></i>
							<span>чаты
							</span>
						</a>

					</li>
					<!------
					<li style="">
						<a href="{{route('privat-index')}}">
							<i class="icon-city"></i>
							<span>чаты
							</span>
						</a>

					</li>
-------------->

				</ul>
			</div>

		</div>
		<div class="sidebar-category sidebar-category-visible">
			<div class="category-content no-padding">
				<ul class="navigation navigation-main navigation-accordion">







					<li style="">
						<a style="" href="#">
							<i class="icon-city"></i>
							<span>Пользователи
							</span></a>
						<ul class="hidden-ul">

							<li>
								<a href="{{route('user.index')}}">
									<span>Пользователи </span>
								</a>
							</li>





						</ul>



					</li>

					<li style="">
						<a style="" href="#">
							<i class="icon-city"></i>
							<span>Роли
							</span></a>
						<ul class="hidden-ul">
							<li>
								<a href="{{route('role.index')}}">
									<span>роли </span>
								</a>
							</li>


						</ul>



					</li>



				</ul>
			</div>

		</div>




	</div>
</div>