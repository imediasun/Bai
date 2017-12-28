<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Кастомные данные [{{$val}}]</h3>
        <div class="panel-actions">
            <a class="panel-action voyager-angle-down" data-toggle="panel-collapse" aria-hidden="true"></a>
            <button class="btn btn-danger btn-del-props-custom" data-id="@if(isset($custom_prop->id)){{$custom_prop->id}}@endif">x</button>
        </div>
    </div>
    <div class="panel-body">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#tab_custom_{{ $val }}_ru" aria-expanded="true">Русский</a></li>
            <li><a data-toggle="tab" href="#tab_custom_{{ $val }}_kz">Казахский</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab_custom_{{$val}}_ru" class="tab-pane fade active in">
                <div class="col-md-6 form-group">
                    <label for="custom_options[name_ru][{{ $val }}]">Название условия</label>
                    <input data-translit-source="1" data-translit-target="alt_name_ru_{{ $val }}" class="form-control" id="name_ru_{{ $val }}" name="custom_options[name_ru][{{ $val }}]" value="@if(isset($custom_prop->name_ru)){{ $custom_prop->name_ru }}@endif">
                </div>
                <div class="col-md-6 form-group">
                    <label for="custom_options[alt_name_ru][{{ $val }}]">Транслит [Название]</label>
                    <input class="form-control" id="alt_name_ru_{{ $val }}" name="custom_options[alt_name_ru][{{ $val }}]" value="@if(isset($custom_prop->alt_name_ru)){{ $custom_prop->alt_name_ru }}@endif">
                </div>
                <div class="col-md-6 form-group">
                    <label for="custom_options[value_ru][{{ $val }}]">Значение условия</label>
                    <input data-translit-source="1" data-translit-target="alt_value_ru_{{ $val }}" class="form-control" id="value_ru_{{ $val }}" name="custom_options[value_ru][{{ $val }}]" value="@if(isset($custom_prop->value_ru)){{ $custom_prop->value_ru }}@endif">
                </div>
                <div class="col-md-6 form-group">
                    <label for="alt_{{ $val }}_ru">Транслит [Значение]</label>
                    <input class="form-control" id="alt_value_ru_{{ $val }}" name="custom_options[alt_value_ru][{{ $val }}]" value="@if(isset($custom_prop->alt_value_ru)){{ $custom_prop->alt_value_ru }}@endif">
                </div>
                <div class="col-md-12 form-group">
                    <label for="custom_options[comment_ru][{{ $val }}]">Комментарий</label>
                    <textarea class="form-control " id="comment_ru_{{ $val }}" name="custom_options[comment_ru][{{ $val }}]">@if(isset($custom_prop->comment_ru)){{ $custom_prop->comment_ru }}@endif</textarea>
                </div>
            </div>
            <div id="tab_custom_{{ $val }}_kz" class="tab-pane fade">
                <div class="col-md-6 form-group">
                    <label for="custom_options[name_kz][{{ $val }}]">Название условия</label>
                    <input data-translit-source="1" data-translit-target="alt_name_kz_{{ $val }}" class="form-control" id="name_kz_{{ $val }}" name="custom_options[name_kz][{{ $val }}]" value="@if(isset($custom_prop->name_kz)){{ $custom_prop->name_kz }}@endif">
                </div>
                <div class="col-md-6 form-group">
                    <label for="custom_options[alt_name_kz][{{ $val }}]">Транслит [Название]</label>
                    <input class="form-control" id="alt_name_kz_{{ $val }}" name="custom_options[alt_name_kz][{{ $val }}]" value="@if(isset($custom_prop->alt_name_kz)){{ $custom_prop->alt_name_kz }}@endif">
                </div>
                <div class="col-md-6 form-group">
                    <label for="custom_options[value_kz][{{ $val }}]">Значение условия</label>
                    <input data-translit-source="1" data-translit-target="alt_value_kz_{{ $val }}" class="form-control" id="value_kz_{{ $val }}" name="custom_options[value_kz][{{ $val }}]" value="@if(isset($custom_prop->value_kz)){{ $custom_prop->value_kz }}@endif">
                </div>
                <div class="col-md-6 form-group">
                    <label for="alt_{{ $val }}_kz">Транслит [Значение]</label>
                    <input class="form-control" id="alt_value_kz_{{ $val }}" name="custom_options[alt_value_kz][{{ $val }}]" value="@if(isset($custom_prop->alt_value_kz)){{ $custom_prop->alt_value_kz }}@endif">
                </div>
                <div class="col-md-12 form-group">
                    <label for="custom_options[comment_kz][{{ $val }}]">Комментарий</label>
                    <textarea class="form-control " id="comment_kz_{{ $val }}" name="custom_options[comment_kz][{{ $val }}]">@if(isset($custom_prop->comment_kz)){{ $custom_prop->comment_kz }}@endif</textarea>
                </div>
            </div>
        </div>
        <input type="hidden" name="custom_options[id][{{ $val }}]" value="@isset($custom_prop->id){{$custom_prop->id}}@endisset">

        {{--<div class="row">--}}
            {{--<div class="col-md-3">--}}
                {{--<label for="credit_props">Название условия</label>--}}
                {{--<input type="text" class="form-control" name="credit_props[min_amount][{{$val}}]" placeholder="Название условия" value="@if(isset($prop->min_amount)){{ $prop->min_amount }}@endif">--}}
            {{--</div>--}}
            {{--<div class="col-md-3">--}}
                {{--<label for="credit_props">Значение условия @if(isset($custom_prop->id))[{{$custom_prop->name_ru}}]@endif</label>--}}
                {{--<input type="text" class="form-control" name="credit_props[min_amount][{{$val}}]" placeholder="Значение условия" value="@if(isset($prop->min_amount)){{ $prop->min_amount }}@endif">--}}
            {{--</div>--}}
        {{--</div>--}}
    </div>
</div>
<script>
    function delete_custom_prop_button(id, block) {
        var data = {
            url: '/admin/ajax/del-custom-prop-block',
            id: id,
            product: 'credit',
        };

        $.ajax({
            url: data.url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: data,
            method: 'post',

            error:function (error) {
                console.dir(error);
            }
        });
        block.remove();
    }

    $('[data-translit-source]').bind('change click keyup', function (e) {
        var el = $(e.target),
            id = el.data('translitTarget');
        $.post(
//                    Routing.generate('admin_ajax_tools_translit'),
            '/admin/ajax/translit',
            {
                'text': el.val()
            },
            function (data) {
                $('#' + id).val(data);
            }
        );
    });

    $('.btn-del-props-custom').on('click', function (e) {
        e.preventDefault();

        var panel = $(this).parents('.panel');

        delete_custom_prop_button($(this).data('id'), panel);
    });
</script>
