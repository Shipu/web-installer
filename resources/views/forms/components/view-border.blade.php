{{--<div class="flex grid grid-cols-4 gap-x-2 p-3 border-2">--}}
{{--    <div class="col-span-1 pl-10">{{ $getLabel() }}</div>--}}
{{--    <div class="@if($isRequired()) text-danger-500 @endif">--}}
{{--        @if(is_string($getState()))--}}
{{--            {{ $getState() }}--}}
{{--        @else--}}
{{--            @if($getState() === true)--}}
{{--                <x-heroicon-o-check-circle class="w-8 h-8 text-success-500" />--}}
{{--            @elseif($getState() === false)--}}
{{--                <x-heroicon-o-x-circle class="w-8 h-8 text-danger-500" />--}}
{{--            @endif--}}
{{--        @endif--}}
{{--    </div>--}}
{{--</div>--}}
<div class="flex justify-between gap-x-2 p-3 border-2">
    <div class="pl-10">
        {{ $getLabel() }}
    </div>
    <div class="@if($isRequired()) text-danger-500 @endif">
        @if(is_string($getState()))
            {{ $getState() }}
        @else
            @if($getState() === true)
                <x-heroicon-o-check-circle class="w-8 h-8 text-primary-500" />
            @elseif($getState() === false)
                <x-heroicon-o-x-circle class="w-8 h-8 text-danger-500" />
            @endif
        @endif
    </div>
</div>
