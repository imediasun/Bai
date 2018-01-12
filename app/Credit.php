<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    protected $fillable = [
        'bank_id',
        'insurance',
        'insurance_input',
        'name_ru',
        'name_kz',
        'alt_name_ru',
        'alt_name_kz',
        'short_description_ru',
        'short_description_kz',
        'description_ru',
        'description_kz',
        'online_url',
        'promo',
        'is_approved',
        'breadcrumbs_ru',
        'breadcrumbs_kz',
        'meta_title_ru',
        'meta_title_kz',
        'changed_by',
        'created_by',
        'sort_order',
        'h1_ru',
        'h1_kz',
        'meta_description_ru',
        'meta_description_kz',
        'minimum_income',
        'occupational_life',
        'occupational_current',
        'have_constant_income',
        'have_mobile_phone',
        'have_work_phone',
        'have_early_repayment',
        'have_prolongation',
        'have_citizenship',
        'method_of_repayment_ru',
        'method_of_repayment_kz',
        'docs_ru',
        'docs_kz',
        'other_claims_ru',
        'other_claims_kz',
        'debtor_category',
        'credit_goal',
        'receive_mode',
        'registration',
        'time_for_consideration',
        'credit_history',
        'credit_formalization',

        'time_for_consideration_comment',
        'have_early_repayment_comment',
        'occupational_life_comment',
        'minimum_income_comment',
    ];

    public function bank()
    {
        return $this->belongsTo('App\Bank');
    }

    public function fees()
    {
        return $this->hasMany('App\CreditPropFee');
    }

    public function props()
    {
        return $this->hasMany('App\CreditProp');
    }

    public function custom_props()
    {
        return $this->hasMany('App\CustomProp');
    }

    public static function transform_credit_goal($item)
    {
        $arr = [
            'none' => 'не важно',
            'any' => 'любая',
            'emergency_needs' => 'неотложные нужды',
            'just_money' => 'просто деньги',
            'goods' => 'товары',
            'business' => 'бизнес',
            'refinancing' => 'рефинансирование',
            'medication' => 'лечение',
            'education' => 'образование',
            'traveling' => 'путешествие',
        ];
        if(isset($arr[$item])){
            return $arr[$item];
        }

        return null;
    }

    public static function transform_credit_history($item)
    {
        $arr = [
            'none' => 'не важно',
            'positive' => 'положительная кредитная история',
            'negative' => 'отрицательная кредитная история',
        ];
        if(isset($arr[$item])){
            return $arr[$item];
        }

        return null;
    }

    public static function transform_credit_formalization($item)
    {
        $arr = [
            'none' => 'не важно',
            'online' => 'онлайн заявка',
            'office' => 'в отделении банка',
            'both' => 'в отделений банка и онлайн заявка',
        ];
        if(isset($arr[$item])){
            return $arr[$item];
        }

        return null;
    }

    public static function transform_security($item)
    {
        $arr = [
            '' => 'без залога и поручительства',
            'none' => 'без залога и поручительства',
            'without-security' => 'без залога и поручительства',
            'guarantor' => 'поручитель',
            'deposit' => 'залог - депозит',
            'immovables_current' => 'залог - имеющееся недвижимость',
            'immovables_bying' => 'залог - приобретемая недвижимость',
            'auto_current' => 'залог - имеющееся авто',
            'auto_buying' => 'залог - приобретаемое авто',
            'money' => 'залог - денежные средства',
        ];

        if(isset($arr[$item])){
            return $arr[$item];
        }

        return null;
    }

    public static function transform_currency($item, $symbol = true)
    {
        if($symbol){
            $arr = [
                'kzt' => '₸',
                'usd' => '$',
                'eur' => '€',
                '' => '₸',
            ];
        }
        else{
            $arr = [
                'kzt' => 'тенге',
                'usd' => 'долларов',
                'eur' => 'евро',
                '' => 'тенге',
            ];
        }

        if(isset($arr[$item])){
            return $arr[$item];
        }

        return null;
    }

    public static function transform_income_confirmation($item)
    {
        $arr = [
            1 => 'с подтверждением дохода',
            0 => 'без подтверждением дохода',
        ];

        if(isset($arr[$item])){
            return $arr[$item];
        }

        return null;
    }

    public static function getCredits($city = null, $bank = null, $ff = null, $opt = [])
    {
        $credits = Credit::all();

//        if(!isset($opt['currency'])){
//            $opt['currency'] = 'kzt';
//        }

        if(empty($opt)){
            foreach ($credits as $credit) {


                $props = $credit->props()->orderBy('percent_rate', 'asc')->get();//->whereNotNull('percent_rate')
                ;

                $rate = $props->min('percent_rate');

                $options['initial_fee'] = $opt['initial_fee']??0;
                $options['rate'] = $opt['rate']?? $rate;
                $options['tot'] = $opt['calc[tot]']??200000;
                $options['period'] = $opt['calc[period]']??12;

                $result = \CalcHelper::calculate_credit($options['period'], $options['tot'], $options['rate'], 1, $options['initial_fee']);
                $credit->ppm = $result['ppm'][0];
                $credit->overpay = $result['procentAmount'];

                $props = $props->first();
                if($props){
                    if(!empty($props->min_amount) && !empty($props->max_amount)){
                        $min = \CommonHelper::format_number($props->min_amount, false);
                        $max = \CommonHelper::format_number($props->max_amount, false);
                        $credit->amount = "от $min до $max";
                    }
                    elseif (!empty($props->min_amount)){
                        $min = \CommonHelper::format_number($props->min_amount, false);
                        $credit->amount = "от $min";
                    }
                    elseif (!empty($props->max_amount)){
                        $max = \CommonHelper::format_number($props->max_amount, false);
                        $credit->amount = "до $max";
                    }
                    else{
                        $credit->amount = "";
                    }

                    if(!empty($props->min_period) && !empty($props->max_period)){
                        $min = $props->min_period;
                        $max = $props->max_period;
                        $credit->period = "от $min до $max";
                    }
                    elseif (!empty($props->min_period)){
                        $min = $props->min_period;
                        $credit->period = "от $min";
                    }
                    elseif (!empty($props->max_period)){
                        $max = $props->max_period;
                        $credit->period = "до $max";
                    }
                    else{
                        $credit->amount = "";
                    }


                    $credit->min_amount = $props->min_amount;
                    $credit->max_amount = $props->max_amount;

                    $credit->min_period = $props->min_period;
                    $credit->max_period = $props->max_period;

                    $credit->percent_rate = $props->percent_rate;
                    $credit->gesv = $props->gesv;

                    $credit->minimum_income = \CommonHelper::format_number($credit->minimum_income, false);


                    $credit->credit_security = Credit::transform_security($props->credit_security);
//                    $credit->currency = Credit::transform_currency($props->currency) ?? '₸';
                    $credit->currency = $_SESSION['currency'];
                    $credit->income_confirmation = Credit::transform_income_confirmation($props->income_confirmation);
                }
            }
        }
        else{

        }
//        $credits = $credits->order
        return $credits;
    }

//    public function scopeppm($query, $id)
//    {
//        return $query->where('');
//    }

//    public function ppm($id)
//    {
//        return 123;
//    }
}
