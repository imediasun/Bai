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
                    <textarea class="form-control @if($is_richbox) richTextBox @endif" id="{{ $name }}_ru" name="{{ $name }}_ru" @if($is_richbox)style="border:0px;"@endif>@if(isset($dataTypeContent[$name.'_ru'])){{ $dataTypeContent[$name.'_ru'] }}@endif</textarea>
                </div>
            </div>
            <div id="tab_{{ $name }}_kz" class="tab-pane fade">
                <div class="col-md-12 form-group">
                    <label for="{{ $name }}_kz">{{ $title }} на казахском</label>
                    <textarea class="form-control @if($is_richbox) richTextBox @endif" id="{{ $name }}_kz" name="{{ $name }}_kz" @if($is_richbox)style="border:0px;"@endif>@if(isset($dataTypeContent[$name.'_kz'])){{ $dataTypeContent[$name.'_kz'] }}@endif</textarea>
                </div>
            </div>
        </div>
    </div>

</div><!-- .panel -->