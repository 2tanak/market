<div class="content-right">
     <details class="lang-dropdown" open>
         <summary class="lang-title">Языки</summary>
         <ul class="lang-list">
             @foreach ($sys_lang->getAr() as $k => $v)
             <li class="lang-item">
                 <a href="{{ route($route_path.'.edit', $model) }}?lang={{ $k }}"
                     @class([ 'lang-link' , 'active'=> ($lang === $k) || (blank($lang) && env('APP_LOCALE') === $k)
                     ])>
                     {{ $v }}
                 </a>

             </li>
             @endforeach
         </ul>
     </details>
 </div>