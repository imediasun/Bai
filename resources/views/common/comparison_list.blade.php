
<table>
    <tr>
        <th>Продукт</th>
        @foreach($products as $product)
            <td class="compare_list_item_{{ $product->id }}">
                <div class="compare_head">
                    <div class="img">
                        <img src="{{ asset($product->logo)  }}" width="128" height="47" alt=""/>
                    </div>
                    <!-- end .img-->
                    <a href="{{ $product }}/{{$product->alt_name_ru}}">{{ $product->name_ru }}</a>
                    <div class="delete">
                        <span onclick="removeFromCompareList({{ $product->id }})" class="ic ic_trash"></span>
                    </div>
                    <!-- end .delete-->
                </div>
                <!-- end .compare_head-->
            </td>
        @endforeach
    </tr>
    {{--<tr>--}}
        {{--<th>ГЭСВ</th>--}}
        {{--@foreach($products as $product)--}}
            {{--<td class="compare_list_item_{{ $product->id }}">{{ $product->gesv }}%--}}
            {{--</td>--}}
        {{--@endforeach--}}
    {{--</tr>--}}
    <tr>
        <th>Сумма</th>
        @foreach($products as $product)
            <td class="compare_list_item_{{ $product->id }}">{{ $product->amount }}{{ $product->currency }}
            </td>
        @endforeach
    </tr>
    <tr>
        <th>Процентная ставка</th>
        @foreach($products as $product)
            <td class="compare_list_item_{{ $product->id }}">{{ $product->percent_rate }}%
            </td>
        @endforeach
    </tr>
    <tr>
        <th>Платеж</th>
        @foreach($products as $product)
            <td class="compare_list_item_{{ $product->id }}">{{ $product->ppm }}{{ $product->currency }}
            </td>
        @endforeach
    </tr>
    <tr>
        <th>Переплата</th>
        @foreach($products as $product)
            <td class="compare_list_item_{{ $product->id }}">{{ $product->overpay }}{{ $product->currency }}
            </td>
        @endforeach
    </tr>
    {{--<tr>--}}
        {{--<th>Валюта</th>--}}
        {{--@foreach($products as $product)--}}
            {{--<td class="compare_list_item_{{ $product->id }}">{{ $product->currency }}--}}
            {{--</td>--}}
        {{--@endforeach--}}
    {{--</tr>--}}
    <tr>
        <th>Возраст</th>
        @foreach($products as $product)
            <td class="compare_list_item_{{ $product->id }}">{{ $product->age }}
            </td>
        @endforeach
    </tr>
    {{--<tr>--}}
        {{--<th>Досрочное погашение</th>--}}
        {{--@foreach($products as $product)--}}
            {{--<td class="compare_list_item_{{ $product->id }}">{{ $product->have_early_repayment }}--}}
            {{--</td>--}}
        {{--@endforeach--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<th>Зарплатный проект</th>--}}
        {{--@foreach($products as $product)--}}
            {{--<td class="compare_list_item_{{ $product->id }}">{{ \App\Bank::find($product->gesv) != null ? \App\Bank::find($product->gesv)->name_ru : '' }}--}}
            {{--</td>--}}
        {{--@endforeach--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<th>Гражданство</th>--}}
        {{--@foreach($products as $product)--}}
            {{--<td class="compare_list_item_{{ $product->id }}">{{ $product->credit->citizenship }}--}}
            {{--</td>--}}
        {{--@endforeach--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<th>Категории заемщиков</th>--}}
        {{--@foreach($products as $product)--}}
            {{--<td class="compare_list_item_{{ $product->id }}">{{ $product->credit->debtor_category }}%--}}
            {{--</td>--}}
        {{--@endforeach--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<th>Комиссия за выдачу займа</th>--}}
        {{--@foreach($products as $product)--}}
            {{--<td class="compare_list_item_{{ $product->id }}">{{ $product->granting  }}%--}}
            {{--</td>--}}
        {{--@endforeach--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<th>Комиссия за обналичивание</th>--}}
        {{--@foreach($products as $product)--}}
            {{--<td class="compare_list_item_{{ $product->id }}">{{ $product->gesv }}%--}}
            {{--</td>--}}
        {{--@endforeach--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<th>Комиссия за обслуживание кредита</th>--}}
        {{--@foreach($products as $product)--}}
            {{--<td class="compare_list_item_{{ $product->id }}">{{ $product->gesv }}%--}}
            {{--</td>--}}
        {{--@endforeach--}}
    {{--</tr>--}}
    {{--<tr>--}}
        {{--<th>Комиссия за рассмотрение</th>--}}
        {{--@foreach($products as $product)--}}
            {{--<td class="compare_list_item_{{ $product->id }}">{{ $product->gesv }}%--}}
            {{--</td>--}}
        {{--@endforeach--}}
    {{--</tr>--}}
    <tr>
        <th>Цель кредита</th>
        @foreach($products as $product)
            <td class="compare_list_item_{{ $product->id }}">{{ $product->credit_goal }}
            </td>
        @endforeach
    </tr>
    {{--<tr>--}}
        {{--<th>Досрочное погашение</th>--}}
        {{--@foreach($products as $product)--}}
            {{--<td class="compare_list_item_{{ $product->id }}">{{ $product->have_early_repayment ? 'да' : 'нет' }}--}}
            {{--</td>--}}
        {{--@endforeach--}}
    {{--</tr>--}}
    <tr>
        <th>Схема погашения</th>
        @foreach($products as $product)
            <td class="compare_list_item_{{ $product->id }}">{{ $product->repayment_structure == 'ann' ? 'аннуитетная' : 'дифференцированная' }}
            </td>
        @endforeach
    </tr>
    {{--<tr>--}}
        {{--<th>Пролонгация</th>--}}
        {{--@foreach($products as $product)--}}
            {{--<td class="compare_list_item_{{ $product->id }}">{{ $product->have_prolongation }}--}}
            {{--</td>--}}
        {{--@endforeach--}}
    {{--</tr>--}}
    <tr>
        <th>Документы</th>
        @foreach($products as $product)
            <td class="compare_list_item_{{ $product->id }}">{!! $product->docs_ru !!}
            </td>
        @endforeach
    </tr>
    <tr>
        <th>Обеспечение</th>
        @foreach($products as $product)
            <td class="compare_list_item_{{ $product->id }}">{{ $product->credit_security }}
            </td>
        @endforeach
    </tr>
    <tr>
        <th>Кредитная история</th>
        @foreach($products as $product)
            <td class="compare_list_item_{{ $product->id }}">{{ $product->credit_history }}
            </td>
        @endforeach
    </tr>
    {{--<tr>--}}
        {{--<th>Наличие мобильного номера</th>--}}
        {{--@foreach($products as $product)--}}
            {{--<td class="compare_list_item_{{ $product->id }}">{{ $product->have_mobile_phone }}--}}
            {{--</td>--}}
        {{--@endforeach--}}
    {{--</tr>--}}
    <tr>
        <th>Оформление кредита</th>
        @foreach($products as $product)
            <td class="compare_list_item_{{ $product->id }}">{{ $product->credit_formalization }}
            </td>
        @endforeach
    </tr>
    <tr>
        <th>Подтверждение дохода</th>
        @foreach($products as $product)
            <td class="compare_list_item_{{ $product->id }}">{{ $product->income_confirmation }}
            </td>
        @endforeach
    </tr>
    <tr>
        <th>Способ оплаты</th>
        @foreach($products as $product)
            <td class="compare_list_item_{{ $product->id }}">{{ $product->method_of_repayment_ru }}
            </td>
        @endforeach
    </tr>
    <tr>
        <th class="empty"></th>
        @foreach($products as $product)
            <td class="compare_list_item_{{ $product->id }}">
                <div class="btn_hold">
                    <a class="btn btn_round btn_orange" href="#">
                        <span>Оформить<span class="hide_mob"> кредит</span></span>
                    </a>
                </div>
            </td>
        @endforeach
    </tr>
</table>