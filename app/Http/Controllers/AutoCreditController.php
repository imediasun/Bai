<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Admin\CompareController;
use AppBundle\Entity\AutoCredit;
use AppBundle\Entity\Bank;
use AppBundle\Entity\City;
use AppBundle\Entity\ExchangeRate;
use AppBundle\Entity\Prop;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/avtokredity")
 */
class AutoCreditController extends RouteController
{
    public $mainBradcrumbsName = 'Кредит на автомобиль в [TODO getCity in controller]';
    /**
     * @Route("/compare/{act}/{id}/", name="autocredits_compare",
     *          requirements={"id": "\d+"},
     *          options={"expose"=true},
     * )
     */
    public function compareAction($act, $id = null)
    {
        $props = $this->getDoctrine()->getRepository('AppBundle:CompareItem')->findBy(['type'=>CompareController::AUTO_CREDIT_COMPARE]);

        $compare = $this->get(CompareManager::class);
        $compare->init(AutoCredit::class, 'avtokredity', ':templates:avtokredity_list.html.twig', $props);
        return $compare->$act($id);
    }

    /**
     * @Route("/", name="auto_credit_index")
     * @Route("/", name="autoCredit_index")
     * @Method("GET")
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $this->setRoutePrefix($request, 'autoCredit');

        if ($this->searchFilter) {
            $filter = str_replace('prop.', 'cp.', $this->searchFilter);
            $autoCredits = $this->getDoctrine()->getRepository('AppBundle:AutoCredit')->findFiltered($filter);
        } else {
            $autoCredits = $this->getDoctrine()->getRepository('AppBundle:AutoCredit')->findBy(['parent' => null]);
        }

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Главная", "homepage");
        //TODO: получить текущий город человека
        $breadcrumbs->addItem($this->mainBradcrumbsName);

        $params = [
            'autoCredits' => $autoCredits,
        ];

        return $this->render(':auto_credit:index.html.twig', $params);
    }

    /**
     * @Route("/{altName}/", name="auto_credit_page")
     * @Route("/{altName}/", name="autoCredit_page")
     * @Route("/{altName}/", name="autocredit_page")
     * @Route("/{altName}/{city}/", name="autocredit_page_bank_city", defaults={"city" = "null" })
     * @Method("GET")
     *
     * @param $altName
     * @param $city
     * @param Request $request
     *
     * @return Response
     */
    public function autoCreditAction($altName, $city = null, Request $request)
    {
        $this->setRoutePrefix($request, 'autoCredit');
        $tools = $this->get('app.tools');
        // Check if altName is a City
        if($city == null){
            $cityManager = $this->get("app.city_manager");
            $cityManager->setCity($altName);
            $city = $cityManager->getCityEntity();
        }else{
            $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByAltName($city != null ? $city : $altName);
        }

        $bank = $this->getDoctrine()->getRepository('AppBundle:Bank')->findOneBy(['parent' => null, 'altName' => $altName]);

        if ($city && $bank) {

            $autoCredits = $this->getDoctrine()->getRepository('AppBundle:AutoCredit')->findBy(['parent' => null, 'bank' => $bank]);
        }

        if ($city) {

            if(!isset($autoCredits)){
                $autoCredits = $this->getDoctrine()->getRepository('AppBundle:AutoCredit')->findByCity($city);
            }

            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addItem("Авто кредиты", "auto_credit_index");

            //найти ставку и посчитать переплату сразу
            $options = [];
            $creditOpts = [];
            $options['initial_fee'] = 0;
            $options['rate'] = 0;
            foreach($autoCredits as $index => $credit){
                $creditProps = $this->getDoctrine()->getRepository('AppBundle:AutoCreditProp')->getAutoCreditProps($credit);
                foreach ($creditProps as $i => $pr){
                    if($pr['altName'] == 'procentnaya-stavka'){
                        $options['rate'] = $pr['valueFrom'];
                    }
                    if($pr['altName'] == 'pervichnyi-vznos'){
                        $options['initial_fee'] = $pr['valueFrom'];
                    }
                }
                $result = $tools::calculate_credit(36, 3000000, $options['rate'], 1, $options['initial_fee']);
                $creditOpts[$index]['bloc_prop'] = [
                    'percentRate' => $options['rate'],
                    'ppm' => round($result['ppm'][0]),
                    'overpay' => round($result['procentAmount']),
                ];
            }

            $params = [
                'autoCredits' => $autoCredits,
                'options' => $creditOpts,
                'city' => $city,
            ];

            return $this->render(':auto_credit:index.html.twig', $params);
        }

        // Check if altName is a Fast Filter
        $fastFilter = $this->getDoctrine()->getRepository('AppBundle:FastFilter')->findOneBy([
                'product' => $this->getDoctrine()->getRepository('AppBundle:Product')->findOneByName('AUTOCREDIT'),
                'altName' => $altName]
        );
        if ($fastFilter) {
            $tools = $this->get('app.tools');
            $filter = $tools->getFastFilterWhere($fastFilter);
            $filter = str_replace('prop.', 'cp.', $filter);
            $autoCredits = $this->getDoctrine()->getRepository('AppBundle:AutoCredit')->findFiltered($filter);

            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addItem("Авто кредиты", "auto_credit_index");

            $params = [
                'autoCredits' => $autoCredits,
                'filter' => $tools->getFastFilterPropsArray($fastFilter),
            ];

            return $this->render(':auto_credit:index.html.twig', $params);
        }

        // Check if altName is a AutoCredit
        /* @var $autoCredit AutoCredit */
        $autoCredit = $this->getDoctrine()->getRepository('AppBundle:AutoCredit')->findOneBy(['parent' => null, 'altName' => $altName]);
        if ($autoCredit) {
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addRouteItem("Авто кредиты", "auto_credit_index");
            $breadcrumbs->addItem($autoCredit->getName());

            $autoCreditProps = $this->getDoctrine()->getRepository('AppBundle:AutoCreditProp')->getAutoCreditProps($autoCredit);

            $bank = $this->getDoctrine()->getRepository('AppBundle:Bank')->find($autoCredit->getBank());
            $branches = $this->getDoctrine()->getRepository('AppBundle:Bank')->findByParent($bank);
            $atms = $this->getDoctrine()->getRepository('AppBundle:Atm')->findByBank($bank);

            $terms = [];
            foreach ($autoCreditProps as $creditProp) {
                if($creditProp['altName'] == 'period'){
                    $terms['from'] = $creditProp['valueFrom'];
                    $terms['to'] = $creditProp['valueTo'];
                }
            }

            $creditTerms = !empty($terms) ? $tools::getCreditTerms($terms['from'], $terms['to']) : '';

            $type = 1;
            foreach ($autoCreditProps as $item){
                if(strpos($item['optionValue'], 'аннуитетная' ) !== false){
                    $type = 1;
                    break;
                }
                elseif (strpos($item['optionValue'], 'дифференци') !== false){
                    $type = 2;
                    break;
                }
            }

            $params = [
                'autoCredit' => $autoCredit,
                'bank' => $bank,
                'branches' => $branches,
                'atms' => $atms,
                'branchCount' => count($branches),
                'atmCount' => count($atms),
                'autoCreditProps' => $autoCreditProps,
                'creditTerms' => $creditTerms,
                'payment_type' => $type,
            ];

            return $this->render(':auto_credit:page.html.twig', $params);
        }

        // Else redirect to credit list
        return $this->redirectToRoute('auto_credit_index');

    }

