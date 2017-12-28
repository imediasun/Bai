<?php

namespace App\Http\Controllers\Admin;

use App\CreditProp;
use App\CreditPropFee;
use App\CustomProp;
use App\Fee;
use App\FeeType;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class AjaxController extends Controller
{
    public function getProductProps(Request $request)
    {
        if (!session()->has('props_cnt')){
            session(['props_cnt' => 0]);
            $val = 0;
        }
        else{
            $val = session('props_cnt');
            session(['props_cnt' => ++$val]);
        }

        return view('admin.credits.add.add_product_props', compact('val'));
    }

    public function getCustomProp()
    {
        if (!session()->has('custom_props_cnt')){
            session(['custom_props_cnt' => 0]);
            $val = 0;
        }
        else{
            $val = session('custom_props_cnt');
            session(['custom_props_cnt' => ++$val]);
        }
        return view('admin.credits.add.add_product_custom_options', ['val' => $val]);
    }

    public function translit(Request $request)
    {
        return \CommonHelper::translit($request->text);
    }

    public function delProductProps(Request $request)
    {
        $result = json_encode(false);
        //todo: продолжить для всех продуктов
        if ($request->product == 'credit'){
            $propBlock = CreditProp::destroy($request->id);
            $result = json_encode(true);

        }
        return $result;
    }

    public function getProductFees(Request $request)
    {
        if (!session()->has('fees_cnt')){
            session(['fees_cnt' => 0]);
        }
        else{
            $val = session('fees_cnt');
            session(['fees_cnt' => ++$val]);
        }

        foreach (FeeType::all() as $fee){
            $control = $fee->name_ru;
            $control_slug = $fee->alt_name_ru;
            $options = [];
            foreach ($fee->fee_values as $fee_value){
                $options[] = [
                    'value_title' => $fee_value->name_ru,
                    'value' => $fee_value->value,
                ];
            }
            $fees[] = [
                'control_title' => $control,
                'control_slug' => $control_slug,
                'controls' => $options,
                'prop_number' => session('props_cnt'),
                'fee_number' => session('fees_cnt')
            ];

            $prop_number = session('props_cnt');
            $fee_number = session('fees_cnt');
        }

        return view('admin.credits.add.add_product_props_fees', compact('fees', 'prop_number', 'fee_number'));
    }

    public function delProductFees(Request $request)
    {
        $result = json_encode(false);
        $propBlock = CreditPropFee::destroy($request->id);
        $result = json_encode(true);

        return $result;
    }

    public function delCustomProp(Request $request)
    {
        $result = json_encode(false);
        $propBlock = CustomProp::destroy($request->id);
        $result = json_encode(true);

        return $result;
    }
}



