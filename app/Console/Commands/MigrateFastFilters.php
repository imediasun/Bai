<?php

namespace App\Console\Commands;

use App\Credit;
use App\CreditProp;
use App\CreditPropFee;
use App\FastFilter;
use App\FastFilterProp;
use App\FeeType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateFastFilters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:ff';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fast filters migration';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        FastFilter::truncate();
        FastFilterProp::truncate();
        $this->migrate();
    }

    private function migrate()
    {
        $query_limit = "SELECT
          f.id as ff_id,
          f.name as f_name,
          pr.name as prop_name,
          po.name as option_name,
          po.value_from as option_value_from,
          po.value_to as option_value_to,
          po.value_unit as option_value_unit
          FROM fast_filter f
            JOIN fast_filter_prop fp on f.id = fp.fast_filter_id
            LEFT JOIN prop_old pr on fp.prop_id = pr.id
            LEFT JOIN prop_option_old po on fp.prop_option_id = po.id
        WHERE 1=1
            AND f.product_id = 1
            ORDER BY pr.name";

        $ff_olds = DB::select($query_limit);

        foreach ($ff_olds as $ff_old) {
            $arr = [
                'sort_order' => 10,
                'is_approved' => true,
                'product_type' => 'credit',
                'name_ru' => $ff_old->f_name,
                'name_kz' => $ff_old->f_name,
                'alt_name_ru' => str_slug($ff_old->f_name),
                'alt_name_kz' => str_slug($ff_old->f_name),
            ];
            $fastFilter = FastFilter::updateOrCreate (['name_ru' => $ff_old->f_name], $arr);
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
                    'без подтверждения доходов' => false,
                    'с подтверждением доходов' => true,
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

                        $credit_prop[$arr_item['new_name']] = $arr_item['transform'][mb_strtolower($old_credit->option_name)];
                    }
                }
                else{
                    // преобразование не требуется, сохраняем как есть
                    // свойство: currency => USD
                    $credit_prop[$arr_item['new_name']] = mb_strtolower($old_credit->option_name);
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
                    $credit_arr[$arr_item['new_name']] = mb_strtolower($old_credit->option_name);
                    if($arr_item['new_name2'] != null){
                        $credit_arr[$arr_item['new_name2']] = mb_strtolower($old_credit->option_name);
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


}
