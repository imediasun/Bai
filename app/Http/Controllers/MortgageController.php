<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Admin\CompareController;
use AppBundle\Entity\Bank;
use AppBundle\Entity\City;
use AppBundle\Entity\ExchangeRate;
use AppBundle\Entity\Mortgage;
use AppBundle\Entity\MortgageProp;
use AppBundle\Entity\Prop;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/ipoteka")
 */
class MortgageController extends RouteController
{

    /**
     * @Route("/compare/{act}/{id}/", name="mortgages_compare",
     *          requirements={"id": "\d+"},
     *          options={"expose"=true},
     * )
     */
    public function compareAction($act, $id = null)
    {
        $props = $this->getDoctrine()->getRepository('AppBundle:CompareItem')->findBy(['type'=>CompareController::MORTGAGE_COMPARE]);
        $compare = $this->get(CompareManager::class);
        $compare->init(Mortgage::class, 'ipoteka', ':templates:ipoteka_list.html.twig', $props);
        return $compare->$act($id);
    }

    /**
     * @Route("/", name="mortgage_index")
     * @Method("GET")
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $this->setRoutePrefix($request, 'mortgage');

        $mortgages = $this->getDoctrine()->getRepository('AppBundle:Mortgage')->findBy(['parent' => null]);

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Главная", "homepage");
        $breadcrumbs->addItem("Ипотека", "mortgage_index");

        $params = [
            'mortgages' => $mortgages,
        ];

        return $this->render(':mortgage:index.html.twig', $params);
    }

    /**
     * @Route("/{altName}/", name="mortgage_page")
     * @Route("/{altName}/{city}", name="mortgage_page_with_city", defaults={"city" = "null"})
     * @Method("GET")
     *
     * @param $altName
     * @param $city
     * @param Request $request
     *
     * @return Response
     */
    public function mortgageAction($altName, $city = null, Request $request)
    {
        $this->setRoutePrefix($request, 'mortgage');
        $tools = $this->get('app.tools');

        // Check if altName is a City
        $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByAltName($city != null ? $city : $altName);
        $bank = $this->getDoctrine()->getRepository('AppBundle:Bank')->findOneBy(['parent' => null, 'altName' => $altName]);
        $mortgages = $this->getDoctrine()->getRepository('AppBundle:Mortgage')->findBy(['parent' => null, 'bank' => $bank]);

        if ($city) {
            $cityManager = $this->get("app.city_manager");
            $cityManager->setCity($city != null ? $city->getAltName() : $altName);

            if(!isset($mortgages)){
                $mortgages = $this->getDoctrine()->getRepository('AppBundle:Mortgage')->findByCity($city);
            }


            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addItem("Ипотека", "mortgage_index");

            //найти ставку и посчитать переплату сразу
            $options = [];
            $creditOpts = [];
            $options['initial_fee'] = 0;
            foreach($mortgages as $index => $credit){
                $creditProps = $this->getDoctrine()->getRepository('AppBundle:MortgageProp')->getMortgageProps($credit);
                foreach ($creditProps as $i => $pr){
                    if($pr['altName'] == 'procentnaya-stavka'){
                        $options['rate'] = $pr['valueFrom'];
                    }
                    if($pr['altName'] == 'pervichnyi-vznos'){
                        $options['initial_fee'] = $pr['valueFrom'];
                    }
                }
                if(is_null($options['initial_fee'])){
                    $options['initial_fee'] = 0;
                }
                $result = $tools::calculate_credit(120, 10000000, $options['rate'], 1, $options['initial_fee']);
                $creditOpts[$index]['bloc_prop'] = [
                    'percentRate' => $options['rate'],
                    'ppm' => round($result['ppm'][0]),
                    'overpay' => round($result['procentAmount']),
                ];
            }

            $params = [
                'mortgages' => $mortgages,
                'options' => $creditOpts,
                'city' => $city,
            ];

            return $this->render(':mortgage:index.html.twig', $params);
        }

        // Check if altName is a Fast Filter
        $fastFilter = $this->getDoctrine()->getRepository('AppBundle:FastFilter')->findOneBy([
                'product' => $this->getDoctrine()->getRepository('AppBundle:Product')->findOneByName('MORTGAGE'),
                'altName' => $altName]
        );
        if ($fastFilter) {
            $tools = $this->get('app.tools');
            $filter = $tools->getFastFilterWhere($fastFilter);
            $filter = str_replace('prop.', 'cp.', $filter);
            $mortgages = $this->getDoctrine()->getRepository('AppBundle:Mortgage')->findFiltered($filter);

            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addItem("Ипотека", "mortgage_index");

            $params = [
                'mortgages' => $mortgages,
                'filter' => $tools->getFastFilterPropsArray($fastFilter),
            ];

            return $this->render(':mortgage:index.html.twig', $params);
        }

        // Check if altName is a Mortgage
        /* @var $mortgage Mortgage */
        $mortgage = $this->getDoctrine()->getRepository('AppBundle:Mortgage')->findOneBy(['parent' => null, 'altName' => $altName]);
        if ($mortgage) {
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addRouteItem("Ипотека", "mortgage_index");
            $breadcrumbs->addItem($mortgage->getName());

            $mortgageProps = $this->getDoctrine()->getRepository('AppBundle:MortgageProp')->getMortgageProps($mortgage);

            $bank = $this->getDoctrine()->getRepository('AppBundle:Bank')->find($mortgage->getBank());
            $branches = $this->getDoctrine()->getRepository('AppBundle:Bank')->findByParent($bank);
            $atms = $this->getDoctrine()->getRepository('AppBundle:Atm')->findByBank($bank);

            $terms = [];
            foreach ($mortgageProps as $creditProp) {
                if($creditProp['altName'] == 'period'){
                    $terms['from'] = $creditProp['valueFrom'];
                    $terms['to'] = $creditProp['valueTo'];
                }
            }

            $mortgageTerms = !empty($terms) ? $tools::getCreditTerms($terms['from'], $terms['to']) : '';

            $from_listing = $tools->getSessionFilterData('credit');

            $params = [
                'mortgage' => $mortgage,
                'bank' => $bank,
                'branches' => $branches,
                'atms' => $atms,
                'branchCount' => count($branches),
                'atmCount' => count($atms),
                'mortgageProps' => $mortgageProps,
                'mortgageTerms' => $mortgageTerms,
                'from_listing' => $from_listing,
            ];

            return $this->render(':mortgage:page.html.twig', $params);
        }

        // Else redirect to mortgage list
        return $this->redirectToRoute('mortgage_index');

    }

