<?php

namespace App\Http\Controllers;

use App\Article;
use App\AutoCredit;
use App\BreakingNews;
use App\Credit;
use App\CreditCard;
use App\CreditProp;
use App\CreditPropFee;
use App\Currency;
use App\DebitCard;
use App\Deposit;
use App\FeeType;
use App\FeeValue;
use App\Loan;
use App\Mortgage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function index(Currency $currency)
    {





















        $currencies = $currency->getExchangeCurrencies();

        $news = BreakingNews::skip(0)->take(15)->orderBy('created_at', 'desc')->get();

        $articles = Article::skip(0)->take(5)->orderBy('created_at', 'desc')->get();

        $deposits = Deposit::count();
        $credit_cards = CreditCard::count();
        $debit_cards = DebitCard::count();
        $mortgages = Mortgage::count();
        $loans = Loan::count();
        $credits = Credit::count();
        $autoCredits = AutoCredit::count();

        return view('main.index', [
            'currencies' => $currencies,
            'news' => $news,
            'articles' => $articles,
            'creditsCount' => $credits,
            'autoCreditsCount' => $autoCredits,
            'depositsCount' => $deposits,
            'debetCardsCount' => $debit_cards,
            'creditCardsCount' => $credit_cards,
            'mortgagesCount' => $mortgages,
            'loansCount' => $loans,
        ]);
    }




}
