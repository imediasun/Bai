<?php

//namespace TCG\Voyager\Http\Controllers;
namespace App\Http\Controllers\Admin;

use App\Bank;
use App\City;
use App\Credit;
use App\CreditProp;
use App\CreditPropFee;
use App\Currency;
use App\CustomProp;
use App\FastFilter;
use App\Fee;
use App\FeeType;
use App\FeeValue;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;

class CreditController extends Controller
{
    public function index(Request $request)
    {
        session()->forget('props_cnt');
        session()->forget('fees_cnt');

        $dataType = Voyager::model('DataType')->where('slug', '=', 'credits')->first();
        $view = 'admin.credits.browse';
        $isServerSide = false;

        $search = (object) ['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];
        $searchable = $dataType->server_side ? array_keys(SchemaManager::describeTable(app($dataType->model_name)->getTable())->toArray()) : '';
        $orderBy = $request->get('order_by');
        $sortOrder = $request->get('sort_order', null);
        $dataTypeContent = Credit::all();


        return view($view, compact(
            'dataType',
            'isServerSide',
            'dataTypeContent',
//            'isModelTranslatable',
            'search',
            'orderBy',
            'sortOrder',
            'searchable'
        ));
    }

    public function edit($id, Request $request)
    {
        $dataType = Voyager::model('DataType')->where('slug', '=', 'credits')->first();
        $view = 'admin.credits.edit-add';
        $isServerSide = false;

        $search = (object) ['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];
        $searchable = $dataType->server_side ? array_keys(SchemaManager::describeTable(app($dataType->model_name)->getTable())->toArray()) : '';
        $orderBy = $request->get('order_by');
        $sortOrder = $request->get('sort_order', null);
        $dataTypeContent = Credit::find($id);
        $banks = Bank::all();

        return view($view, compact(
            'dataType',
            'isServerSide',
            'dataTypeContent',
//            'isModelTranslatable',
            'search',
            'orderBy',
            'banks',
            'sortOrder',
            'searchable'
        ));
    }

    public function destroy($id)
    {
        Credit::destroy($id);
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $credit = Credit::find($id);
//        return redirect()->route('voyager.credits.store');
//        return $this->store($request, $id);
        $this->store($request, $id);
        return redirect()->route('voyager.credits.edit', $credit->id);
    }

    public function create()
    {
        $dataType = Voyager::model('DataType')->where('slug', '=', 'credits')->first();
        $dataTypeContent = (strlen($dataType->model_name) != 0)
            ? new $dataType->model_name()
            : false;
        foreach ($dataType->addRows as $key => $row) {
            $details = json_decode($row->details);
            $dataType->addRows[$key]['col_width'] = isset($details->width) ? $details->width : 100;
        }

//        $banks = Bank::where('parent_id', '=', null)->get();
        $banks = Bank::doesntHave('parents')->get();

        return view('admin.credits.edit-add', compact('dataType', 'dataTypeContent', 'banks'));
    }

    public function store(Request $request, $id = null)
    {
        if (!$request->ajax()) {

        $credit_arr = [
            'bank_id' => $request->bank,
            'name_ru' => $request->name_ru,
            'name_kz' => $request->name_kz,
            'alt_name_ru'  => $request->alt_name_ru,
            'alt_name_kz'  => $request->alt_name_ru,
            'short_description_ru' => $request->short_description_ru,
            'short_description_kz' => $request->short_description_kz,
            'description_ru' => $request->description_ru,
            'description_kz' => $request->description_kz,
            'online_url'  => $request->online_url,
            'promo'  => $request->promo,
            'sort_order'  => $request->sort_order,
            'h1_ru'  => $request->h1_ru,
            'h1_kz'  => $request->h1_kz,
            'meta_description_ru'  => $request->meta_description_ru,
            'meta_description_kz' => $request->meta_description_kz,
            'minimum_income' => $request->minimum_income,
            'occupational_life' => $request->occupational_life,
            'have_prolongation' => $request->have_prolongation ?? false,
            'occupational_current' => $request->occupational_current,
            'have_citizenship' => $request->have_citizenship ?? false,
            'method_of_repayment_ru' => $request->method_of_repayment_ru,
            'method_of_repayment_kz'  => $request->method_of_repayment_kz,
            'docs_ru' => $request->docs_ru,
            'docs_kz' => $request->docs_kz,
            'other_claims_ru' => $request->other_claims_ru,
            'other_claims_kz'  => $request->other_claims_kz,
            'have_mobile_phone'  => $request->have_mobile_phone ?? false,
            'have_work_phone'  => false,
            'have_early_repayment' => $request->have_early_repayment ?? false,
            'debtor_category' => $request->debtor_category,
            'credit_goal'  => $request->credit_goal,
            'receive_mode'  => $request->receive_mode,
            'registration'   => $request->registration,
            'time_for_consideration'    => $request->time_for_consideration,
            'credit_history'     => $request->credit_history,
            'credit_formalization'     => $request->credit_formalization,
            'is_approved'  => $request->is_approved ?? false,
            'have_constant_income'  => $request->have_constant_income ?? false,
            'changed_by'  => Auth::id(),
            'created_by'  => Credit::find($id) == null ? Auth::id() : null,
            'gesv'  => $request->gesv,

        ];

        $credit = Credit::updateOrCreate(['id' => $id ?? 0], $credit_arr);

        $credit_props_arr = [];
        $credit_props_fees_arr = [];

        $credit_props_input = $request->credit_props;
        if ($credit_props_input){

            $keys = array_keys($request->credit_props['min_amount']);

            foreach ($keys as $key) {
                $credit_props_arr = [
                    'min_amount'  => $credit_props_input['min_amount'][$key],
                    'max_amount'  => $credit_props_input['max_amount'][$key],
                    'min_period'  => $credit_props_input['min_period'][$key],
                    'max_period'  => $credit_props_input['max_period'][$key],
                    'percent_rate'  => $credit_props_input['percent_rate'][$key],
                    'currency'  => $credit_props_input['currency'][$key],
                    'income_confirmation'  => $credit_props_input['income_confirmation'][$key],
                    'repayment_structure'  => $credit_props_input['repayment_structure'][$key],
                    'credit_security'  => $credit_props_input['credit_security'][$key],
                    'credit_id'  => $credit->id,
                    'age' => $credit_props_input['age'][$key],
                    'income_project'    => $credit_props_input['income_project'][$key],
                    'client_type'  => $credit_props_input['client_type'][$key],
//                    'changed_by',
//                    'created_by',
                ];

                $credit_prop = CreditProp::updateOrCreate(['id' => $credit_props_input['id'][$key] ?? 0], $credit_props_arr);

                if(isset($credit_props_input['credit_fees'][$key])){

                    foreach ($credit_props_input['credit_fees'][$key] as $fee_inner) {

                        $fee_keys = array_keys($fee_inner);

                        foreach ($fee_keys as $fee_key) {

                            $fee_type = FeeType::where('alt_name_ru', $fee_key)
                                ->where('product_type', 'credit')
                                ->first();


                            if($fee_type != null){
                                $fee_value = $fee_type->fee_values()->where('value', '=', $fee_inner[$fee_key])->first();
                            }
                            else{
                                $fee_value = null;
                            }

                            if($fee_value != null){
                                $credit_props_fees_arr = [
                                    'credit_id' => $credit->id,
                                    'credit_prop_id' => $credit_prop->id,
                                    'fee_type_id' => $fee_type->id,
                                    'fee_value_id' => $fee_value->id,
                                    'input' => $fee_inner[$fee_key.'_input'],
                                ];

                                CreditPropFee::updateOrCreate(['id' => $fee_inner['fee_id'] ?? 0], $credit_props_fees_arr);
                            }
                        }
                    }
                }
            }
        }

        $creditCustomProps = $request->custom_options;

        if($creditCustomProps != null){
            $value_keys = array_keys($creditCustomProps['name_ru']);
            foreach ($value_keys as $value_key) {

                $custom_arr_data = [
                    'name_ru' => $creditCustomProps['name_ru'][$value_key],
                    'alt_name_ru' => $creditCustomProps['alt_name_ru'][$value_key],
                    'value_ru' => $creditCustomProps['value_ru'][$value_key],
                    'alt_value_ru' => $creditCustomProps['alt_value_ru'][$value_key],
                    'comment_ru' => $creditCustomProps['comment_ru'][$value_key],
                    'name_kz' => $creditCustomProps['name_kz'][$value_key],
                    'alt_name_kz' => $creditCustomProps['alt_name_kz'][$value_key],
                    'value_kz' => $creditCustomProps['value_kz'][$value_key],
                    'alt_value_kz' => $creditCustomProps['alt_value_kz'][$value_key],
                    'comment_kz' => $creditCustomProps['comment_kz'][$value_key],
                    'credit_id' => $credit->id,
                ];

                CustomProp::updateOrCreate(['id' => $creditCustomProps['id'][$value_key] ?? 0], $custom_arr_data);

                $custom_arr_data = [];
            }
        }

        return redirect()->route('voyager.credits.edit', $credit->id);
    }
    }
}