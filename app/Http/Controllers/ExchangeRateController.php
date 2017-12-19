<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bank;
use AppBundle\Entity\City;
use AppBundle\Entity\Currency;
use AppBundle\Entity\ExchangeRate;
use AppBundle\Form\Admin\ExchangerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/kursy")
 */
class ExchangeRateController extends RouteController
{

    /**
     * @Route("/nacbank/{code}/", name="exchange_rates_national_bank_currency")
     *
     * @param Currency $currency
     * @param Request $request
     * @return Response
     */
    public function currencyRateAction( $code, Request $request)
    {
        /*$this->setRoutePrefix($request, 'exchange_rates');

        $em = $this->getDoctrine()->getManager();

        $currencies = $this->getDoctrine()->getRepository('AppBundle:Currency')->findBy([], ['isCommon' => 'DESC', 'code' => 'ASC']);

        $cityManager = $this->get("app.city_manager");
        $city = $cityManager->getCityEntity();

        $bestBuyRates = array();
        $bestSellRates = array();

        if (in_array($currency->getCode(), array('usd', 'eur', 'rub', 'gbp', 'chf', 'jpy', 'cny', 'inr', 'kgs', 'try'))) {
            $bestBuyRates = $em->getRepository('AppBundle:ExchangeRate')->getBestBuyRates($city, $currency);
            $bestSellRates = $em->getRepository('AppBundle:ExchangeRate')->getBestSellRates($city, $currency);
        }

        $history = $currency->getHistoryFormatted();
        $history = array_slice($history, 0, Currency::HISTORY_RATES_COUNT);

        $ratesInOthersBanks = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->getRatesInOtherBanks($city);

        return $this->render(':bank:currency.html.twig', array(
            'ratesInOthersBanks' => $ratesInOthersBanks,
            'currencies' => $currencies,
            'history' => $history,
            'historyCount' => Currency::HISTORY_RATES_COUNT,
            'current' => $currency,
            'bestBuyRates' => $bestBuyRates,
            'bestSellRates' => $bestSellRates,
            'currentCity' => $city
        ));*/
        $currency = $this->getDoctrine()->getRepository('AppBundle:Currency')->findOneByCode(strtoupper($code));
        $currencies = $this->getDoctrine()->getRepository('AppBundle:Currency')->findAll();
        $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByAltName('almaty');

        $curArray = ['usd','eur','rub','gbp','chf','jpy','cny','inr','kgs','try'];
        $ratesBuy = null;
        $ratesSell = null;
        $ratesBuyNC = null;
        $ratesSellNC = null;
        if (in_array($code, $curArray)){
            $ratesBuy = $this->getDoctrine()
                ->getRepository('AppBundle:ExchangeRate')
                ->getRatesInOtherBanks($city, null, ExchangeRate::TYPE_CASH, ['field'=>'er.'.$code.'Buy', 'asc'=>'ASC']);
            $ratesSell = $this->getDoctrine()
                ->getRepository('AppBundle:ExchangeRate')
                ->getRatesInOtherBanks($city, null, ExchangeRate::TYPE_CASH, ['field'=>'er.'.$code.'Sell', 'asc'=>'DESC']);

            $ratesBuyNC = $this->getDoctrine()
                ->getRepository('AppBundle:ExchangeRate')
                ->getRatesInOtherBanks($city, null, ExchangeRate::TYPE_NO_CASH, ['field'=>'er.'.$code.'Buy', 'asc'=>'ASC']);
            $ratesSellNC = $this->getDoctrine()
                ->getRepository('AppBundle:ExchangeRate')
                ->getRatesInOtherBanks($city, null, ExchangeRate::TYPE_NO_CASH, ['field'=>'er.'.$code.'Sell', 'asc'=>'DESC']);
            dump($ratesBuy, $ratesSell, $ratesBuyNC, $ratesSellNC);
        }
        $cities = $this->getDoctrine()->getRepository('AppBundle:City')->findAll();
        return $this->render(':dynamic:dynamic.html.twig' ,
            compact('currency', 'city', 'ratesBuy', 'code', 'ratesSell', 'ratesBuyNC', 'ratesSellNC', 'currencies', 'cities')
        );
    }


    /**
     * @Route("/{currency}/", name="exchange_rates_one_currency", requirements={
     *     "currency": "usd|eur|rub|gbp|chf|jpy|cny|inr|kgs|try"
     * })
     * @Route("/{altName}/{currency}/", name="exchange_rates_city_buy", requirements={
     *      "currency": "usd|eur|rub|gbp|chf|jpy|cny|inr|kgs|try",
     *      "type": "prodazha|pokupka"
     * })
     * @Route("/{currency}/{type}/", name="exchange_rates_rates", requirements={
     *      "currency": "usd|eur|rub|gbp|chf|jpy|cny|inr|kgs|try",
     *      "type": "prodazha|pokupka"
     * })
     * @Route("/{currency}/{type}/{altName}/", name="exchange_rates_city_rates", requirements={
     *      "currency": "usd|eur|rub|gbp|chf|jpy|cny|inr|kgs|try",
     *      "type": "prodazha|pokupka"
     * })
     *
     * @param Request $request
     * @param string $city
     * @param $currency
     * @param null $type
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function currencyAction(Request $request, $altName = null, $currency, $type = null)
    {
        $this->setRoutePrefix($request, 'exchange_rates');

        /* @var City $city */
        $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByAltName($altName);
        if (empty($city)) {
            $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByAltName('almaty');
        }