    /**
     * @Route("/{bankName}/{altName}/", name="auto_credit_page_single")
     * @Method("GET")
     *
     * @param $bankName
     * @param $altName
     * @param Request $request
     *
     * @return Response
     */
    public function autoCreditPageAction($bankName, $altName, Request $request)
    {
        $this->setRoutePrefix($request, 'autoCredit');
        $tools = $this->get('app.tools');
        $cityManager = $this->get("app.city_manager");
        $city = $cityManager->getCityEntity();

        // Check if altName is a AutoCredit
        /* @var $autoCredit AutoCredit */
        $autoCredit = $this->getDoctrine()->getRepository('AppBundle:AutoCredit')->findOneBy(['parent' => null, 'altName' => $altName]);
        if ($autoCredit) {
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addRouteItem($this->mainBradcrumbsName, "auto_credit_index");
            $breadcrumbs->addItem($autoCredit->getName());

            $autoCreditProps = $this->getDoctrine()->getRepository('AppBundle:AutoCreditProp')->getAutoCreditProps($autoCredit);

            $bank = $this->getDoctrine()->getRepository('AppBundle:Bank')->find($autoCredit->getBank());
            $branches = $this->getDoctrine()->getRepository('AppBundle:Bank')->findByParent($bank);
            $atms = $this->getDoctrine()->getRepository('AppBundle:Atm')->findByBank($bank);

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

            $terms = [];
            foreach ($autoCreditProps as $creditProp) {
                if($creditProp['altName'] == 'period'){
                    $terms['from'] = $creditProp['valueFrom'];
                    $terms['to'] = $creditProp['valueTo'];
                }
            }

            $creditTerms = !empty($terms) ? $tools::getCreditTerms($terms['from'], $terms['to']) : '';

            $type = 1;
            foreach ($autoCreditProps as $item){
                if(strpos($item['optionValue'], 'аннуитетная' ) !== false){
                    $type = 1;
                    break;
                }
                elseif (strpos($item['optionValue'], 'дифференци') !== false){
                    $type = 2;
                    break;
                }
            }

            $childAutocredits = [];
            $children = $this->getDoctrine()->getRepository('AppBundle:AutoCredit')->findBy(['parent' => $autoCredit]);
            foreach ($children as $child) {
                $childAutocredits[] = [
                    'credit' => $child,
                    'creditProps' => $this->getDoctrine()->getRepository('AppBundle:AutoCreditProp')->getAutoCreditProps($child),
                ];
            }

            $params = [
                'autoCredit' => $autoCredit,
                'childAutocredits' => $childAutocredits,
                'bank' => $bank,
                'branches' => $branches,
                'atms' => $atms,
                'branchCount' => count($branches),
                'atmCount' => count($atms),
                'autoCreditProps' => $autoCreditProps,
                'creditTerms' => $creditTerms,
                'payment_type' => $type,
                'currencies' => $currencies,
                'bankCashRates' => $bankRates,
                'bankCashLastRates' => $bankLastRates,
                'from_listing' => $tools->getSessionFilterData('autocredit'),
            ];

            return $this->render(':auto_credit:page.html.twig', $params);
        }
        // Else redirect to credit list
//        return $this->redirectToRoute('auto_credit_index');
        return $this->redirectToRoute('autocredit_page_bank_city', ['altName' => $altName, 'city' => $city,]);

    }

