<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Admin\CompareController;
use AppBundle\Entity\Loan;
use AppBundle\Entity\City;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/zaimy")
 */
class LoanController extends RouteController
{

    /**
     * @Route("/compare/{act}/{id}", name="loan_compare",
     *          requirements={"id": "\d+"},
     *          options={"expose"=true},
     * )
     */
    public function compareAction($act, $id = null)
    {
        $props = $this->getDoctrine()->getRepository('AppBundle:CompareItem')->findBy(['type'=>CompareController::LOAN_COMPARE]);

        $compare = $this->get(CompareManager::class);
        $compare->init(Loan::class, 'loan', ':templates:loan_list.html.twig', $props);
        return $compare->$act($id);
    }

    /**
     * @Route("/", name="loan_index")
     * @Method("GET")
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $this->setRoutePrefix($request, 'loan');
        $loans = $this->getDoctrine()->getRepository('AppBundle:Loan')->findBy(['parent' => null]);

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Главная", "homepage");
        $breadcrumbs->addItem("МФО", "loan_index");

        $params = [
            'loans' => $loans,
        ];

        return $this->render(':loan:index.html.twig', $params);
    }

    /**
     * @Route("/{altName}/", name="loan_page")
     * @Method("GET")
     *
     * @param $altName
     * @param Request $request
     * @return Response
     */
    public function loanAction($altName, Request $request)
    {
        $this->setRoutePrefix($request, 'loan');
        $tools = $this->get('app.tools');

        // Check if altName is a City
        $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByAltName($altName);
        if ($city) {
            $cityManager = $this->get("app.city_manager");
            $cityManager->setCity($altName);

            $loans = $this->getDoctrine()->getRepository('AppBundle:Loan')->findBy(['parent' => null]);
//            $loans = $this->getDoctrine()->getRepository('AppBundle:Loan')->findByCity($city);

            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addItem("Займы", "loan_index");

            //найти ставку и посчитать переплату сразу
            $options = [];
            $options['rate'] = 0;
            $creditOpts = [];
            foreach($loans as $index => $credit){
                $creditProps = $this->getDoctrine()->getRepository('AppBundle:LoanProp')->getLoanProps($credit);
                foreach ($creditProps as $i => $pr){
                    if($pr['altName'] == 'procentnaya-stavka'){
                        $options['rate'] = $pr['valueFrom'];
                    }
                }
                $result = $tools::calculate_credit(1, 50000, $options['rate'], 1);
                if(count($result) > 0)
                $creditOpts[$index]['bloc_prop'] = [
                    'percentRate' => $options['rate'],
                    'ppm' => round($result['ppm'][0]),
                    'overpay' => round($result['procentAmount']),
                ];
            }

            $params = [
                'loans' => $loans,
                'options' => $creditOpts,
            ];

            return $this->render(':loan:index.html.twig', $params);
        }

        // Check if altName is a Fast Filter
        /* @var $deposit Loan */
        $fastFilter = $this->getDoctrine()->getRepository('AppBundle:FastFilter')->findOneBy([
                'product' => $this->getDoctrine()->getRepository('AppBundle:Product')->findOneByName('LOAN'),
                'altName' => $altName]
        );
        if ($fastFilter) {
            $tools = $this->get('app.tools');
            $filter = $tools->getFastFilterWhere($fastFilter);
            $filter = str_replace('prop.', 'cp.', $filter);
            $loans = $this->getDoctrine()->getRepository('AppBundle:Loan')->findFiltered($filter);

            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addItem("Займы", "deposit_index");

            $params = [
                'loans' => $loans,
                'filter' => $tools->getFastFilterPropsArray($fastFilter),
            ];

            return $this->render(':loan:index.html.twig', $params);
        }

        // Check if altName is a Loan
        $loan = $this->getDoctrine()->getRepository('AppBundle:Loan')->findOneByAltName($altName);
        if ($loan) {
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addRouteItem("МФО", "loan_index");
            $breadcrumbs->addItem($loan->getName());

            $loanProps = $this->getDoctrine()->getRepository('AppBundle:LoanProp')->getLoanProps($loan);

            $terms = [];
            $credit_security = [];
            $credit_purpose = [];
            foreach ($loanProps as $creditProp) {
                if($creditProp['altName'] == 'period'){
                    $terms['from'] = $creditProp['valueFrom'];
                    $terms['to'] = $creditProp['valueTo'];
                }
                if($creditProp['name'] == 'Обеспечение'){
                    $credit_security = explode('<br>', $creditProp['optionValue']);
                }
                if($creditProp['name'] == 'Цель кредита'){
                    $credit_purpose = explode('<br>', $creditProp['optionValue']);
                }
            }

            $creditTerms = !empty($terms) ? $tools::getCreditTerms($terms['from'], $terms['to']) : '';

            $type = 1;
            foreach ($loanProps as $item){
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
                'loan' => $loan,
                'loanProps' => $loanProps,
                'payment_type' => $type,
                'creditTerms' => $creditTerms,
                'credit_security' => $credit_security,
                'credit_purpose' => $credit_purpose,

            ];

            return $this->render(':loan:page.html.twig', $params);
        }

        // Else redirect to loan list
        return $this->redirectToRoute('loan_index');

    }

