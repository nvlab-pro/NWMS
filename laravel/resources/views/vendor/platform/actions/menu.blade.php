@isset($title)
    <li class="nav-item mt-3 mb-1">
        <small class="text-muted ms-4 w-100 user-select-none">{{ __($title) }}</small>
    </li>
@endisset

@if (!empty($name))
    <li class="nav-item {{ active($active) }}" style="background-color: #2A2A2A">
        <a data-turbo="{{ var_export($turbo) }}"
                {{ $attributes }}
        >
            @isset($icon)
                <x-orchid-icon :path="$icon" class="overflow-visible"/>
            @endisset

            <span class="mx-2">{{ $name ?? '' }}</span>

            @isset($badge)
                <b class="badge rounded-pill bg-{{$badge['class']}} col-auto ms-auto">{{$badge['data']()}}</b>
            @endisset
        </a>
    </li>
@endif

@if(!empty($list))
    <div class="nav collapse sub-menu ps-3 {{active($active, 'show')}}" style="background-color: #555555" id="menu-{{$slug}}"
         @isset($parent)
             data-bs-parent="#menu-{{$parent}}">
        @else
            data-bs-parent="#headerMenuCollapse">
        @endisset
        @foreach($list as $item)
            {!!  $item->build($source) !!}
        @endforeach
    </div>
@endif

@if($divider)
    <li class="divider my-2"></li>
@endif

