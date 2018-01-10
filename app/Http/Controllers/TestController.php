<?php

namespace App\Http\Controllers;

use App\Credit;
use App\CreditProp;
use App\CreditPropFee;
use App\Fee;
use App\FeeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function fee_test()
    {
        $this->handle();
    }

    public function handle()
    {
        Credit::truncate();
        CreditProp::truncate();
        CreditPropFee::truncate();
        $this->migrate();
//        $this->fill_credit_props_parent();
//        $this->fill_income_confirmation();
        $this->fill_insurance();
    }

    private function migrate()
    {
        $query = "SELECT DISTINCT c.id as cr_id
            FROM credit_old c
            ORDER BY c.id";

        $query_limit = "SELECT
          c.id as cr_id,
          c.parent_id as cr_parent,
          c.name as credit_name,
          c.alt_name as credit_alt_name,
          c.bank_id as bank_id,
          c.meta_title as cr_meta_title,
          c.meta_description as cr_meta_description,
          c.breadcrumbs as cr_breadcrumbs,
          c.h1 as cr_h1,
          c.short_description as cr_short_description,
          c.description as cr_description,
          c.promo as cr_promo,
          c.online_url as cr_online_url,
          cp.value_from as value_from,
          cp.value_to as value_to,
          cp.value_unit as value_unit,
          cp.description as description,
          pr.name as prop_name,
          po.name as option_name,
          po.value_from as option_value_from,
          po.value_to as option_value_to,
          po.value_unit as option_value_unit
          FROM credit_old c
            JOIN credit_prop_old cp on c.id = cp.credit_id
            LEFT JOIN prop_old pr on cp.prop_id = pr.id
            LEFT JOIN prop_option_old po on cp.prop_option_id = po.id
        WHERE c.id = :id
        ORDER BY c.id, pr.name";

        $credits_old = DB::select($query);

        // бежим по всем старым кредитам (group by credit.id)
        foreach ($credits_old as $old_credit_upper) {

            // забираем все свойства по id кредита из верхнего селекта
            $credits_old_where = DB::select($query_limit, ['id' => $old_credit_upper->cr_id]);

            $credit_prop = [];
            $credit_fees = [];
            $credit_arr = [];

            foreach ($credits_old_where as $old_credit) {

                //если это дочерний кредит, ищем его материнский кредит, забираем его props дочернего
                if($old_credit->cr_parent != null){
                    // ищем материнский кредит
                    $parent = DB::table('credit_old')->where('id', $old_credit->cr_parent)->first();
                    // если материнский кредит в старых кредитах существует, имеет смысл продолжать
                    if($parent){
                        $credit_new = Credit::where('name_ru', $parent->name)->first();

                        //если материнский кредит уже есть в новой таблице, имеет смысл продолжать
                        //заполняем его свойства и комиссии
                        if($credit_new){

                            $credit_prop = $this->fill_props($old_credit, $credit_prop);
//                            $credit_fees = $this->fill_fees($old_credit, $credit_fees);
                            $credit_fees = $this->fill_fees2($old_credit, $credit_fees);

                        }
                    }
                }
                //если это материнский кредит, забираем его props и credit
                else{
                    $credit_prop = $this->fill_props($old_credit, $credit_prop);
//                    $credit_fees = $this->fill_fees($old_credit, $credit_fees);
                    $credit_fees = $this->fill_fees2($old_credit, $credit_fees);
                    $credit_arr = $this->fill_credit($old_credit, $credit_arr);
                }
            }

            // сохраняем свойства кредита
            //если это материнский кредит, то создаем новую запись
            if(!empty($credit_arr)){

                $credit = Credit::create($credit_arr);

                $credit_prop['credit_id'] = $credit->id;
                //сохраняем prop
                $credit_prop = CreditProp::create($credit_prop);

                if(!empty($credit_fees)){
                    $credit_fees['credit_id'] = $credit->id;
                    $credit_fees['credit_prop_id'] = $credit_prop->id;
                    $credit_fees['created_by'] = 1;
                    $credit_fees['changed_by'] = 1;
                    //сохраняем fees
//                    $credit_prop_fee = CreditPropFee::create($credit_fees);
                    $credit_prop_fee = CreditPropFee::create($credit_fees);
                }
            }
            else{
                if(isset($credit_new)){
                    $credit_prop['credit_id'] = $credit_new->id;
                    //сохраняем prop
                    $credit_prop = CreditProp::create($credit_prop);

                    if(!empty($credit_fees)){
                        $credit_fees['credit_id'] = $credit_new->id;
                        $credit_fees['credit_prop_id'] = $credit_prop->id;
                        $credit_fees['created_by'] = 1;
                        $credit_fees['changed_by'] = 1;
                        //сохраняем fees
//                        $credit_prop_fee = CreditPropFee::create($credit_fees);
                        $credit_prop_fee = CreditPropFee::create($credit_fees);
                    }
                }
            }

        }


    }

    private function fill_props($old_credit, $credit_prop = [])
    {
        $old_new_credit_props = [
            'Валюта' => [
                'new_name' => 'currency',
                'need_comment_field' => 'currency_comment',
                'option_name' => true,
                'value_from' => false,
                'value_to' => false,
                'transform' => []
            ],

            'Зарплатный проект' => [
                'new_name' => 'income_project',
                'need_comment_field' => '',
                'new_name2' => null,
                'option_name' => true,
                'description' => false,
                'value_from' => false,
                'value_to' => false,
                'transform' => [
                    'altyn-bank' => 31,
                    'казком' => 19,
                    'народный банк' => 47,
                    'центркредит' => 23,
                ]
            ],

            'Срок' => [
                'new_name' => 'min_period|max_period',
                'need_comment_field' => 'period_comment',
                'option_name' => false,
                'value_from' => true,
                'value_to' => true,
                'transform' => []
            ],

            'Возраст' => [
                'new_name' => 'age',
                'need_comment_field' => 'age_comment',
                'option_name' => true,
                'value_from' => false,
                'value_to' => false,
                'transform' => [
                    'от 18 лет' => '18',
                    'От 21 до 58 лет' => '18',
                    'от 18 до 65' => '18',
                    '20-58/63' => '18',
                    'от 21 до 65' => '23-64',
                    'от 21 до 63 лет' => '23-64',
                    'от 25 до 63 лет' => '23-64',
                    'от 22 до 63 лет' => '23-64',
                    'от 23 до 64 лет' => '23-64',
                    'предельный возраст - до 70 лет на момент погашения' => '23-64',
                    'от 21 до 67 лет' => '23-64',
                    'от 18 до 75 лет' => '23-64',
                    'от 21 до 70 лет' => '23-64',
                    'от 20 до 63' => '23-64',
                    'от 23 до 63 лет' => '23-64',
                ]

            ],
            'Период' => [
                'new_name' => 'min_period|max_period',
                'need_comment_field' => '',
                'option_name' => false,
                'value_from' => true,
                'value_to' => true,
                'transform' => [],
            ],
            'Сумма' => [
                'new_name' => 'min_amount|max_amount',
                'need_comment_field' => 'amount_comment',
                'option_name' => false,
                'value_from' => true,
                'value_to' => true,
                'transform' => [],
            ],

            'Схема погашения' => [
                'new_name' => 'repayment_structure',
                'need_comment_field' => 'repayment_structure_comment',
                'option_name' => true,
                'value_from' => false,
                'value_to' => false,
                'transform' => [
                    'аннуитетная (ежемесячно равными платежами)' => 'ann',
                    'дифференцированная' => 'diff',
                ],
            ],

            'Обеспечение' => [
                'new_name' => 'credit_security',
                'need_comment_field' => 'credit_security_comment',
                'option_name' => true,
                'value_from' => false,
                'value_to' => false,
                'transform' => [
                    'без залога и поручительства' => 'without-security',
                    'залог - имеющеяся недвижимость' => 'immovables_current',
                    'залог - приобретаемая недвижимость' => 'immovables_bying',
                    'залог - имеющееся авто' => 'auto_current',
                    'залог - депозит' => 'deposit',
                ],
            ],

            'Процентная ставка' => [
                'new_name' => 'percent_rate|null',
                'need_comment_field' => 'percent_rate_comment',
                'option_name' => false,
                'value_from' => true,
                'value_to' => false,
                'transform' => [],
            ],

            'Подтверждение дохода' => [
                'new_name' => 'income_confirmation',
                'need_comment_field' => 'income_confirmation_comment',
                'option_name' => true,
                'value_from' => false,
                'value_to' => false,
                'transform' => [
                    0 => false,
                    1 => true,
                ],
            ],
        ];
        //если такое условие существует в массиве $old_new_credit_props
        if(isset($old_new_credit_props[$old_credit->prop_name])){

            // берем массив для удобства из $old_new_credit_props
            $arr_item = $old_new_credit_props[$old_credit->prop_name];

            // откуда брать данные: option_name / value_from / value_to
            if($arr_item['option_name']){

                // если данные надо преобразовать в новый тип:
                // C подтверждением доходов => 1
                // Без подтверждения доходов => 0
                if(!empty($arr_item['transform'])){
                    //если в массиве transform есть такой ключ: "C подтверждением доходов"
                    if(isset($arr_item['transform'][mb_strtolower($old_credit->option_name)])){

                        if(!isset($credit_prop[$arr_item['new_name']]))
                        $credit_prop[$arr_item['new_name']] = $arr_item['transform'][trim(mb_strtolower($old_credit->option_name))];
                    }
                }
                else{
                    // преобразование не требуется, сохраняем как есть
                    // свойство: currency => USD
                    if($old_credit->option_name != null){
                        $credit_prop[$arr_item['new_name']] = mb_strtolower($old_credit->option_name);
                    }
                }
            }
            // value_from / value_to
            else{
                // разбиваем min|max по |
                list($min_value, $max_value) = explode('|',$arr_item['new_name']);

                $cleared = $old_credit->value_from;
                $cleared = str_replace([','], '.', $cleared);
                $cleared = str_replace(['%', 'от', ' '], '', $cleared);
                $cleared = str_replace([' '], '', $cleared);

                $credit_prop[$min_value] = $cleared;

                if($max_value != 'null'){
                    $credit_prop[$max_value] = $old_credit->value_to;
                }
            }

            if(!empty($arr_item['need_comment_field'])){
                $credit_prop[$arr_item['need_comment_field']] = trim($old_credit->description);
            }
        }

        return $credit_prop;
    }

    private function fill_credit($old_credit, $credit_arr = [])
    {

        $credit_arr['bank_id'] = $old_credit->bank_id;
        $credit_arr['name_ru'] = $old_credit->credit_name;
        $credit_arr['name_kz'] = $old_credit->credit_name;
        $credit_arr['alt_name_ru'] = $old_credit->credit_alt_name;
        $credit_arr['alt_name_kz'] = $old_credit->credit_alt_name;
        $credit_arr['short_description_ru'] = $old_credit->cr_short_description;
        $credit_arr['short_description_kz'] = $old_credit->cr_short_description;
        $credit_arr['short_description_kz'] = $old_credit->cr_short_description;
        $credit_arr['description_ru'] = $old_credit->cr_description;
        $credit_arr['description_kz'] = $old_credit->cr_description;
        $credit_arr['online_url'] = $old_credit->cr_online_url;
        $credit_arr['promo'] = $old_credit->cr_promo;
        $credit_arr['is_approved'] = true;
        $credit_arr['breadcrumbs_ru'] = $old_credit->cr_breadcrumbs;
        $credit_arr['breadcrumbs_kz'] = $old_credit->cr_breadcrumbs;
        $credit_arr['meta_title_ru'] = $old_credit->cr_meta_title;
        $credit_arr['meta_title_kz'] = $old_credit->cr_meta_title;
        $credit_arr['created_by'] = 1;
        $credit_arr['changed_by'] = 1;
        $credit_arr['sort_order'] = 10;
        $credit_arr['h1_ru'] = $old_credit->cr_h1;
        $credit_arr['h1_kz'] = $old_credit->cr_h1;
        $credit_arr['meta_description_ru'] = $old_credit->cr_meta_description;
        $credit_arr['meta_description_kz'] = $old_credit->cr_meta_description;

        $old_new_credit = [
            'Минимальный официальный доход' => [
                'new_name' => 'minimum_income',
                'need_comment_field' => 'minimum_income_comment',
                'new_name2' => null,
                'option_name' => false,
                'description' => false,
                'value_from' => true,
                'value_to' => false,
                'transform' => []
            ],

            'Страхование' => [
                'new_name' => 'insurance_input',
                'need_comment_field' => '',
                'new_name2' => null,
                'description' => true,
                'option_name' => false,
                'value_from' => false,
                'value_to' => false,
                'transform' => [],
//                'transform' => [
//                    'Не требуется' => null,
//                    'разово в %' => 'one_time_percent',
//                    'разово в тенге' => 'one_time_amount',
//                    'не менее' => 'not_less_then_amount',
//                    'не обязательно' => null,
//                    'Не важно' => null,
//                ],
            ],

            'ГЭСВ' => [
                'new_name' => 'gesv',
                'need_comment_field' => 'gesv_comment',
                'new_name2' => null,
                'option_name' => false,
                'description' => false,
                'value_from' => true,
                'value_to' => false,
                'transform' => []
            ],

            'Общий стаж работы' => [
                'new_name' => 'occupational_life',
                'need_comment_field' => 'occupational_life_comment',
                'new_name2' => null,
                'option_name' => false,
                'description' => false,
                'value_from' => true,
                'value_to' => true,
                'transform' => []
            ],

            'Стаж на текущем месте работы' => [
                'new_name' => 'occupational_current',
                'need_comment_field' => '',
                'new_name2' => null,
                'option_name' => true,
                'description' => false,
                'value_from' => false,
                'value_to' => false,
                'transform' => [
                    'до 3 дней' => 3,
                    'до 7 дней' => 7,
                    'до 14 дней' => 14,
                ]
            ],

            'Способ оплаты' => [
                'new_name' => 'method_of_repayment_ru',
                'new_name2' => 'method_of_repayment_kz',
                'need_comment_field' => '',
                'option_name' => false,
                'description' => true,
                'value_from' => false,
                'value_to' => false,
                'transform' => [],
//                'transform' => [
//                    'в отделении банка' => 3,
//                    'через терминалы банка' => 7,
//                    'интернет-банкинг' => 14,
//                ]
            ],

            'Постоянный доход' => [
                'new_name' => 'have_constant_income',
                'need_comment_field' => '',
                'new_name2' => null,
                'option_name' => true,
                'description' => false,
                'value_from' => false,
                'value_to' => false,
                'transform' => [
                    'обязательно' => true,
                    'не обязательно' => false,
                ]
            ],

            'Наличие мобильного номера' => [
                'new_name' => 'have_mobile_phone',
                'need_comment_field' => '',
                'new_name2' => null,
                'option_name' => true,
                'description' => false,
                'value_from' => false,
                'value_to' => false,
                'transform' => [
                    'обязательно' => true,
                    'не обязательно' => false,
                ]
            ],

            'Наличие рабочего номера' => [
                'new_name' => 'have_work_phone',
                'need_comment_field' => '',
                'new_name2' => null,
                'option_name' => true,
                'description' => false,
                'value_from' => true,
                'value_to' => true,
                'transform' => [
                    'обязательно' => true,
                    'не обязательно' => false,
                ]
            ],

            'Досрочное погашение' => [
                'new_name' => 'have_early_repayment',
                'need_comment_field' => 'have_early_repayment_comment',
                'new_name2' => null,
                'option_name' => true,
                'description' => false,
                'value_from' => false,
                'value_to' => false,
                'transform' => [
                    'есть' => true,
                    'нет' => false,
                ]
            ],

            'Пролонгация' => [
                'new_name' => 'have_prolongation',
                'need_comment_field' => '',
                'new_name2' => null,
                'option_name' => true,
                'description' => false,
                'value_from' => false,
                'value_to' => false,
                'transform' => [
                    'есть' => true,
                    'нет' => false,
                ]
            ],

            'Гражданство' => [
                'new_name' => 'have_citizenship',
                'need_comment_field' => '',
                'new_name2' => null,
                'option_name' => true,
                'description' => false,
                'value_from' => false,
                'value_to' => false,
                'transform' => [
                    'резидент' => true,
                    'не резидент' => false,
                    'не важно' => null,
                ]
            ],

//            'Способы погашение' => [
//                'new_name' => 'method_of_repayment_ru',
//                'new_name2' => null,
//                'option_name' => true,
//                'description' => false,
//                'value_from' => false,
//                'value_to' => false,
//                'transform' => []
//            ],

            'Цель кредита' => [
                'new_name' => 'credit_goal',
                'need_comment_field' => '',
                'new_name2' => null,
                'option_name' => true,
                'description' => false,
                'value_from' => false,
                'value_to' => false,
                'transform' => [
                    'любая' => 'any',
                    'неотложные нужды' => 'emergency_needs',
                    'просто деньги' => 'just_money',
                    'товары' => 'goods',
                    'бизнес' => 'business',
                    'рефинансирование' => 'refinancing',
                    'лечение' => 'medication',
                    'образование' => 'education',
                    'путешествие' => 'traveling',
                ]
            ],

            'Способ получение' => [
                'new_name' => 'receive_mode',
                'need_comment_field' => '',
                'new_name2' => null,
                'option_name' => true,
                'description' => false,
                'value_from' => false,
                'value_to' => false,
                'transform' => [
                    'наличными' => 'cash',
                    'на банковскую карту' => 'bank_card',
                    'на банковский счет' => 'bank_account',
                ]
            ],

            'Регистрация' => [
                'new_name' => 'registration',
                'need_comment_field' => '',
                'new_name2' => null,
                'option_name' => true,
                'description' => false,
                'value_from' => false,
                'value_to' => false,
                'transform' => [
                    'постоянная в районе обращения' => 'const_in_area',
                    'постоянная' => 'const',
                ]
            ],

            'Срок рассмотрения' => [
                'new_name' => 'time_for_consideration',
                'need_comment_field' => 'time_for_consideration_comment',
                'new_name2' => null,
                'option_name' => true,
                'description' => false,
                'value_from' => false,
                'value_to' => false,
                'transform' => [
                    'в день обращения' => 1,
                    'до 3 дней' => 3,
                    'до 7 дней' => 7,
                    'до 14 дней' => 14,
                    '1 день' => 1,
                    '3 дня' => 3,
                ]
            ],

            'Кредитная история' => [
                'new_name' => 'credit_history',
                'need_comment_field' => '',
                'new_name2' => null,
                'option_name' => true,
                'description' => false,
                'value_from' => false,
                'value_to' => false,
                'transform' => [
                    'положительная кредитная история' => 'positive',
                    'отрицательная кредитная история' => 'negative',
                ]
            ],

            'Оформление кредита' => [
                'new_name' => 'credit_formalization',
                'need_comment_field' => '',
                'new_name2' => null,
                'option_name' => true,
                'description' => false,
                'value_from' => false,
                'value_to' => false,
                'transform' => [
                    'Онлайн заявка' => 'online',
                    'В отделении банка' => 'office',
                    'в отделении банка или он-лайн заявка' => 'both',
                ]
            ],

            'Документы' => [
                'new_name' => 'docs_ru',
                'new_name2' => 'docs_kz',
                'need_comment_field' => '',
                'option_name' => false,
                'description' => true,
                'value_from' => false,
                'value_to' => false,
                'transform' => []
            ],

            'Прочие требования' => [
                'new_name' => 'other_claims_ru',
                'new_name2' => 'other_claims_kz',
                'need_comment_field' => '',
                'option_name' => false,
                'description' => true,
                'value_from' => false,
                'value_to' => false,
                'transform' => []
            ],

            'Категории заемщиков' => [
                'new_name' => 'debtor_category',
                'need_comment_field' => '',
                'new_name2' => null,
                'option_name' => true,
                'description' => false,
                'value_from' => false,
                'value_to' => false,
                'transform' => [
                    'Работники по найму' => 'employee',
                    'Индивидуальные предприниматели' => 'one_man_business',
                    'Владельцы или совладельцы собственного бизнеса' => 'business_owners',
                    'Работники бюджетной сферы / госслужащие' => 'civil_service',
                    'Владельцы личных подсобных хозяйств' => 'farm_owners',
                    'Работники правоохранительных органов' => 'law_enforcements',
                    'Адвокаты / Нотариусы' => 'lawyers',
                    'Пенсионеры' => 'pensioner',
                    'Военнослужащие' => 'military',
                    'Семья' => 'family',
                    'Молодая семья' => 'new_family',
                    'не имеет значения' => null,
                ]
            ],

        ];
        $receive_mode = [];

        if(isset($old_new_credit[$old_credit->prop_name])){

            // берем массив для удобства из $old_new_credit
            $arr_item = $old_new_credit[$old_credit->prop_name];

            // откуда брать данные: option_name / value_from / value_to
            if($arr_item['option_name']){

                // если данные надо преобразовать в новый тип:
                // C подтверждением доходов => 1
                // Без подтверждения доходов => 0
                if(!empty($arr_item['transform'])){
                    //если в массиве transform есть такой ключ: "C подтверждением доходов"
                    if(isset($arr_item['transform'][mb_strtolower($old_credit->option_name)])){

                        if($arr_item['new_name'] == 'receive_mode'){
                            $receive_mode[] = $arr_item['transform'][mb_strtolower($old_credit->option_name)];
//                            $credit_arr[$arr_item['new_name']][] = $arr_item['transform'][mb_strtolower($old_credit->option_name)];
                        }
                        else{
                            $credit_arr[$arr_item['new_name']] = $arr_item['transform'][mb_strtolower($old_credit->option_name)];
                        }
                        if($arr_item['new_name2'] != null){
                            $credit_arr[$arr_item['new_name2']] = $arr_item['transform'][mb_strtolower($old_credit->option_name)];
                        }
                    }
                }
                else{
                    // преобразование не требуется, сохраняем как есть
                    // свойство: currency => usd
                    if(!isset($credit_arr[$arr_item['new_name']])){
                        $credit_arr[$arr_item['new_name']] = mb_strtolower($old_credit->option_name);
                        if($arr_item['new_name2'] != null){
                            $credit_arr[$arr_item['new_name2']] = mb_strtolower($old_credit->option_name);
                        }
                    }

                }
            }
            elseif ($arr_item['description'] != false){
                $credit_arr[$arr_item['new_name']] = $old_credit->description;
                if($arr_item['new_name2'] != null){
                    $credit_arr[$arr_item['new_name2']] = $old_credit->description;
                }
            }
            // value_from / value_to
            else{

                if($old_credit->value_from != null){

                    $cleared = $old_credit->value_from;
                    $cleared = str_replace([','], '.', $cleared);
                    $cleared = str_replace(['%', 'от', ' '], '', $cleared);
                    $cleared = str_replace([' '], '', $cleared);

                    $credit_arr[$arr_item['new_name']] = $cleared;
                }
                elseif($old_credit->value_to != null){
                    $credit_arr[$arr_item['new_name']] = $old_credit->value_to;
                }
                elseif ($old_credit->option_value_from != null){
                    $credit_arr[$arr_item['new_name']] = $old_credit->option_value_from;
                }
                elseif ($old_credit->option_value_to != null){
                    $credit_arr[$arr_item['new_name']] = $old_credit->option_value_to;
                }
                else{
                    $credit_arr[$arr_item['new_name']] = null;
                }
            }

            if(!empty($arr_item['need_comment_field'])){
                $credit_arr[$arr_item['need_comment_field']] = trim($old_credit->description);
            }
        }

        if(!empty($receive_mode)){
            $credit_arr['receive_mode'] = json_encode($receive_mode);
        }


        return $credit_arr;
    }

    private function fill_fees2($old_credit, $credit_fees = [])
    {
        // соответствия старым условиям новым (комиссии)
        $old_new_credit_fees = [

//            'Тип комиссии' => [
//                'new_name' => 'komissiya-za-organizatsiyu',
//                'new_name2' => null,
//                'description' => false,
//                'option_name' => true,
//                'value_from' => true,
//                'value_to' => true,
//                'transform' => [
//                    'разовая, %' => 'one_time_percent',
//                    'разово % от суммы' => 'one_time_percent',
//                    'разовая, сумма' => 'one_time_amount',
//                    'разово в сумме' => 'one_time_amount',
//                    'ежемесячная, %' => 'every_month_percent',
//                    'ежемесячно % от кредита' => 'every_month_percent',
//                    'не менее суммы' => 'not_less_then_amount',
//                ],
//            ],
            'Комиссия за обслуживание кредита' => [
                'new_name' => 'service',
                'new_name2' => null,
                'description' => false,
                'option_name' => true,
                'value_from' => true,
                'value_to' => true,
                'transform' => [
                    'разовая, %' => 'one_time_percent',
                    'разово % от суммы' => 'one_time_percent',
                    'разовая, сумма' => 'one_time_amount',
                    'разово в сумме' => 'one_time_amount',
                    'ежемесячная, %' => 'every_month_percent',
                    'ежемесячно % от кредита' => 'every_month_percent',
                    'не менее суммы' => 'not_less_then_amount',
                ],
            ],

            'Комиссия за зачисление на карту / счет' => [
                'new_name' => 'card_account_enrolment',
                'new_name2' => null,
                'description' => false,
                'option_name' => true,
                'value_from' => true,
                'value_to' => true,
                'transform' => [
                    'разовая, %' => 'one_time_percent',
                    'разово % от суммы' => 'one_time_percent',
                    'разовая, сумма' => 'one_time_amount',
                    'разово в сумме' => 'one_time_amount',
                    'ежемесячная, %' => 'every_month_percent',
                    'ежемесячно % от кредита' => 'every_month_percent',
                    'не менее суммы' => 'not_less_then_amount',
                ],
            ],

            'Комиссия за обналичивание' => [
                'new_name' => 'monetisation',
                'new_name2' => null,
                'description' => false,
                'option_name' => true,
                'value_from' => true,
                'value_to' => true,
                'transform' => [
                    'разовая, %' => 'one_time_percent',
                    'разово % от суммы' => 'one_time_percent',
                    'разовая, сумма' => 'one_time_amount',
                    'разово в сумме' => 'one_time_amount',
                    'ежемесячная, %' => 'every_month_percent',
                    'ежемесячно % от кредита' => 'every_month_percent',
                    'не менее суммы' => 'not_less_then_amount',
                ],
            ],

            'Комиссия за организацию финансирования' => [
                'new_name' => 'organization',
                'new_name2' => null,
                'description' => false,
                'option_name' => true,
                'value_from' => true,
                'value_to' => true,
                'transform' => [
                    'разовая, %' => 'one_time_percent',
                    'разово % от суммы' => 'one_time_percent',
                    'разовая, сумма' => 'one_time_amount',
                    'разово в сумме' => 'one_time_amount',
                    'ежемесячная, %' => 'every_month_percent',
                    'ежемесячно % от кредита' => 'every_month_percent',
                    'не менее суммы' => 'not_less_then_amount',
                ],
            ],
            'Комиссия за выдачу займа' => [
                'new_name' => 'granting',
                'new_name2' => null,
                'description' => false,
                'option_name' => true,
                'value_from' => true,
                'value_to' => true,
                'transform' => [
                    'разовая, %' => 'one_time_percent',
                    'разово % от суммы' => 'one_time_percent',
                    'разовая, сумма' => 'one_time_amount',
                    'разово в сумме' => 'one_time_amount',
                    'ежемесячная, %' => 'every_month_percent',
                    'ежемесячно % от кредита' => 'every_month_percent',
                    'не менее суммы' => 'not_less_then_amount',
                ],
            ],
            'Комиссия за рассмотрение' => [
                'new_name' => 'review',
                'new_name2' => null,
                'description' => false,
                'option_name' => true,
                'value_from' => true,
                'value_to' => true,
                'transform' => [
                    'разовая, %' => 'one_time_percent',
                    'разово % от суммы' => 'one_time_percent',
                    'разовая, сумма' => 'one_time_amount',
                    'разово в сумме' => 'one_time_amount',
                    'ежемесячная, %' => 'every_month_percent',
                    'ежемесячно % от кредита' => 'every_month_percent',
                    'не менее суммы' => 'not_less_then_amount',
                ],
            ],


        ];

        if(isset($old_new_credit_fees[$old_credit->prop_name])){

            // берем массив для удобства из $old_new_credit
            $arr_item = $old_new_credit_fees[$old_credit->prop_name];

            // откуда брать данные: option_name / value_from / value_to
            if($arr_item['option_name']){

                // если данные надо преобразовать в новый тип:
                // C подтверждением доходов => 1
                // Без подтверждения доходов => 0
                //если в массиве transform есть такой ключ: "C подтверждением доходов"
                if(isset($arr_item['transform'][mb_strtolower($old_credit->option_name)])){

                    $credit_fees[$arr_item['new_name']] = $arr_item['transform'][mb_strtolower($old_credit->option_name)];
                    $credit_fees[$arr_item['new_name'].'_input'] = $old_credit->value_from;

                }
            }
        }

        return $credit_fees;
    }

    private function fill_income_confirmation()
    {
        $creditProps = CreditProp::all();
        foreach ($creditProps as $creditProp) {
            if($creditProp->income_confirmation == null){
                $creditProp->income_confirmation = true;
                $creditProp->save();
            }
        }
    }

    private function fill_credit_props_parent()
    {
        $credits = Credit::all();
        foreach ($credits as $credit) {
            $props = $credit->props;
            foreach ($props as $prop) {

                if($prop->min_amount == null){
                    $founded_row = $credit->props()->whereNotNull('min_amount')->first();
                    if($founded_row != null){
                        $prop->min_amount = $founded_row->min_amount;
                    }
                }

                if($prop->max_amount == null){
                    $founded_row = $credit->props()->whereNotNull('max_amount')->first();
                    if($founded_row != null){
                        $prop->max_amount = $founded_row->max_amount;
                    }
                }

                if($prop->min_period == null){
                    $founded_row = $credit->props()->whereNotNull('min_period')->first();
                    if($founded_row != null){
                        $prop->min_period = $founded_row->min_period;
                    }
                }

                if($prop->max_period == null){
                    $founded_row = $credit->props()->whereNotNull('max_period')->first();
                    if($founded_row != null){
                        $prop->max_period = $founded_row->max_period;
                    }
                }

                if($prop->percent_rate == null){
                    $founded_row = $credit->props()->whereNotNull('percent_rate')->first();
                    if($founded_row != null){
                        $prop->percent_rate = $founded_row->percent_rate;
                    }
                }

                if($prop->currency == null){
                    $founded_row = $credit->props()->where('currency', '!=', null)->
                    where('currency', '!=', '')->first();
                    if($founded_row != null){
                        $prop->currency = $founded_row->currency;
                    }
                }

                if($prop->income_confirmation == null){
                    $founded_row = $credit->props()->whereNotNull('income_confirmation')->first();
                    if($founded_row != null){
                        $prop->income_confirmation = $founded_row->income_confirmation;
                    }
                }

                if($prop->repayment_structure == null){
                    $founded_row = $credit->props()->whereNotNull('repayment_structure')->first();
                    if($founded_row != null){
                        $prop->repayment_structure = $founded_row->repayment_structure;
                    }
                }

                if($prop->credit_security == null){
                    $founded_row = $credit->props()->whereNotNull('credit_security')->first();
                    if($founded_row != null){
                        $prop->credit_security = $founded_row->credit_security;
                    }
                }

                if($prop->income_project == null){
                    $founded_row = $credit->props()->whereNotNull('income_project')->first();
                    if($founded_row != null){
                        $prop->income_project = $founded_row->income_project;
                    }
                }

                if($prop->age == null){
                    $founded_row = $credit->props()->whereNotNull('age')->first();
                    if($founded_row != null){
                        $prop->age = $founded_row->age;
                    }
                }

                if($prop->client_type == null){
                    $founded_row = $credit->props()->whereNotNull('client_type')->first();
                    if($founded_row != null){
                        $prop->client_type = $founded_row->client_type;
                    }
                    else{
                        $prop->client_type = 'standart';
                    }
                }

                $prop->created_by = 1;
                $prop->changed_by = 1;
                $prop->save();
            }
        }
    }

    private function fill_insurance()
    {
        $credits = Credit::all();
        $no = [
            'не требуется',
            'Отсутствует',
            'нет',
            'Нет',
            'Не требуется',
        ];
        foreach ($credits as $credit) {
            if(in_array($credit->insurance_input, $no) || $credit->insurance_input == null){
                $credit->insurance = 'voidable';
                $credit->save();
            }
        }
    }
}