    /**
     * @Route("/ajax/getFiltered/", name="credit_auto_ajax_get_filtered",
     *          options={"expose"=true},
     *          condition="request.isXmlHttpRequest()"
     * )
     * @Method({"POST"})
     */
    public function getFilteredAction(Request $request)
    {
        $this->setRoutePrefix($request, 'autoCredit');

        $tools = $this->get('app.tools');
        $filter = $tools->getFilteredWhere($request->request->get('data'));
        if ($filter) {
            $filter = str_replace('prop.', 'cp.', $filter);
            $autoCredits = $this->getDoctrine()->getRepository('AppBundle:AutoCredit')->findFiltered($filter);
        } else {
            $autoCredits = $this->getDoctrine()->getRepository('AppBundle:AutoCredit')->findBy(['parent' => null]);
        }
        $output = '';
        if ($autoCredits) {
            foreach ($autoCredits as $key => $autoCredit) {
                $output .= $this->renderView('auto_credit/index_credit_block.html.twig', [
                    'autoCredit' => $autoCredit,
                    'autoCredits' => $autoCredits,
                    'key' => $key,
                ]);
            }
        } else {
            $output = $this->renderView(':common:nothing_found.html.twig');
        }

        $jsonResponseHelper = $this->get('app.json_response_helper');
        $response = $jsonResponseHelper->prepareJsonResponse();

        $response->setData($output);

        return $response;
    }