        // Set selected city as current
        $cityManager = $this->get("app.city_manager");
        $cityManager->setCity($city->getAltName());

        $widgetRates = [];
        $ratesWidget = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->getWidgetRates();
        /* @var ExchangeRate $widgetRate */
        foreach ($ratesWidget as $widgetRate) {
            $widgetLastRate = $this->getDoctrine()->getRepository('AppBundle:ExchangeRateHist')->getLastRatesHistory($widgetRate->getId());

            $widgetRates[] = [
                'currentRate' => $widgetRate,
                'lastRate' => $widgetLastRate,
            ];
        }

        $currencies = $this->getDoctrine()->getRepository('AppBundle:Currency')->findBy([], ['isCommon' => 'DESC', 'code' => 'ASC']);
        $currentCurrency = $this->getDoctrine()->getRepository('AppBundle:Currency')->findOneByCode($currency);

        if (!empty($type) || $type = $request->query->get('type')) {
            if ($type === 'prodazha') {
                $type = 'sell';
            } else {
                $type = 'buy';
            }
            $orderBy = ['field' => 'er.' . $currency . ucfirst($type), 'asc' => 'DESC'];
        } else {
            $orderBy = [];
        }

        $amount = $request->query->get('amount');
        $amount = str_replace(' ', '', $amount);

        $rates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->getRatesInOtherBanks($city, null, null, $orderBy);
        $getRatesInExchangers = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->getRatesInExchangers($city, $orderBy);

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Главная", "homepage");
        $breadcrumbs->addItem("Курсы в " . $city->getName());
        $exchangersRate = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->findBy(['city'=>$city, 'bank'=>null]);

        $params = [
            'city' => $city,
            'ratesInOthersBanks' => $rates,
            'ratesInExchangers' => $getRatesInExchangers,
            'widgetRates' => $widgetRates,
            'currencies' => $currencies,
            'currentCurrency' => $currentCurrency,
            'type' => $type,
            'amount' => $amount,
            'cur' =>$currency,
            'altName' => $city->getAltName(),
            'exchangersRate' => $exchangersRate
        ];