    /**
     * @Route("/{bankName}/{altName}/", name="mortgage_page_single", defaults={"altName" = "null"})
     * @Method("GET")
     *
     * @param $bankName
     * @param $altName
     * @param Request $request
     *
     * @return Response
     */
    public function mortgagePageAction($bankName, $altName, Request $request)
    {
        $this->setRoutePrefix($request, 'mortgage');
        $tools = $this->get('app.tools');
        $cityManager = $this->get("app.city_manager");
        $city = $cityManager->getCityEntity();

        // Check if altName is a Mortgage
        /* @var $mortgage Mortgage */
        $bankAltName = $this->getDoctrine()->getRepository('AppBundle:Bank')->findOneByAltName($bankName);
        $mortgage = $this->getDoctrine()->getRepository('AppBundle:Mortgage')->findOneBy([
            'parent' => null,
            'altName' => $altName,
            'bank' => $bankAltName->getId(),
        ]);

        if ($mortgage) {
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addRouteItem("Ипотека", "mortgage_index");
            $breadcrumbs->addItem($mortgage->getName());

            $mortgageProps = $this->getDoctrine()->getRepository('AppBundle:MortgageProp')->getMortgageProps($mortgage);

            $bank = $this->getDoctrine()->getRepository('AppBundle:Bank')->find($mortgage->getBank());
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
            foreach ($mortgageProps as $creditProp) {
                if($creditProp['altName'] == 'period'){
                    $terms['from'] = $creditProp['valueFrom'];
                    $terms['to'] = $creditProp['valueTo'];
                }
            }

            $from_listing = $tools->getSessionFilterData('mortgage');

            $clildMortgages = [];
            $children = $this->getDoctrine()->getRepository('AppBundle:Mortgage')->findBy(['parent' => $mortgage]);
            foreach ($children as $child) {
                $clildMortgages[] = [
                    'mortgage' => $child,
                    'mortgageProps' => $this->getDoctrine()->getRepository('AppBundle:MortgageProp')->getMortgageProps($child),
                ];
            }

            $mortgageTerms = !empty($terms) ? $tools::getCreditTerms($terms['from'], $terms['to']) : '';

            $params = [
                'mortgage' => $mortgage,
                'childMortgages' => $clildMortgages,
                'bank' => $bank,
                'branches' => $branches,
                'atms' => $atms,
                'branchCount' => count($branches),
                'atmCount' => count($atms),
                'mortgageProps' => $mortgageProps,
                'mortgageTerms' => $mortgageTerms,
                'currencies' => $currencies,
                'bankCashRates' => $bankRates,
                'bankCashLastRates' => $bankLastRates,
                'from_listing' => $from_listing,

            ];

            return $this->render(':mortgage:page.html.twig', $params);
        }

        // Else redirect to mortgage list
        return $this->redirectToRoute('mortgage_page_with_city', ['altName' => $bankName, 'city' => $city->getAltName(),]);


    }

