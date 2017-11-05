@if($menuItem && count($menuItem->children))
    <ul class="menu">
        @foreach($menuItem->children as $child)
            <li class="menu__item">
                <a href="{{ $child->url }}">{{ $child->title }}</a>
                {!! crud_menu($child->id) !!}
            </li>
        @endforeach
    </ul>
@endif