<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Admin\CompareController;
use AppBundle\Entity\Bank;
use AppBundle\Entity\City;
use AppBundle\Entity\CreditCard;
use AppBundle\Service\CityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/kreditnye-karty")
 */
class CardController extends RouteController
{
    /**
     * @Route("/", name="card_index")
     * @Method("GET")
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        if ($this->searchFilter) {
            $filter = str_replace('prop.', 'cp.', $this->searchFilter);
            $credit_cards = $this->getDoctrine()->getRepository('AppBundle:CreditCard')->findFiltered($filter);
        } else {
//            $credit_cards = $this->getDoctrine()->getRepository('AppBundle:CreditCard')->findBy(['parent' => null]);
            $query = $this->getDoctrine()->getRepository('AppBundle:CreditCard')->createQueryBuilder('p')
                ->join('p.creditCardProps', 'card_prop', 'p.id = card_prop.creditCard_id')
                ->where('card_prop.propOption = 239')
                ->getQuery();
            ;
            $credit_cards = $query->getResult();
        }

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Главная", "homepage");
        $breadcrumbs->addItem("Карты", "credit_index");

        $params = [
            'credit_cards' => $credit_cards,
        ];

        return $this->render(':card/credit:index.html.twig', $params);
    }


    /**
     * @Route("/{altName}/", name="creditcard_page")
     * @Route("/{bankName}/{altName}/", name="creditcard_page_with_bank", defaults={"bankName" = "null"})
     * @Method("GET")
     *
     * @param $altName
     * @param $bankName
     * @param Request $request
     *
     * @return Response
     */
    public function cardAction($altName, $bankName = null, Request $request)
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
                ->where('card_prop.propOption = 239')
                ->getQuery();
            ;
            $credit_cards = $query->getResult();
            $credit_cards_props = [];
            foreach ($credit_cards as $card) {
                $values = $this->getDoctrine()->getRepository('AppBundle:CreditCard')->getCreditCardProps($card);
                $prop_array = [];
                foreach ($values as $value) {
                    $key = $value['altName'];
                    $prop_array[$key] = $value;
                }

                $credit_cards_props[] = $prop_array;
            }

            $params = [
                'city' => $city,
                'credit_cards' => $credit_cards,
                'credit_cards_props' => $credit_cards_props,
            ];

            return $this->render(':card/credit:index.html.twig', $params);
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
            $cityManager = $this->get('app.city_manager');
            $city = $cityManager->getCityEntity();

            $branches = $this->getDoctrine()->getRepository('AppBundle:Bank')->findBy(['parent' => $bank, 'city' => $city]);
            $atms = $this->getDoctrine()->getRepository('AppBundle:Atm')->findByBank($bank);
            $currencies = $this->getDoctrine()->getRepository('AppBundle:Currency')->getExchangeCurrencies();

            $terms = [];
            $prop_array = [];
            foreach ($creditCardProps as $creditProp) {
                if($creditProp['altName'] == 'period'){
                    $terms['from'] = $creditProp['valueFrom'];
                    $terms['to'] = $creditProp['valueTo'];
                }
                $key = $creditProp['altName'];
                $prop_array[$key] = $creditProp;
            }

            $creditTerms = !empty($terms) ? $tools::getCreditTerms($terms['from'], $terms['to']) : '';

            $params = [
                'card' => $card,
                'cardProps' => $creditCardProps,
                'branches' => $branches,
                'atms' => $atms,
                'bank' => $bank,
                'branchCount' => count($branches),
                'atmCount' => count($atms),
                'creditTerms' => $creditTerms,
                'card_props_custom' => $prop_array,
                'currencies' => $currencies,
            ];

            return $this->render(':card/credit:page.html.twig', $params);
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

    /**
     * @Route("/compare/{act}/{id}/", name="credit_card_compare",
     *          requirements={"id": "\d+"},
     *          options={"expose"=true},
     * )
     */
    public function compareAction($act, $id = null)
    {
        $props = $this->getDoctrine()->getRepository('AppBundle:CompareItem')->findBy(['type'=>CompareController::CREDIT_CART_COMPARE]);
        $compare = $this->get(CompareManager::class);
        $compare->init(CreditCard::class, 'kreditnye-karty', ':templates:comparison_list.html.twig', $props);
        return $compare->$act($id);
    }


}
