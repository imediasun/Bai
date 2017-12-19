<?php

namespace App\Http\Controllers;

use App\Bank;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function index()
    {

    }

    public function creditPage($bank, $credit)
    {
        $bank = Bank::where('slug_ru', $bank)->first();
        if($bank != null){
            $credit = $bank->credits()->where('slug_'.$this->locale, $credit)->first();

            if ($credit != null){
                return view('');
                //todo: забираем кредит
            }
        }

        abort(404);

    }

    public function indexCityOrBank($cityOrBank)
    {

    }
}
