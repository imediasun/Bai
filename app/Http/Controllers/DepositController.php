<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Admin\CompareController;
use AppBundle\Entity\Bank;
use AppBundle\Entity\City;
use AppBundle\Entity\Deposit;
use AppBundle\Entity\ExchangeRate;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/depozity")
 */
class DepositController extends RouteController
{

    /**
     * @Route("/compare/{act}/{id}/", name="deposits_compare",
     *          requirements={"id": "\d+"},
     *          options={"expose"=true},
     * )
     */
    public function compareAction($act, $id = null)
    {
        $props = $this->getDoctrine()->getRepository('AppBundle:CompareItem')->findBy(['type'=>CompareController::DEPOSIT_COMPARE]);

        $compare = $this->get(CompareManager::class);
        $compare->init(Deposit::class, 'depozity', ':templates:depozity_list.html.twig', $props);
        return $compare->$act($id);
    }

    /**
     * @Route("/", name="deposit_index")
     * @Method("GET")
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $this->setRoutePrefix($request, 'deposit');

        if ($this->searchFilter) {
            $filter = str_replace('prop.', 'cp.', $this->searchFilter);
            $deposits = $this->getDoctrine()->getRepository('AppBundle:Deposit')->findFiltered($filter);
        } else {
            $deposits = $this->getDoctrine()->getRepository('AppBundle:Deposit')->findBy(['parent' => null]);
        }

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Главная", "homepage");
        $breadcrumbs->addItem("Депозиты", "deposit_index");

        $params = [
            'deposits' => $deposits,
        ];

        return $this->render(':deposit:index.html.twig', $params);
    }

    /**
     * @Route("/{altName}/", name="deposit_page")
     * @Method("GET")
     *
     * @param $altName
     * @param Request $request
     *
     * @return Response
     */
    public function depositAction($altName, Request $request)
    {
        $this->setRoutePrefix($request, 'deposit');

        // Check if altName is a City
        $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByAltName($altName);
        if ($city) {
            $cityManager = $this->get("app.city_manager");
            $cityManager->setCity($altName);

            $deposits = $this->getDoctrine()->getRepository('AppBundle:Deposit')->findByCity($city);

            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addItem("Депозиты", "deposit_index");

            $childDeposits = [];
            foreach ($deposits as $deposit) {
                $children = $this->getDoctrine()->getRepository('AppBundle:Deposit')->findBy(['parent' => $deposit]);

                foreach ($children as $child) {
                    $childDeposits[] = [
                        'deposit' => $child,
                        'depositProps' => $this->getDoctrine()->getRepository('AppBundle:DepositProp')->getDepositProps($child),
                    ];
                }
            }

            $params = [
                'deposits' => $deposits,
                'city' => $city,
            ];

            return $this->render(':deposit:index.html.twig', $params);
        }

        // Check if altName is a Fast Filter
        /* @var $deposit Deposit */
        $fastFilter = $this->getDoctrine()->getRepository('AppBundle:FastFilter')->findOneBy([
                'product' => $this->getDoctrine()->getRepository('AppBundle:Product')->findOneByName('DEPOSIT'),
                'altName' => $altName]
        );
        if ($fastFilter) {
            $tools = $this->get('app.tools');
            $filter = $tools->getFastFilterWhere($fastFilter);
            $filter = str_replace('prop.', 'cp.', $filter);
            $deposits = $this->getDoctrine()->getRepository('AppBundle:Deposit')->findFiltered($filter);

            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addItem("Депозиты", "deposit_index");

            $params = [
                'deposits' => $deposits,
                'filter' => $tools->getFastFilterPropsArray($fastFilter),
            ];

            return $this->render(':deposit:index.html.twig', $params);
        }

        // Check if altName is a Deposit
        /* @var $deposit Deposit */
        $deposit = $this->getDoctrine()->getRepository('AppBundle:Deposit')->findOneBy(['parent' => null, 'altName' => $altName]);
        if ($deposit) {
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addRouteItem("Депозиты", "deposit_index");
            $breadcrumbs->addItem($deposit->getName());

            $depositProps = $this->getDoctrine()->getRepository('AppBundle:DepositProp')->getDepositProps($deposit);

            $bank = $this->getDoctrine()->getRepository('AppBundle:Bank')->find($deposit->getBank());
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

            $params = [
                'deposit' => $deposit,
                'bank' => $bank,
                'branches' => $branches,
                'atms' => $atms,
                'branchCount' => count($branches),
                'atmCount' => count($atms),
                'depositProps' => $depositProps,
                'currencies' => $currencies,
                'bankCashRates' => $bankRates,
                'bankCashLastRates' => $bankLastRates,
            ];

            return $this->render(':deposit:page.html.twig', $params);
        }

        // Else redirect to deposit list
        return $this->redirectToRoute('deposit_index');

    }

