<!-- ### {{ $title }} ### -->
<div class="panel @if(isset($panel_classes)) {{$panel_classes}} @endif">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="icon wb-book"></i>{{ $title }}</h3>
        <div class="panel-actions">
            @if(isset($panel_action))
                <a class="panel-action voyager-angle-down" data-toggle="panel-collapse" aria-hidden="true"></a>
            @else
                <a class="panel-action voyager-resize-full" data-toggle="panel-fullscreen" aria-hidden="true"></a>
            @endif
        </div>
    </div>
    <div class="panel-body">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#tab_{{ $name }}_ru" aria-expanded="true">Русский</a></li>
            <li><a data-toggle="tab" href="#tab_{{ $name }}_kz">Казахский</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab_{{$name}}_ru" class="tab-pane fade active in">
                <div class="col-md-12 form-group">
                    <label for="{{ $name }}_ru">{{ $title }} на русском</label>
                    <input @if($need_slug)data-translit-source="1" data-translit-target="alt_{{ $name }}_ru" @endif class="form-control" id="{{ $name }}_ru" name="{{ $name }}_ru" value="@if(isset($dataTypeContent[$name.'_ru'])){{ $dataTypeContent[$name.'_ru'] }}@endif">
                </div>
                @if($need_slug)
                    <div class="col-md-12 form-group">
                        <label for="alt_{{ $name }}_ru">Транслит с русского</label>
                        <input class="form-control" id="alt_{{ $name }}_ru" name="alt_{{ $name }}_ru" value="@if(isset($dataTypeContent['alt_'.$name.'_ru'])){{ $dataTypeContent['alt_'.$name.'_ru'] }}@endif">
                    </div>
                @endif
            </div>
            <div id="tab_{{ $name }}_kz" class="tab-pane fade">
                <div class="col-md-12 form-group">
                    <label for="{{ $name }}_kz">{{ $title }} на казахском</label>
                    <input @if($need_slug)data-translit-source="1" data-translit-target="alt_{{ $name }}_kz" @endif class="form-control" id="{{ $name }}_kz" name="{{ $name }}_kz" value="@if(isset($dataTypeContent[$name.'_kz'])){{ $dataTypeContent[$name.'_kz'] }}@endif">
                </div>
                @if($need_slug)
                    <div class="col-md-12 form-group">
                        <label for="alt_{{ $name }}_kz">Транслит с казахского</label>
                        <input class="form-control" id="alt_{{ $name }}_kz" name="alt_{{ $name }}_kz" value="@if(isset($dataTypeContent['alt_'.$name.'_kz'])){{ $dataTypeContent['alt_'.$name.'_kz'] }}@endif">
                    </div>
                @endif
            </div>
        </div>
    </div>

</div><!-- .panel -->