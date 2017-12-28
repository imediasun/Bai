<div class="form-group">
    <label for="{{$name}}">{{$title}}</label>
    <select name="{{$name}}" class="@if($is_select2) select2 @else form-control @endif">
        @foreach($options as $option)
            <option @if(isset($dataTypeContent->{$name}) && $dataTypeContent->{$name} == $option['value']){{ 'selected="selected"' }}@endif value="{{$option['value']}}">{{$option['name']}}</option>
        @endforeach
    </select>
</div>