    /**
     * @Route("/ajax/autocredit/calculate/", name="autocredit_ajax_calc",
     *          options={"expose"=true},
     *          condition="request.isXmlHttpRequest()"
     * )
     * @Method({"POST"})
     */
    public function getCalcAction(Request $request)
    {
        $this->setRoutePrefix($request, 'credit');

        $tools = $this->get('app.tools');
        $all = [];
        foreach ($request->request->get('data') as $item){
            if($item['name'] == 'amount'){
                $all['amount'] = $item['value'];
                $_SESSION['autocredit.amount'] = $item['value'];
            }
            elseif ($item['name'] == 'percent'){
                $all['percent'] = $item['value'];
                $_SESSION['autocredit.percent'] = $item['value'];
            }
            elseif ($item['name'] == 'term'){
                $all['term'] = $item['value'];
                $_SESSION['autocredit.term'] = $item['value'];
            }
            elseif ($item['name'] == 'scheme'){
                $all['type'] = $item['value'];
                $_SESSION['autocredit.type'] = $item['value'];
            }
        }
        $output = '';
        $output .= $this->renderView('auto_credit/credit_calc_result.html.twig',
            [
                'amount' => $all['amount'],
                'percent' => $all['percent'],
                'term' => $all['term'],
                'type' => $all['type'],
            ]);

        $jsonResponseHelper = $this->get('app.json_response_helper');
        $response = $jsonResponseHelper->prepareJsonResponse();

        $response->setData($output);

        return $response;
    }

    /**
     * @Route("/ajax/autocredits_percent/", name="autocredits_ajax_percent",
     *          options={"expose"=true},
     *          condition="request.isXmlHttpRequest()"
     * )
     * @Method({"POST"})
     */
    public function getPercentAction(Request $request)
    {
        $this->setRoutePrefix($request, 'auto_credit');

        $tools = $this->get('app.tools');
        $all = $request->request->all();
        $_SESSION['autocredit.amount'] = $all['amount'];
        $output = '';
        $autoCredits = $this->getDoctrine()->getRepository('AppBundle:AutoCredit')->filterGetPercent($all['term']);
        if ($autoCredits) {
            foreach ($autoCredits as $autoCredit) {
                $output .= $this->renderView('auto_credit/index_credit_block.html.twig', ['autoCredit' => $autoCredit]);
            }
        } else {
            $output = $this->renderView(':common:nothing_found.html.twig');
        }

        $jsonResponseHelper = $this->get('app.json_response_helper');
        $response = $jsonResponseHelper->prepareJsonResponse();

        $response->setData($output);

        return $response;
    }

    /**
     * @Route("/ajax/autocredits/amount/", name="autocredits_ajax_amount",
     *          options={"expose"=true},
     *          condition="request.isXmlHttpRequest()"
     * )
     * @Method({"POST"})
     */
    public function getAmountAction(Request $request)
    {
        $this->setRoutePrefix($request, 'auto_credit');

        $tools = $this->get('app.tools');
        $all = $request->request->all();
        $_SESSION['autocredit.amount'] = $all['amount'];

        $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByAltName($all['city']);

        $output = '';

        $credits = $this->getDoctrine()->getRepository('AppBundle:AutoCredit')->findByCity($city);

        $options = [];
        $mortgage_opts = [];
        foreach($credits as $index => $autocredit){
            $creditProps = $this->getDoctrine()->getRepository('AppBundle:AutoCreditProp')->getAutoCreditProps($autocredit);
            foreach ($creditProps as $i => $pr){
                if($pr['altName'] == 'procentnaya-stavka'){
                    $options['rate'] = $pr['valueFrom'];
                    $_SESSION['autocredit.percent'] = $pr['valueFrom'];
                }
                if($pr['altName'] == 'pervichnyi-vznos'){
                    $options['initial_fee'] = $pr['valueFrom'];
                }
            }
            $result = $tools::calculate_credit($all['term'], $all['amount'], $options['rate'], 1, $options['initial_fee']);
            $mortgage_opts[$index]['bloc_prop'] = [
                'percentRate' => $options['rate'],
                'ppm' => round($result['ppm'][0]),
                'overpay' => round($result['procentAmount']),
            ];
        }
        if ($credits) {
            foreach ($credits as $key => $autocredit) {
                $output .= $this->renderView('auto_credit/index_credit_block.html.twig', [
                    'autoCredit' => $autocredit,
                    'key' => $key,
                    'options' => $mortgage_opts,
                ]);
            }
        } else {
            $output = $this->renderView(':common:nothing_found.html.twig');
        }

        $jsonResponseHelper = $this->get('app.json_response_helper');
        $response = $jsonResponseHelper->prepareJsonResponse();

        $response->setData($output);

        return $response;
    }

}
