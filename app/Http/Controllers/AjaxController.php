<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
}