    /**
     * @Route("/{bankName}/{altName}/", name="loan_page_single")
     * @Method("GET")
     *
     * @param $altName
     * @param Request $request
     * @return Response
     */
    public function loanPageAction($bankName, $altName, Request $request)
    {
        $this->setRoutePrefix($request, 'loan');
        $tools = $this->get('app.tools');
        $cityManager = $this->get("app.city_manager");
        $city = $cityManager->getCityEntity();

        // Check if altName is a Loan
        $loan = $this->getDoctrine()->getRepository('AppBundle:Loan')->findOneByAltName($altName);
        if ($loan) {
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addRouteItem("МФО", "loan_index");
            $breadcrumbs->addItem($loan->getName());

            $loanProps = $this->getDoctrine()->getRepository('AppBundle:LoanProp')->getLoanProps($loan);

            $terms = [];
            $credit_security = [];
            $credit_purpose = [];
            foreach ($loanProps as $creditProp) {
                if($creditProp['altName'] == 'period'){
                    $terms['from'] = $creditProp['valueFrom'];
                    $terms['to'] = $creditProp['valueTo'];
                }
                if($creditProp['name'] == 'Обеспечение'){
                    $credit_security = explode('<br>', $creditProp['optionValue']);
                }
                if($creditProp['name'] == 'Цель кредита'){
                    $credit_purpose = explode('<br>', $creditProp['optionValue']);
                }
            }

            $creditTerms = !empty($terms) ? $tools::getCreditTerms($terms['from'], $terms['to']) : '';

            $type = 1;
            foreach ($loanProps as $item){
                if(strpos($item['optionValue'], 'аннуитетная' ) !== false){
                    $type = 1;
                    break;
                }
                elseif (strpos($item['optionValue'], 'дифференци') !== false){
                    $type = 2;
                    break;
                }
            }

            $from_listing = $tools->getSessionFilterData('loan');

            $params = [
                'loan' => $loan,
                'from_listing' => $from_listing,
                'loanProps' => $loanProps,
                'payment_type' => $type,
                'creditTerms' => $creditTerms,
                'credit_security' => $credit_security,
                'credit_purpose' => $credit_purpose,
            ];

            return $this->render(':loan:page.html.twig', $params);
        }

        // Else redirect to loan list
        return $this->redirectToRoute('loan_index');

    }

    /**
     * @Route("/ajax/getFilteredMortgage/", name="loan_ajax_get_filtered",
     *          options={"expose"=true},
     *          condition="request.isXmlHttpRequest()"
     * )
     * @Method({"POST"})
     */
    public function getFilteredActionLoan(Request $request)
    {
        $this->setRoutePrefix($request, 'loan');

        $tools = $this->get('app.tools');
        $filter = $tools->getFilteredWhere($request->request->get('data'));
        if ($filter) {
            $filter = str_replace('prop.', 'cp.', $filter);
            $loans = $this->getDoctrine()->getRepository('AppBundle:Loan')->findFiltered($filter);
        } else {
            $loans = $this->getDoctrine()->getRepository('AppBundle:Loan')->findBy(['parent' => null]);
        }
        $output = '';
        if ($loans) {
            foreach ($loans as $loan) {
                $output .= $this->renderView('loan/index_loan_block.html.twig', ['loan' => $loan, 'loans' => $loans]);
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
     * @Route("/ajax/loan/calculate/", name="loan_ajax_calc",
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
        $all['term'] = 12;
        foreach ($request->request->get('data') as $item){
            if($item['name'] == 'amount'){
                $all['amount'] = $item['value'];
            }
            elseif ($item['name'] == 'percent'){
                $all['percent'] = $item['value'];
            }
            elseif ($item['name'] == 'term'){
                $all['term'] = $item['value'];
            }
            elseif ($item['name'] == 'scheme'){
                $all['type'] = $item['value'];
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
     * @Route("/ajax/loan/percent/", name="loans_ajax_percent",
     *          options={"expose"=true},
     *          condition="request.isXmlHttpRequest()"
     * )
     * @Method({"POST"})
     */
    public function getPercentAction(Request $request)
    {
        $this->setRoutePrefix($request, 'loan');

        $tools = $this->get('app.tools');
        $all = $request->request->all();

        $output = '';
        $loans = $this->getDoctrine()->getRepository('AppBundle:Loan')->filterGetPercent($all['term']);
        if ($loans) {
            foreach ($loans as $loan) {
                $output .= $this->renderView('loan/index_loan_block.html.twig', ['loan' => $loan]);
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
     * @Route("/ajax/loans/amount/", name="loans_ajax_amount",
     *          options={"expose"=true},
     *          condition="request.isXmlHttpRequest()"
     * )
     * @Method({"POST"})
     */
    public function getAmountAction(Request $request)
    {
        $this->setRoutePrefix($request, 'loan');

        $tools = $this->get('app.tools');
        $all = $request->request->all();
        $_SESSION['loan.amount'] = $all['amount'];


//        $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByAltName($all['city']);

        $output = '';

        $loans = $this->getDoctrine()->getRepository('AppBundle:Loan')->findBy([]);

        $options = [];
        $loans_opts = [];
        foreach($loans as $index => $loan){
            $creditProps = $this->getDoctrine()->getRepository('AppBundle:LoanProp')->getLoanProps($loan);
            foreach ($creditProps as $i => $pr){
                if($pr['altName'] == 'procentnaya-stavka'){
                    $options['rate'] = $pr['valueFrom'];
                }
            }
            $result = $tools::calculate_credit($all['term'], $all['amount'], $options['rate'], 1);
            $loans_opts[$index]['bloc_prop'] = [
                'percentRate' => $options['rate'],
                'ppm' => round($result['ppm'][0]),
                'overpay' => round($result['procentAmount']),
            ];
        }
        if ($loans) {
            foreach ($loans as $key => $loan) {
                $output .= $this->renderView('auto_credit/index_credit_block.html.twig', [
                    'loan' => $loan,
                    'key' => $key,
                    'options' => $loans_opts,
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