    /**
     * @Route("/ajax/getFilteredMortgage/", name="mortgage_ajax_get_filtered",
     *          options={"expose"=true},
     *          condition="request.isXmlHttpRequest()"
     * )
     * @Method({"POST"})
     */
    public function getFilteredActionMortgage(Request $request)
    {
        $this->setRoutePrefix($request, 'mortgage');

        $tools = $this->get('app.tools');
        $filter = $tools->getFilteredWhere($request->request->get('data'));
        if ($filter) {
            $filter = str_replace('prop.', 'cp.', $filter);
            $mortgages = $this->getDoctrine()->getRepository('AppBundle:Mortgage')->findFiltered($filter);
        } else {
            $mortgages = $this->getDoctrine()->getRepository('AppBundle:Mortgage')->findBy(['parent' => null]);
        }
        $output = '';
        if ($mortgages) {
            foreach ($mortgages as $key => $mortgage) {
                $output .= $this->renderView('mortgage/index_mortgage_block.html.twig', [
                    'mortgage' => $mortgage,
                    'mortgages' => $mortgages,
                    'key' => $key
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
     * @Route("/ajax/mortgage/percent/", name="mortgages_ajax_percent",
     *          options={"expose"=true},
     *          condition="request.isXmlHttpRequest()"
     * )
     * @Method({"POST"})
     */
    public function getPercentAction(Request $request)
    {
        $this->setRoutePrefix($request, 'mortgage');

        $tools = $this->get('app.tools');
        $all = $request->request->all();

        $output = '';
        $mortgages = $this->getDoctrine()->getRepository('AppBundle:Mortgage')->filterGetPercent($all['term']);
        if ($mortgages) {
            foreach ($mortgages as $mortgage) {
                $output .= $this->renderView('mortgage/index_mortgage_block.html.twig', ['mortgage' => $mortgage]);
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
     * @Route("/ajax/mortgage/fee/", name="mortgages_ajax_fee",
     *          options={"expose"=true},
     *          condition="request.isXmlHttpRequest()"
     * )
     * @Method({"POST"})
     */
    public function getFeeAction(Request $request)
    {
        $this->setRoutePrefix($request, 'mortgage');

        $tools = $this->get('app.tools');
        $all = $request->request->all();

        $output = '';

        $mortgages = $this->getDoctrine()->getRepository('AppBundle:Mortgage')->filterGetFee($all);
//
//        dump($mortgages);
//        die;

        if ($mortgages) {
            foreach ($mortgages as $mortgage) {
                $output .= $this->renderView('mortgage/index_mortgage_block.html.twig', [
                    'mortgage' => $mortgage,
                    'mortgages' => [$mortgage],
                    'key' => 0,
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
     * @Route("/ajax/mortgage/amount/", name="mortgages_ajax_amount",
     *          options={"expose"=true},
     *          condition="request.isXmlHttpRequest()"
     * )
     * @Method({"POST"})
     */
    public function getAmountAction(Request $request)
    {
        $this->setRoutePrefix($request, 'mortgage');


        $tools = $this->get('app.tools');
        $all = $request->request->all();
        $_SESSION['mortgage.amount'] = $all['amount'];


        $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByAltName($all['city']);

        $output = '';

        $credits = $this->getDoctrine()->getRepository('AppBundle:Mortgage')->findByCity($city);

        $options = [];
        $mortgage_opts = [];
        foreach($credits as $index => $mortgage){
            $creditProps = $this->getDoctrine()->getRepository('AppBundle:MortgageProp')->getMortgageProps($mortgage);
            foreach ($creditProps as $i => $pr){
                if($pr['altName'] == 'procentnaya-stavka'){
                    $options['rate'] = $pr['valueFrom'];
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
            foreach ($credits as $key => $mortgage) {
                $output .= $this->renderView('mortgage/index_mortgage_block.html.twig', [
                    'mortgage' => $mortgage,
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
