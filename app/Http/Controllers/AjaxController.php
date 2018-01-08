<?php

namespace App\Http\Controllers;
use App\Credit;
use App\CreditProp;
use App\CreditPropFee;
use Illuminate\Http\Request;


/**
 * @Route("/ajax")
 */
class AjaxController extends Controller
{
    /**
     * @Route("/getBanks/", name="ajax_get_banks",
     *          options={"expose"=true},
     *          condition="request.isXmlHttpRequest()"
     * )
     * @Method({"POST"})
     */
    public function ajaxGetBanksAction(Request $request)
    {
        $text = $request->request->get('text');
        $tools = $this->get('app.tools');

        $jsonResponseHelper = $this->get('app.json_response_helper');
        $response = $jsonResponseHelper->prepareJsonResponse();

        $response->setData($tools->translit($text));

        return $response;
    }

    /**
     * @Route("/getSetCity/", name="ajax_set_city",
     *          options={"expose"=true},
     *          condition="request.isXmlHttpRequest()"
     * )
     *
     */
    public function ajaxSetCityAction(Request $request)
    {
        $cityAltName = $request->request->get('city');
        $route = $request->request->get('route');
        $session = $request->getSession();
        $routeParams = json_decode(html_entity_decode($request->request->get('routeParams')), true);
        if (isset($routeParams['altName'])) {
            $routeParams['altName'] = $cityAltName;
        }

        $cityManager = $this->get('app.city_manager');
        $cityManager->setCity($cityAltName);

        $jsonResponseHelper = $this->get('app.json_response_helper');
        $response = $jsonResponseHelper->prepareJsonResponse();

        $response->setData([
            'city' => $cityManager->getCity(),
            'href' => $this->generateUrl($route, $routeParams, UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        $session->set('current_city_selected', $cityAltName);

        return $response;
    }

    /**
     * @Route("/citySelected/", name="ajax_city_selected",
     *          options={"expose"=true},
     *          condition="request.isXmlHttpRequest()"
     * )
     *
     */
    public function ajaxCitySelected(Request $request)
    {
        $cityAltName = $request->request->get('city');
        $session = $request->getSession();
        $session->set('current_city_selected', $cityAltName);
        return new Response(json_encode(['result' => 'ok']));


    }

    /**
     * @Route("/courseCalculator/{altName}/", name="ajax_course_calculator")
     */
    public function ajaxCourseCalculator($altName, Request $request){

        $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByAltName($altName);
        $ratesInOthersBanks = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->getRatesInOtherBanks($city);
        $ratesInExchangers = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->getRatesInExchangers($city);
        $currencies = $this->getDoctrine()->getRepository('AppBundle:Currency')->findBy([], ['isCommon' => 'DESC', 'code' => 'ASC']);
        $exchangersRate = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->findBy(['city'=>$city, 'bank'=>null]);
        $params = [
            'city' => $city,
            'ratesInOthersBanks' => $ratesInOthersBanks,
            'ratesInExchangers' => $ratesInExchangers,
            'currencies' => $currencies,
            'count' => intval($request->get('count')),
            'exchangersRate' => $exchangersRate
        ];

        return $this->render(':templates:course_list.html.twig', $params);
    }

    /**
     * @Route("/getBank/{bank}/", name="ajax_get_bank")
     */
    public function ajaxGetBank(Bank $bank){
        $res = [];
        $res['address'] = $bank->getAddress();
        $res['phone'] = $bank->getPhone();
        $res['description'] = $bank->getDescription();
        $res['city'] = $bank->getParent()->getCity()->getName();

        return new Response(json_encode($res));
    }

    /**
     * @Route("/notnallist/", name="ajax_notnal_list")
     */
    public function ajaxGetNotNalList( Request $request){
        $exchangersRates = $this
            ->getDoctrine()
            ->getRepository('AppBundle:ExchangeRate')
            ->findByType(ExchangeRate::TYPE_NO_CASH);
        $count = intval($request->get('count'));
        return $this->render(':templates:notnal_list.html.twig', compact('exchangersRates', 'count'));
    }

    /**
     * @Route("/nacbank/{code}/", name="ajax_nacbank_list")
     */
    public function ajaxNacbanklList($code = 0, Request $request){
        $currency = $this->getDoctrine()->getRepository('AppBundle:Currency')->findOneByCode(strtoupper($code));
        $history = $currency->getHistory();
        ksort($history);
        $temp = [];
        foreach($history as $key => $value){
            $temp[] = [ $key, floatval($value)];
        }

        return new JsonResponse($temp);
    }

    /**
     * @Route("/voit-chois/{chois}/{code}/", name="ajax_voit_chois")
     */
    public function ajaxVoitShois($chois, $code, Request $request){

        $this->get('session')->set('voit'.$code, $chois);
        $em = $this->getDoctrine()->getManager();
        $voit = new VotingItem();
        $voit->setChoice($chois);
        $voit->setVoteAt(new \DateTime());
        $voit->setCurCode($code);
        $em->persist($voit);
        $em->flush();
        return new JsonResponse(true);
    }

    /**
     * @Route("/no-cash-bank/{city}/{bank}/{page}/", name="ajax_no_cash_bank")
     */
    public function bankNoCashAction(City $city, Bank $bank, $page){
        $exchangersRates = $this
            ->getDoctrine()
            ->getRepository('AppBundle:ExchangeRate')
            ->findBy(['type' => ExchangeRate::TYPE_NO_CASH], null, 5, $page*5);
        $hasMore = $this
            ->getDoctrine()
            ->getRepository('AppBundle:ExchangeRate')
            ->findBy(['type' => ExchangeRate::TYPE_NO_CASH], null, 5, (1+ $page)*5);
        $currencies = $this->getDoctrine()->getRepository('AppBundle:Currency')->getExchangeCurrencies();
        return $this->render('bank/kysry/no_cash_list.html.twig', compact('exchangersRates', 'currencies', 'city', 'hasMore'));
    }

    /**
     * @Route("/cash-bank/{city}/{bank}/{page}/", name="ajax_bash_bank")
     */
    public function bankCashAction(City $city, Bank $bank, $page){
        $ratesInOthersBanks = $this->getDoctrine()
            ->getRepository('AppBundle:ExchangeRate')
            ->getRatesInOtherBanks($city, $bank, null, null, $page);
        $hasMore = $this->getDoctrine()
            ->getRepository('AppBundle:ExchangeRate')
            ->getRatesInOtherBanks($city, $bank, null, null, $page+1);
        $currencies = $this->getDoctrine()->getRepository('AppBundle:Currency')->getExchangeCurrencies();
        return $this->render('bank/kysry/cash_list.html.twig', compact('ratesInOthersBanks', 'currencies', 'city', 'hasMore'));
    }

    public function credit_calc(Request $request)
    {
        $all = $request->data;
        $all_parsed = [];
        parse_str($all, $all_parsed);
        $all_parsed_without_none = [];
        $all_parsed['calc']['tot'] = str_replace(' ', '', $all_parsed['calc']['tot']);
        foreach ($all_parsed['calc'] as $key => $item) {
            if($item != 'none' && $key != 'tot' && $key != 'period'){
                if($key == 'curr'){
                    $all_parsed_without_none['currency'] = $item;
                }
                else{
                    $all_parsed_without_none[$key] = $item;
                }
            }
        }

        $cr = Credit::leftJoin('credit_props', 'credits.id', '=', 'credit_props.credit_id')
                        ->where('percent_rate', '!=', null)
                        /*->
                        where('credit_props.min_amount', '>=', $all_parsed['calc']['tot'])->
                        where('credit_props.max_amount', '<=', $all_parsed['calc']['tot'])->
                        where('credit_props.min_period', '=>', $all_parsed['calc']['period'])->
                        where('credit_props.max_period', '<=', $all_parsed['calc']['period'])*/

        ;
        foreach ($all_parsed_without_none as $key => $item) {
            $cr = $cr->where($key, $item);
        }
        $cr = $cr->
//            groupBy('credits.id')->
            orderBy('credit_props.percent_rate', 'asc')
            ->get();
        foreach ($cr as $item) {

            $credit_security = [
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

            $currency = [
                'kzt' => '₸',
                'usd' => '$',
                'eur' => '€',
            ];

            if(isset($credit_security[$item->credit_security])){
                $item->credit_security = $credit_security[$item->credit_security];
            }

            if(isset($currency[$item->currency])){
                $item->currency = $currency[$item->currency];
            }

            $fees = CreditPropFee::where('credit_prop_id', $item['credit_props']['id'])->first();

            $fee_arr = [];
            if ($fees != null) {
                if($fees->review != null && $fees->review_input != null){
                    $fee_arr[] = [
                        'fee_amount' => $fees->review_input,
                        'fee_rate' => $fees->review,
                    ];
                }
                //todo: продолжить для остальных комиссий
            }

            $options['initial_fee'] = 0;
            $options['rate'] = $item->percent_rate;
            $options['tot'] = $all_parsed['calc']['tot'] ?? 300000;
            $options['period'] = $all_parsed['calc']['period'] ?? 12;

            $result = \CalcHelper::calculate_credit($options['period'], $options['tot'], $options['rate'], 1, $options['initial_fee']);
            $item->ppm = $result['ppm'][0];
            $item->overpay = $result['procentAmount'];
            $item->percent = $options['rate'];
        }

//        $filtered_credits = Credit::where('credit_goal', $all_parsed['calc']['credit_goal'])->
//                        leftJoin('credit_props', 'credits.id', '=', 'credit_props.credit_id')->
//                        where('credit_formalization', $all_parsed['calc']['credit_formalization'])->
//                        where('currency', $all_parsed['calc']['curr'])->
//                        where('have_citizenship', $all_parsed['calc']['have_citizenship'])->
//                        where('have_early_repayment', $all_parsed['calc']['have_early_repayment'])->
//                        where('receive_mode', $all_parsed['calc']['receive_mode'])->
//                        where('registration', $all_parsed['calc']['registration'])
//
////                ->props()
//                ->where('credit_props.credit_security', $all_parsed['calc']['credit_security'])
//                ->where('credit_props.income_confirmation', $all_parsed['calc']['income_confirmation'])
//                ->where('credit_props.income_project', $all_parsed['calc']['income_project'])
//                ->where('credit_props.min_period', '>=', $all_parsed['calc']['period'])
//                ->where('credit_props.max_period', '<=', $all_parsed['calc']['period'])
//                ->where('credit_props.repayment_structure', $all_parsed['calc']['repayment_structure'])
//                ->where('credit_props.min_amount', '>=', $all_parsed['calc']['tot'])
//                ->where('credit_props.max_amount', '<=', $all_parsed['calc']['tot'])
//        ->get();

        $returnHTML = '';
        foreach ($cr as $item) {
            $returnHTML .= view('credit.index_credit_block')->with('credit', $item)->render();
        }

        if($returnHTML == ''){
            $returnHTML .= view('credit.nothing_found')->render();
        }

        return response()->json(array('success' => true, 'html' => $returnHTML, 'cr' => $cr));

    }

    public function compare(Request $request, $product, $id)
    {

//        $exists = session()->has('compare.'.$product);
        $exists = isset($_SESSION['compare'][$product]);

        if(!$exists){
            $compare = [
                $product => [$id],
            ];

//            session('compare', $compare);
            $_SESSION['compare'] = $compare;
        }
        else{
//            $compare = session('compare');
            $compare = $_SESSION['compare'];
            $compare['credit'][] = $id;
            $_SESSION['compare'] = $compare;

//            session('compare', $compare);
        }

        $comparisonList = null;
        if ($product == 'credit'){
            $comparisonList = CreditProp::find($compare['credit']);
            foreach ($comparisonList as $item) {
                $item->logo = $item->credit->bank->logo;
                $item->granting = $item->fees()->whereNotNull('granting_input')->first();
                if($item->granting){
                    $item->granting = $item->granting->granting_input;
                }
//                $fees[$item->id]['granting'] = $item->fees()->whereNotNull('granting_input')->first();
            }
        }


        $returnHTML = '';
//        foreach ($props as $item) {
            $returnHTML = view('common.comparison_list')->with('products', $comparisonList)->render();
//        }

//        $compare = $this->get(CompareManager::class);
//        $compare->init(Credit::class, 'kredity', ':templates:kredity_list.html.twig',$props);

        return response()->json(array('success' => true, 'html' => $returnHTML, 'action' => 'add'));

//        return $compare->$act($id);
    }

    public function compareList($product)
    {
//        $exists = session()->has('compare.'.$product);
        $exists = isset($_SESSION['compare'][$product]);

        if($exists){
            if ($product == 'credit'){

//                $compare = session('compare.'.$product);
                $compare = $_SESSION['compare'][$product];


                $comparisonList = CreditProp::find($compare);
                foreach ($comparisonList as $item) {
                    $item->logo = $item->credit->bank->logo;
                }

                $returnHTML = view('common.comparison_list')->with('products', $comparisonList)->render();
                return response()->json(array('success' => true, 'html' => $returnHTML, 'action' => 'add'));
            }
        }

        return response()->json(array('success' => false, 'html' => '', 'action' => 'add'));

    }
}



