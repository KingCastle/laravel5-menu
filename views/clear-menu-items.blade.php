@foreach($items as $item)
<li{!!$item->builder->attributes(array_except($item->attr(),['id','class']))!!}>
    <a>{!!$item->title!!}</a>    
    @if($item->hasChildren())
    <ul>
        @include('laravel5-menu::clear-menu-items', 
          array('items' => $item->children()))
    </ul> 
    @endif
</li>
@endforeach