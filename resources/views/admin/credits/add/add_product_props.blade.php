<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Доп. свойства продукта [{{$val}}]</h3>
        <div class="panel-actions">
            <a class="panel-action voyager-angle-down" data-toggle="panel-collapse" aria-hidden="true"></a>
            <button class="btn btn-danger btn-delete_prop" data-id="@if(isset($prop->id)){{$prop->id}}@endif">x</button>
        </div>
    </div>
    <div class="panel-body">

        <div class="row">
            <div class="col-md-3">
                <label for="credit_props">Мин сумма</label>
                <input type="text" class="form-control" name="credit_props[min_amount][{{$val}}]" placeholder="Сумма от" value="@if(isset($prop->min_amount)){{ $prop->min_amount }}@endif">
            </div>
            <div class="col-md-3">
                <label for="credit_props">Макс сумма</label>
                <input type="text" class="form-control" name="credit_props[max_amount][{{$val}}]" placeholder="Сумма до" value="@if(isset($prop->max_amount)){{ $prop->max_amount }}@endif">
            </div>

            <div class="col-md-3">
                <label for="credit_props">Мин cрок</label>
                <input type="text" class="form-control" name="credit_props[min_period][{{$val}}]" placeholder="Срок от" value="@if(isset($prop->min_period)){{ $prop->min_period }}@endif">
            </div>
            <div class="col-md-3">
                <label for="credit_props">Макс cрок</label>
                <input type="text" class="form-control" name="credit_props[max_period][{{$val}}]" placeholder="Срок до" value="@if(isset($prop->max_period)){{ $prop->max_period }}@endif">
            </div>
        </div>

        <div class="row">
            <div class="col-md-1">
                <label for="credit_props">Ставка</label>
                <input type="text" class="form-control" name="credit_props[percent_rate][{{$val}}]" placeholder="%" value="@if(isset($prop->percent_rate)){{ $prop->percent_rate }}@endif">
            </div>
            <div class="col-md-2">
                <label for="credit_props">Валюта</label>
                <select name="credit_props[currency][{{$val}}]" class="form-control">
                    <option @if(isset($prop->currency) && $prop->currency == 'usd' ) selected @endif value="usd">USD</option>
                    <option @if(isset($prop->currency) && $prop->currency == 'eur' ) selected @endif value="eur">EUR</option>
                    <option @if(isset($prop->currency) && $prop->currency == 'rub' ) selected @endif value="rub">RUB</option>
                    <option @if(isset($prop->currency) && $prop->currency == 'kzt' ) selected @endif value="kzt">KZT</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="credit_props">Подтвержение дохода</label>
                <select name="credit_props[income_confirmation][{{$val}}]" class="form-control">
                    <option @if(isset($prop->income_confirmation) && $prop->income_confirmation == '0' ) selected @endif value="0">Без подтверждения доходов</option>
                    <option @if(isset($prop->income_confirmation) && $prop->income_confirmation == '1' ) selected @endif value="1">С подтверждением доходов</option>
                </select>
            </div>

        {{--</div>--}}

        {{--<div class="row">--}}

            <div class="col-md-2">
                <label for="credit_props">Схема погашения</label>
                <select name="credit_props[repayment_structure][{{$val}}]" class="form-control">
                    <option @if(isset($prop->repayment_structure) && $prop->repayment_structure == 'ann' ) selected @endif value="ann">аннуитетная</option>
                    <option @if(isset($prop->repayment_structure) && $prop->repayment_structure == 'diff' ) selected @endif value="diff">дифференцированная</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="credit_props">Обеспечение</label>
                <select name="credit_props[credit_security][{{$val}}]" class="form-control">
                    <option @if(isset($prop->credit_security) && $prop->credit_security == 'none' ) selected @endif value="none">без залога и поручительства</option>
                    <option @if(isset($prop->credit_security) && $prop->credit_security == 'guarantor' ) selected @endif value="guarantor">поручитель</option>
                    <option @if(isset($prop->credit_security) && $prop->credit_security == 'deposit' ) selected @endif value="deposit">залог - депозит</option>
                    <option @if(isset($prop->credit_security) && $prop->credit_security == 'immovables_current' ) selected @endif value="immovables_current">залог - имеющееся недвижимость</option>
                    <option @if(isset($prop->credit_security) && $prop->credit_security == 'immovables_bying' ) selected @endif value="immovables_bying">залог - приобретемая недвижимость</option>
                    <option @if(isset($prop->credit_security) && $prop->credit_security == 'auto_current' ) selected @endif value="auto_current">залог - имеющееся авто</option>
                    <option @if(isset($prop->credit_security) && $prop->credit_security == 'auto_buying' ) selected @endif value="auto_buying">залог - приобретаемое авто</option>
                    <option @if(isset($prop->credit_security) && $prop->credit_security == 'money' ) selected @endif value="money">залог - денежные средства</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="credit_props">Возраст</label>
                <select name="credit_props[age][{{$val}}]" class="form-control">
                    <option @if(isset($prop->age) && $prop->age == '18'){{ 'selected' }}@endif value="18">от 18 лет</option>
                    <option @if(isset($prop->age) && $prop->age == '23-63'){{ 'selected' }}@endif value="23-63">от 23 до 63 лет</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="credit_props">Зарплатный проект</label>
                <select name="credit_props[income_project][{{$val}}]" class="select2">
                    <option value=""></option>
                    @foreach($banks as $bank)
                        <option @if(isset($prop->income_project) && $prop->income_project == $bank->id){{ 'selected="selected"' }}@endif value="{{$bank->id}}">{{$bank->name_ru}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="credit_props">Тип клиента</label>
                <select name="credit_props[client_type][{{$val}}]" class="select2">
                    <option @if(isset($prop->client_type) && $prop->client_type == 'standard'){{ 'selected="selected"' }}@endif value="standard">стандарт</option>
                    <option @if(isset($prop->client_type) && $prop->client_type == 'vip'){{ 'selected="selected"' }}@endif value="vip">vip</option>
                    <option @if(isset($prop->client_type) && $prop->client_type == 'vip_elite'){{ 'selected="selected"' }}@endif value="vip_elite">vip-elite</option>
                </select>
            </div>
        </div>
        <input type="hidden" value="@isset($prop->id){{$prop->id}}@endisset" name="credit_props[id][{{$val}}]">

        {{--<textarea class="form-control" name="credit_props[Credit][]">@if (isset($prop->credit_props)){{ $prop->credit_props }}@endif</textarea>--}}
    </div>
    @if(isset($dataTypeContent->id) && count($prop->fees) > 0)
        @foreach($prop->fees as $fee_block)
            @php session(['fees_cnt' => $fee_block->id]); @endphp
            @include('admin.credits.add.add_product_props_fees', ['fee_number' => $fee_block->id, 'prop_number' => $prop->id])
        @endforeach
    @endif
    <button class="btn btn-info btn-add-fees">+ комиссии</button>
    <script>
        $(function () {

            $('.select2').select2({ dropdownAutoWidth: true, width: '100%', });

            function delete_prop_button(id, block) {
                var data = {
                    url: '/admin/ajax/del-prop-block',
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

            function add_fee_button(btn) {
                var data = {
                    url: '/admin/ajax/get-fee-block'
                };

                $.ajax({
                    url: data.url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    method: 'post',
                    success:function (response) {
                        $(btn).before(response);
                    },
                    error:function (error) {
                        console.dir(error);
                    }
                });
            }

            $('.btn-add-fees').on('click', function (e) {
                e.preventDefault();

                add_fee_button(this);
            });

            $('.btn-delete_prop').on('click', function (e) {
                e.preventDefault();

                var panel = $(this).parents('.panel');
                delete_prop_button($(this).data('id'), panel);
            });
        })
    </script>
</div>
{{--<button class="btn btn-danger btn-add-props">+ Доп свойства</button>--}}
