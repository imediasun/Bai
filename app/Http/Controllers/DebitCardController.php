<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bank;
use AppBundle\Entity\City;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/debetovye-karty")
 */
class DebitCardController extends RouteController
{
    /**
     * @Route("/", name="debit_card_index")
     * @Method("GET")
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        if ($this->searchFilter) {
            $filter = str_replace('prop.', 'cp.', $this->searchFilter);
            $debet_cards = $this->getDoctrine()->getRepository('AppBundle:CreditCard')->findFiltered_Debet($filter);

        } else {
            $query = $this->getDoctrine()->getRepository('AppBundle:CreditCard')->createQueryBuilder('p')
                ->join('p.creditCardProps', 'card_prop', 'p.id = card_prop.creditCard_id')
                ->where('card_prop.propOption = 238')
                ->getQuery();
            ;
            $debet_cards = $query->getResult();
        }

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Главная", "homepage");
        $breadcrumbs->addItem("Карты", "card_debet_index");

        $params = [
            'debet_cards' => $debet_cards,
        ];

        return $this->render(':card/debet:index.html.twig', $params);
    }


    /**
     * @Route("/{altName}/", name="debitcard_page")
     * @Method("GET")
     *
     * @param $altName
     * @param Request $request
     *
     * @return Response
     */
    public function cardAction($altName, Request $request)
    {
        $this->setRoutePrefix($request, 'creditcard');
        $tools = $this->get('app.tools');

        // Check if altName is a City
        $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByAltName($altName);
        if ($city) {
//            $banks = $this->getDoctrine()->getRepository('AppBundle:Bank')->getBanks('ratingClients', 'DESC', 0, 30, null, $city);
//
//            $cityManager = $this->get("app.city_manager");
//            $cityManager->setCity($altName);
//
//            $breadcrumbs = $this->get("white_october_breadcrumbs");
//            $breadcrumbs->addRouteItem("Главная", "homepage");
//            $breadcrumbs->addItem("Банки в " . $city->getName());


            /**/
            $query = $this->getDoctrine()->getRepository('AppBundle:CreditCard')->createQueryBuilder('p')
                ->join('p.creditCardProps', 'card_prop', 'p.id = card_prop.creditCard_id')
                ->join('card_prop.propOption', 'prop_option', 'prop_option.id = card_prop.prop_option_id')
                ->where('card_prop.propOption = 239')
                ->getQuery();
            ;
            $credit_cards = $query->getResult();
            /**/

            $params = [
                'city' => $city,
                'credit_cards' => $credit_cards,
            ];

            return $this->render(':card/debet:index.html.twig', $params);
        }

        // Check if altName is a Card
        $card = $this->getDoctrine()->getRepository('AppBundle:CreditCard')->findOneByAltName($altName);
        if ($card) {
            $creditCardProps = $this->getDoctrine()->getRepository('AppBundle:CreditCard')->getCreditCardProps($card);
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addRouteItem("Кредитные карты", "bank_index");
            $breadcrumbs->addItem($card->getName());

            $bank = $this->getDoctrine()->getRepository('AppBundle:Bank')->find($card->getBank());
            $branches = $this->getDoctrine()->getRepository('AppBundle:Bank')->findByParent($bank);
            $atms = $this->getDoctrine()->getRepository('AppBundle:Atm')->findByBank($bank);

            $terms = [];
            foreach ($creditCardProps as $creditProp) {
                if($creditProp['altName'] == 'period'){
                    $terms['from'] = $creditProp['valueFrom'];
                    $terms['to'] = $creditProp['valueTo'];
                }
            }

            $creditTerms = !empty($terms) ? $tools::getCreditTerms($terms['from'], $terms['to']) : '';

            $params = [
                'card' => $card,
                'cardProps' => $creditCardProps,
                'branches' => $branches,
                'atms' => $atms,
                'branchCount' => count($branches),
                'atmCount' => count($atms),
                'creditTerms' => $creditTerms,
            ];

            return $this->render(':card/debet:page.html.twig', $params);
        }

        // Else redirect to bank list
        return $this->redirectToRoute('card_index');

    }

    /**
     * @Route("/ajax/getFilteredCreditCard/", name="credit_card_ajax_get_filtered",
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
            $credit_cards = $this->getDoctrine()->getRepository('AppBundle:CreditCard')->findFiltered($filter);
        } else {
            $credit_cards = $this->getDoctrine()->getRepository('AppBundle:CreditCard')->findBy(['parent' => null]);
        }
        $output = '';
        if ($credit_cards) {
            foreach ($credit_cards as $credit_card) {
                $output .= $this->renderView('card/credit/index_credit_card_block.html.twig', ['credit_card' => $credit_card]);
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
     * @Route("/ajax/card/percent/", name="credit_card_ajax_percent",
     *          options={"expose"=true},
     *          condition="request.isXmlHttpRequest()"
     * )
     * @Method({"POST"})
     */
    public function getPercentAction(Request $request)
    {
        $this->setRoutePrefix($request, 'credit_card');

        $all = $request->request->all();
//        $card = $this->getDoctrine()->getRepository('AppBundle:Card')->findOneBy([
//            'id' => $all['product_id'],
//        ]);
//
//        $depositProps = $this->getDoctrine()->getRepository('AppBundle:CardProp')->getDepositPercent([
//            'id' => $all['product_id'],
//            'currency' => $all['currency'],
//        ]);
//
//        $percent = null;
//
//        $output =
//            [
//                'amount' => $depositProps->valueUnit,
//
//            ];
//
//        $jsonResponseHelper = $this->get('app.json_response_helper');
//        $response = $jsonResponseHelper->prepareJsonResponse();
//
//        $response->setData($output);
//
//        return $response;
    }

}
