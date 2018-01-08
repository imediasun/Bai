<div class="panel panel-fee">

    <div class="panel-heading">
        <h3 class="panel-title">Прочие комиссии [{{$prop_number}}][{{$fee_number}}]</h3>
        <div class="panel-actions">
            {{--<a class="panel-action voyager-angle-down" data-toggle="panel-collapse" aria-hidden="true"></a>--}}
            <a class="btn mce-btn-small btn-delete-fee" data-id="@if(isset($fee_block->id)){{$fee_block->id}}@endif">X</a>
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <input type="hidden" value="@if(isset($fee_block->id)){{$fee_block->id}}@endif" name="credit_props[credit_fees][{{$prop_number}}][{{$fee_number}}][fee_id]">

            <div class="col-md-5">
                <label for="credit_props">Комиссия за рассмотрение</label>
                <select name="credit_props[credit_fees][{{$prop_number}}][{{$fee_number}}][{{ 'review' }}]" class="form-control">
                        <option @if(isset($fee_block) && $fee_block->review == 'one_time_percent') selected @endif value="one_time_percent">разово в % от кредита</option>
                        <option @if(isset($fee_block) && $fee_block->review == 'one_time_amount') selected @endif value="one_time_amount">разово в сумме</option>
                        <option @if(isset($fee_block) && $fee_block->review == 'not_less_then_amount') selected @endif value="not_less_then_amount">не менее</option>
                </select>
            </div>
            <div class="col-md-5">
                <label for="credit_props">Комиссия за рассмотрение (текст)</label>
                <input type="text" class="form-control" name="credit_props[credit_fees][{{$prop_number}}][{{$fee_number}}][{{ 'review_input' }}]" placeholder="" value="@if(isset($fee_block) && !empty($fee_block->review_input)) {{ $fee_block->review_input }}@endif">
            </div>

            <div class="col-md-5">
                <label for="credit_props">Комиссия за организацию</label>
                <select name="credit_props[credit_fees][{{$prop_number}}][{{$fee_number}}][{{ 'organization' }}]" class="form-control">
                        <option @if(isset($fee_block) && $fee_block->organization == 'one_time_percent') selected @endif value="one_time_percent">разово в % от кредита</option>
                        <option @if(isset($fee_block) && $fee_block->organization == 'one_time_amount') selected @endif value="one_time_amount">разово в сумме</option>
                        <option @if(isset($fee_block) && $fee_block->organization == 'not_less_then_amount') selected @endif value="not_less_then_amount">не менее</option>
                </select>
            </div>
            <div class="col-md-5">
                <label for="credit_props">Комиссия за организацию (текст)</label>
                <input type="text" class="form-control" name="credit_props[credit_fees][{{$prop_number}}][{{$fee_number}}][{{ 'organization_input' }}]" placeholder="" value="@if(isset($fee_block) && !empty($fee_block->organization_input)) {{ $fee_block->organization_input }}@endif">
            </div>

            <div class="col-md-5">
                <label for="credit_props">Комиссия за обслуживание</label>
                <select name="credit_props[credit_fees][{{$prop_number}}][{{$fee_number}}][{{ 'service' }}]" class="form-control">
                        <option @if(isset($fee_block) && $fee_block->service == 'every_month_percent') selected @endif value="every_month_percent">ежемесячно в % от суммы погашения</option>
                        <option @if(isset($fee_block) && $fee_block->service == 'one_time_percent') selected @endif value="one_time_percent">разово в % от кредита</option>
                        <option @if(isset($fee_block) && $fee_block->service == 'one_time_amount') selected @endif value="one_time_amount">разово в сумме</option>
                        <option @if(isset($fee_block) && $fee_block->service == 'not_less_then_amount') selected @endif value="not_less_then_amount">не менее</option>
                </select>
            </div>
            <div class="col-md-5">
                <label for="credit_props">Комиссия за обслуживание (текст)</label>
                <input type="text" class="form-control" name="credit_props[credit_fees][{{$prop_number}}][{{$fee_number}}][{{ 'service_input' }}]" placeholder="" value="@if(isset($fee_block) && !empty($fee_block->organization_input)) {{ $fee_block->organization_input }}@endif">
            </div>

            <div class="col-md-5">
                <label for="credit_props">Комиссия за обналичивание</label>
                <select name="credit_props[credit_fees][{{$prop_number}}][{{$fee_number}}][{{ 'monetisation' }}]" class="form-control">
                        <option @if(isset($fee_block) && $fee_block->monetisation == 'one_time_percent') selected @endif value="one_time_percent">разово в % от кредита</option>
                        <option @if(isset($fee_block) && $fee_block->monetisation == 'one_time_amount') selected @endif value="one_time_amount">разово в сумме</option>
                        <option @if(isset($fee_block) && $fee_block->monetisation == 'not_less_then_amount') selected @endif value="not_less_then_amount">не менее</option>
                </select>
            </div>
            <div class="col-md-5">
                <label for="credit_props">Комиссия за обналичивание (текст)</label>
                <input type="text" class="form-control" name="credit_props[credit_fees][{{$prop_number}}][{{$fee_number}}][{{ 'monetisation_input' }}]" placeholder="" value="@if(isset($fee_block) && !empty($fee_block->monetisation_input)) {{ $fee_block->monetisation_input }}@endif">
            </div>

            <div class="col-md-5">
                <label for="credit_props">Комиссия за зачисление на карту / счет</label>
                <select name="credit_props[credit_fees][{{$prop_number}}][{{$fee_number}}][{{ 'card_account_enrolment' }}]" class="form-control">
                        <option @if(isset($fee_block) && $fee_block->card_account_enrolment == 'one_time_percent') selected @endif value="one_time_percent">разово в % от кредита</option>
                        <option @if(isset($fee_block) && $fee_block->card_account_enrolment == 'one_time_amount') selected @endif value="one_time_amount">разово в сумме</option>
                        <option @if(isset($fee_block) && $fee_block->card_account_enrolment == 'not_less_then_amount') selected @endif value="not_less_then_amount">не менее</option>
                </select>
            </div>
            <div class="col-md-5">
                <label for="credit_props">Комиссия за зачисление на карту / счет (текст)</label>
                <input type="text" class="form-control" name="credit_props[credit_fees][{{$prop_number}}][{{$fee_number}}][{{ 'card_account_enrolment_input' }}]" placeholder="" value="@if(isset($fee_block) && !empty($fee_block->card_account_enrolment_input)) {{ $fee_block->card_account_enrolment_input }}@endif">
            </div>

            <div class="col-md-5">
                <label for="credit_props">Комиссия за выдачу кредита</label>
                <select name="credit_props[credit_fees][{{$prop_number}}][{{$fee_number}}][{{ 'granting' }}]" class="form-control">
                        <option @if(isset($fee_block) && $fee_block->granting == 'one_time_percent') selected @endif value="one_time_percent">разово в % от кредита</option>
                        <option @if(isset($fee_block) && $fee_block->granting == 'one_time_amount') selected @endif value="one_time_amount">разово в сумме</option>
                        <option @if(isset($fee_block) && $fee_block->granting == 'not_less_then_amount') selected @endif value="not_less_then_amount">не менее</option>
                </select>
            </div>
            <div class="col-md-5">
                <label for="credit_props">Комиссия за выдачу кредита (текст)</label>
                <input type="text" class="form-control" name="credit_props[credit_fees][{{$prop_number}}][{{$fee_number}}][{{ 'granting_input' }}]" placeholder="" value="@if(isset($fee_block) && !empty($fee_block->granting_input)) {{ $fee_block->granting_input }}@endif">
            </div>









            {{--@if(!isset($fee_block))--}}
                {{--@foreach($fees as $fee)--}}
                    {{--<div class="col-md-5">--}}
                        {{--<label for="credit_props">{{ $fee['control_title'] }}</label>--}}
                        {{--<select name="credit_props[credit_fees][{{$fee['prop_number']}}][{{$fee['fee_number']}}][{{ $fee['control_slug'] }}]" class="form-control">--}}
                            {{--@foreach($fee['controls'] as $control)--}}
                                {{--<option value="{{ $control['value'] }}">{{ $control['value_title'] }}</option>--}}
                            {{--@endforeach--}}
                        {{--</select>--}}
                    {{--</div>--}}
                    {{--<div class="col-md-5">--}}
                        {{--<label for="credit_props">{{ $fee['control_title'] }}</label>--}}
                        {{--<input type="text" class="form-control" name="credit_props[credit_fees][{{$fee['prop_number']}}][{{$fee['fee_number']}}][{{ $fee['control_slug'].'_input' }}]" placeholder="" value="">--}}
                    {{--</div>--}}

                    {{--<input type="hidden" value="@if(isset($credit->fee_id)){{$credit->fee_id}}@endif" name="credit_props[credit_fees][{{$fee['prop_number']}}][{{$fee['fee_number']}}]['fee_id']">--}}
                {{--@endforeach--}}
            {{--@else--}}
                {{--@foreach(\App\FeeType::where('product_type', 'credit')->get() as $fee)--}}
                    {{--<div class="col-md-5">--}}
                        {{--<label for="credit_props">{{ $fee['name_ru'] }}</label>--}}
                        {{--<select name="credit_props[credit_fees][{{$prop_number}}][{{$fee_number}}][{{ $fee['alt_name_ru'] }}]" class="form-control">--}}
                            {{--@foreach(\App\FeeValue::where('fee_type_id', $fee['id'])->get() as $control)--}}
                            {{--TODO: селектить если edit--}}


                                {{--<option @if($fee_block->fee_value_id == $control['id']) selected @endif value="{{ $control['value'] }}">{{ $control['name_ru'] }}</option>--}}

                                {{--@foreach($prop->fees as $prop_fee)--}}
                                    {{--<option @if($control['fee_type_id'] == $prop_fee['fee_type_id'] && $control['id'] == $prop_fee['fee_value_id']) selected @endif value="{{ $control['value'] }}">{{ $control['name_ru'] }}</option>--}}
                                {{--@endforeach--}}
                            {{--@endforeach--}}
                        {{--</select>--}}
                    {{--</div>--}}
                    {{--<div class="col-md-5">--}}
                        {{--<label for="credit_props">{{ $fee['name_ru'] }}</label>--}}
                        {{--@foreach(\App\FeeValue::where('fee_type_id', $fee['id'])->get() as $control)--}}
                            {{--@if($fee_block->fee_value_id == $control['id'])--}}
                                {{--<input type="text" class="form-control" name="credit_props[credit_fees][{{$prop_number}}][{{$fee_number}}][{{ $fee['alt_name_ru'].'_input' }}]" placeholder="" value="{{$fee_block->input}}">--}}
                                {{--@break--}}
                            {{--@else--}}
                                {{--<input type="text" class="form-control" name="credit_props[credit_fees][{{$prop_number}}][{{$fee_number}}][{{ $fee['alt_name_ru'].'_input' }}]" placeholder="" value="">--}}
                                {{--@break--}}
                            {{--@endif--}}
                            {{--<input type="text"  class="form-control" name="credit_props[credit_fees][{{$prop_number}}][{{$fee_number}}][{{ $fee['alt_name_ru'].'_input' }}]" placeholder="" value="@if($control['fee_type_id'] == $prop_fee['fee_type_id'] && $control['id'] == $prop_fee['fee_value_id']) {{$prop_fee['input']}} @endif">--}}

                        {{--@endforeach--}}

                        {{--<input type="text"  class="form-control" name="credit_props[credit_fees][{{$prop_number}}][{{$fee_number}}][{{ $fee['alt_name_ru'].'_input' }}]" placeholder="" value="@if($control['fee_type_id'] == $prop_fee['fee_type_id'] && $control['id'] == $prop_fee['fee_value_id']) {{$prop_fee['input']}} @endif">--}}
                    {{--</div>--}}

                    {{--<input type="hidden" value="@if(isset($credit->fee_id)){{$credit->fee_id}}@endif" name="credit_props[credit_fees][{{$fee['prop_number']}}][{{$fee['fee_number']}}]['fee_id']">--}}
                {{--@endforeach--}}
            {{--@endif--}}

        </div>

    </div>

    <script>
        $(function () {

            function delete_fee_button(id, block) {
                var data = {
                    url: '/admin/ajax/del-fee-block',
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

            $('.btn-delete-fee').on('click', function (e) {
                e.preventDefault();

                var panel = $(this).parents('.panel-fee');
                delete_fee_button($(this).data('id'), panel);
            })
        })
    </script>
</div>