    /**
     * @Route("/ajax/getFiltered/", name="deposit_ajax_get_filtered",
     *          options={"expose"=true},
     *          condition="request.isXmlHttpRequest()"
     * )
     * @Method({"POST"})
     */
    public function getFilteredAction(Request $request)
    {
        $this->setRoutePrefix($request, 'deposit');

        $tools = $this->get('app.tools');
        $filter = $tools->getFilteredWhere($request->request->get('data'));
        if ($filter) {
            $filter = str_replace('prop.', 'cp.', $filter);
            $deposits = $this->getDoctrine()->getRepository('AppBundle:Deposit')->findFiltered($filter);
        } else {
            $deposits = $this->getDoctrine()->getRepository('AppBundle:Deposit')->findBy(['parent' => null]);
        }
        $output = '';
        if ($deposits) {
            foreach ($deposits as $deposit) {
                $output .= $this->renderView('deposit/index_deposit_block.html.twig', ['deposit' => $deposit]);
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
     * @Route("/ajax/deposit/calculate/", name="deposit_ajax_calc",
     *          options={"expose"=true},
     *          condition="request.isXmlHttpRequest()"
     * )
     * @Method({"POST"})
     */
    public function getCalcAction(Request $request)
    {
        $this->setRoutePrefix($request, 'deposit');

        $tools = $this->get('app.tools');
        $all = [];
        $all['amount'] = 0;
        $all['percent'] = 1;
        $all['term'] = 0;
        $all['type'] = 1;
        $all['renewal'] = 1;
        $all['renewal_rate'] = 1;

        foreach ($request->request->get('data') as $item){
            if($item['name'] == 'amount'){
                $all['amount'] = $item['value'];
                $_SESSION['deposit.amount'] = $item['value'];
            }
            elseif ($item['name'] == 'percent'){
                $all['percent'] = $item['value'];
                $_SESSION['deposit.percent'] = $item['value'];
            }
            elseif ($item['name'] == 'term'){
                $all['term'] = $item['value'];
                $_SESSION['deposit.term'] = $item['value'];
            }
            elseif ($item['name'] == 'capitalization'){
                $all['type'] = $item['value'];
                $_SESSION['deposit.type'] = $item['value'];
            }
            elseif ($item['name'] == 'renewal'){
                $all['renewal'] = $item['value'];
                $_SESSION['deposit.renewal'] = $item['value'];

            }
            elseif ($item['name'] == 'renewal_rate'){
                $all['renewal_rate'] = $item['value'];
                $_SESSION['deposit.renewal_rate'] = $item['value'];
            }
        }
        $output = '';
        $output .= $this->renderView('deposit/deposit_calc_result.html.twig',
            [
                'amount' => $all['amount'],
                'percent' => $all['percent'],
                'term' => $all['term'],
                'capitalization' => 1,
                'renewal' => $all['renewal'],
                'renewal_rate' => $all['renewal_rate'],
            ]);

        $jsonResponseHelper = $this->get('app.json_response_helper');
        $response = $jsonResponseHelper->prepareJsonResponse();

        $response->setData($output);

        return $response;
    }

    /**
     * @Route("/ajax/deposit/percent/", name="deposit_ajax_percent",
     *          options={"expose"=true},
     *          condition="request.isXmlHttpRequest()"
     * )
     * @Method({"POST"})
     */
    public function getPercentAction(Request $request)
    {
        $this->setRoutePrefix($request, 'deposit');

        $all = $request->request->all();
        $_SESSION['deposit.amount'] = $all['amount'];

        $deposit = $this->getDoctrine()->getRepository('AppBundle:Deposit')->findOneBy([
            'id' => $all['product_id'],
        ]);

        $depositProps = $this->getDoctrine()->getRepository('AppBundle:DepositProp')->getDepositPercent([
            'id' => $all['product_id'],
            'currency' => $all['currency'],
        ]);

        $output = [
            'amount' => $depositProps,
        ];

        $jsonResponseHelper = $this->get('app.json_response_helper');
        $response = $jsonResponseHelper->prepareJsonResponse();

        $response->setData($output);

        return $response;
    }

    /**
     * @Route("/{bankName}/{altName}/", name="deposit_page_single")
     * @Method("GET")
     * @param $bankName
     * @param $altName
     * @param Request $request
     *
     * @return Response
     */
    public function depositPageAction($bankName, $altName, Request $request)
    {
        $this->setRoutePrefix($request, 'deposit');
        $tools = $this->get('app.tools');
        $cityManager = $this->get("app.city_manager");
        $city = $cityManager->getCityEntity();

        // Check if altName is a Deposit
        /* @var $deposit Deposit */
        $deposit = $this->getDoctrine()->getRepository('AppBundle:Deposit')->findOneBy(['parent' => null, 'altName' => $altName]);
        if ($deposit) {
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addRouteItem("Депозиты", "deposit_index");
            $breadcrumbs->addItem($deposit->getName());

            $depositProps = $this->getDoctrine()->getRepository('AppBundle:DepositProp')->getDepositProps($deposit);

            $bank = $this->getDoctrine()->getRepository('AppBundle:Bank')->find($deposit->getBank());
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
            foreach ($depositProps as $creditProp) {
                if($creditProp['altName'] == 'period'){
                    $terms['from'] = $creditProp['valueFrom'];
                    $terms['to'] = $creditProp['valueTo'];
                }
            }

            $depositTerms = !empty($terms) ? $tools::getCreditTerms($terms['from'], $terms['to']) : '';

            $childDeposits = [];
            $children = $this->getDoctrine()->getRepository('AppBundle:Deposit')->findBy(['parent' => $deposit]);
            foreach ($children as $child) {
                $childDeposits[] = [
                    'deposit' => $child,
                    'depositProps' => $this->getDoctrine()->getRepository('AppBundle:DepositProp')->getDepositProps($child),
                ];
            }

            $from_listing = $tools->getSessionFilterData('deposit');

            $params = [
                'deposit' => $deposit,
                'childDeposits' => $childDeposits,
                'bank' => $bank,
                'branches' => $branches,
                'atms' => $atms,
                'branchCount' => count($branches),
                'atmCount' => count($atms),
                'depositProps' => $depositProps,
                'depositTerms' => $depositTerms,
                'currencies' => $currencies,
                'bankCashRates' => $bankRates,
                'bankCashLastRates' => $bankLastRates,
                'from_listing' => $from_listing,

            ];

            return $this->render(':deposit:page.html.twig', $params);
        }
        $bank = $this->getDoctrine()->getRepository('AppBundle:Bank')->findOneBy(['parent' => null, 'altName' => $bankName]);
        $deposits = $this->getDoctrine()->getRepository('AppBundle:Deposit')->findBy(['parent' => null, 'bank' => $bank]);
        if ($deposits){
            $reviews = $this->getDoctrine()->getRepository('AppBundle:Review')->findBy(['parent' => null, 'approved' => true]);

            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addItem("Депозиты", "deposit_index");

//          найти ставку и посчитать переплату сразу
            $options = [];
            $creditOpts = [];
            $options['initial_fee'] = 0;
            foreach($deposits as $index => $deposit){
                $depositProps = $this->getDoctrine()->getRepository('AppBundle:DepositProp')->getDepositProps($deposit);
                foreach ($depositProps as $i => $pr){
                    if($pr['altName'] == 'procentnaya-stavka'){
                        $options['rate'] = $pr['valueFrom'];
                    }
                    if($pr['altName'] == 'pervichnyi-vznos'){
                        $options['initial_fee'] = $pr['valueFrom'];
                    }
                }
                $result = $tools::calculate_credit(12, 300000, $options['rate'], 1, $options['initial_fee']);
                $creditOpts[$index]['bloc_prop'] = [
                    'percentRate' => $options['rate'],
                    'ppm' => round($result['ppm'][0]),
                    'overpay' => round($result['procentAmount']),
                ];
            }

            $params = [
                'deposits' => $deposits,
                'reviews' => $reviews,
                'options' => $creditOpts,
                'city' => $city,
            ];

            return $this->render(':deposit:index.html.twig', $params);
        }

        // Else redirect to credit list
        return $this->redirectToRoute('credit_index');

    }

    /**
     * @Route("/ajax/deposit/amount/", name="deposits_ajax_amount",
     *          options={"expose"=true},
     *          condition="request.isXmlHttpRequest()"
     * )
     * @Method({"POST"})
     */
    public function getAmountAction(Request $request)
    {
        $this->setRoutePrefix($request, 'deposit');

        $tools = $this->get('app.tools');
        $all = $request->request->all();
        $_SESSION['deposit.amount'] = $all['amount'];

        $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByAltName($all['city']);

        $output = '';

        $credits = $this->getDoctrine()->getRepository('AppBundle:Deposit')->findByCity($city);

        $options = [];
        $depositOptions = [];
        $options['initial_fee'] = 0;
        foreach($credits as $index => $deposit){
            $creditProps = $this->getDoctrine()->getRepository('AppBundle:DepositProp')->getDepositProps($deposit);
            foreach ($creditProps as $i => $pr){
                if($pr['altName'] == 'procentnaya-stavka'){
                    $options['rate'] = $pr['valueFrom'];
                }
                if($pr['altName'] == 'pervichnyi-vznos'){
                    $options['initial_fee'] = $pr['valueFrom'];
                }
            }
            $result = $tools::calculate_deposit($all['term'], $all['amount'], $options['rate'], true, 0, 1);
            $depositOptions[$index]['bloc_prop'] = [
                'percentRate' => $options['rate'],
                'ppm' => round($result['ppm'][0]),
                'overpay' => round($result['procentAmount']),
            ];
        }
        if ($credits) {
            foreach ($credits as $key => $deposit) {
                $output .= $this->renderView('deposit/index_deposit_block.html.twig', [
                    'deposit' => $deposit,
                    'key' => $key,
                    'options' => $depositOptions,
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