        return $this->render(':rate:city_rates.html.twig', $params);

    }

    /**
     * @Route("/redirect-to-kurce/", name="redirect_to_kurce")
     */
    public function redirectToKurseAction(Request $request){

        return $this->redirectToRoute('exchange_rates_page', [
            'altName'=>$request->get('altCity'),
            'count'=>$request->get('count'),
            'action'=>$request->get('action'),
            'currency'=>$request->get('currency'),
        ], 302);
    }

    /**
     * @Route("/", name="exchange_rates_index")
     * @Method("GET")
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $this->setRoutePrefix($request, 'exchange_rates');

        $banks = $this->getDoctrine()->getRepository('AppBundle:Bank')->getBanks('ratingClients', 'DESC', 0, 30);

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Главная", "homepage");
        $breadcrumbs->addItem("Банки", "bank_index");

        return $this->redirectToRoute('exchange_rates_page', ['altName' => 'almaty']);

        $ratesInOthersBanks = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->findBy([])->toArray();
        $ratesInExchangers = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->findBy([]);
        $widgetRates = [];
        $ratesWidget = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->getWidgetRates();
        /* @var ExchangeRate $widgetRate */
        foreach ($ratesWidget as $widgetRate) {
            $widgetLastRate = $this->getDoctrine()->getRepository('AppBundle:ExchangeRateHist')->getLastRatesHistory($widgetRate->getId());

            $widgetRates[] = [
                'currentRate' => $widgetRate,
                'lastRate' => $widgetLastRate,
            ];
        }

        $currencies = $this->getDoctrine()->getRepository('AppBundle:Currency')->findBy([], ['isCommon' => 'DESC', 'code' => 'ASC']);


        $params = [
            'banks' => $banks,
            'ratesInOthersBanks' => $ratesInOthersBanks,
            'ratesInExchangers' => $ratesInExchangers,
            'widgetRates' => $widgetRates,
            'currencies' => $currencies,
        ];

        return $this->render(':rate:city_rates.html.twig', $params);
    }

    /**
     * @Route("/nacbank/", name="exchange_rates_national_bank", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function nationalBankRatesAction(Request $request)
    {
        $this->setRoutePrefix($request, 'exchange_rates');

        $currencies = $this->getDoctrine()->getRepository('AppBundle:Currency')->findBy([], ['code' => 'ASC']);
        $cities = $this->getDoctrine()->getRepository('AppBundle:City')->findAll();

        $cityManager = $this->get("app.city_manager");
        $city = $cityManager->getCityEntity();

        $ratesInOthersBanks = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->getRatesInOtherBanks($city);

        return $this->render(':bank:page_national_bank.html.twig', array(
            'city' => $city,
            'cities' => $cities,
            'currencies' => $currencies,
            'ratesInOthersBanks' => $ratesInOthersBanks,
        ));
    }


    /**
     * @Route("/{altName}/", name="exchange_rates_page")
     * @Route("/{altName}/{bankName}", name="exchange_rates_no_bank_page")
     *
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function exchangeRateAction($altName, $bankName = null, Request $request)
    {
//        $this->setRoutePrefix($request, 'exchange_rates');
        $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByAltName($altName);

        // Check if altName is a Bank
        $bank = $this->getDoctrine()->getRepository('AppBundle:Bank')->findOneByAltName($bankName);
        if ($bank && $city && $bankName !== null) {
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addRouteItem("Банки", "bank_index");
            $breadcrumbs->addItem($bank->getName());

            $ratesInOthersBanks = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->getRatesInOtherBanks($city, $bank);

            $currencies = $this->getDoctrine()->getRepository('AppBundle:Currency')->getExchangeCurrencies();

            $bankLastRates = [];
            /* @var $bankRates ExchangeRate  */
            $bankRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->getBankRates($bank, $city, ExchangeRate::TYPE_NO_CASH);
            if (!empty($bankRates)) {
                if (empty($selectDate)) {
                    $bankLastRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRateHist')->getLastRatesHistory($bankRates->getId());
                    if (!empty($bankLastRates)) {
                        $bankLastRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRateHist')->transformToArray($bankLastRates);
                    }
                    $bankRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->transformToArray($bankRates);
                } else {
                    $bankLastRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRateHist')->getRatesHistory($bankRates->getId(), $selectDate);
                    if (!empty($bankLastRates)) {
                        $bankRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRateHist')->transformToArray($bankLastRates[0]);
                        $bankLastRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRateHist')->transformToArray($bankLastRates[1]);
                    }
                }
            } else {
                $bankRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->getBankRates($bank, $city, ExchangeRate::TYPE_CASH);
                if (!empty($bankRates)) {
                    $bankLastRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRateHist')->getLastRatesHistory($bankRates->getId());
                    if (!empty($bankLastRates)) {
                        $bankLastRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRateHist')->transformToArray($bankLastRates);
                    }
                    $bankRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->transformToArray($bankRates);
                }
            }

            $params = [
                'bank' => $bank,
                'currencies' => $currencies,
                'bankRates' => $bankRates,
                'bankLastRates' => $bankLastRates,
                'ratesInOthersBanks' => $ratesInOthersBanks,

            ];

            return $this->render(':bank:rates.html.twig', $params);
        }

        // Check if altName is a City
        if ($city) {
            $ratesInOthersBanks = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->getRatesInOtherBanks($city);
            $ratesInExchangers = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->getRatesInExchangers($city);

            $widgetRates = [];
            $ratesWidget = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->getWidgetRates();
            /* @var ExchangeRate $widgetRate */
            foreach ($ratesWidget as $widgetRate) {
                $widgetLastRate = $this->getDoctrine()->getRepository('AppBundle:ExchangeRateHist')->getLastRatesHistory($widgetRate->getId());

                $widgetRates[] = [
                    'currentRate' => $widgetRate,
                    'lastRate' => $widgetLastRate,
                ];
            }

            $currencies = $this->getDoctrine()->getRepository('AppBundle:Currency')->findBy([], ['isCommon' => 'DESC', 'code' => 'ASC']);

            // Set selected city as current
            $cityManager = $this->get("app.city_manager");
            $cityManager->setCity($altName);

            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addItem("Курсы в " . $city->getName());

            $exchangersRate = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->findBy(['city'=>$city, 'bank'=>null]);

            $params = [
                'city' => $city,
                'ratesInOthersBanks' => $ratesInOthersBanks,
                'ratesInExchangers' => $ratesInExchangers,
                'widgetRates' => $widgetRates,
                'currencies' => $currencies,
                'altName' => $altName,
                'type' => null,
                'cur' =>'usd',
                'exchangersRate' => $exchangersRate
            ];

            return $this->render(':rate:city_rates.html.twig', $params);
        }



        // Else redirect to bank list
        return $this->redirectToRoute('bank_index');

    }

    /**
     * @Route("/history/{code}/{count}/", name="exchange_rates_history_count", defaults={"count" = 20})
     * @Route("/history/{code}/", name="exchange_rates_history_default")
     *
     * @param string $code
     * @param int $count
     * @return Response
     */
    public function historyAction($code, $count = 20)
    {
        $currency = $this->getDoctrine()->getRepository('AppBundle:Currency')->findOneBy(array('code' => strtoupper($code)));

        if (!is_null($currency)) {

            $history = $currency->getHistoryFormatted();
            $history = array_slice($history, 0, $count);
            return new JsonResponse($history);
        }

        throw $this->createNotFoundException('History not found');
    }




}
