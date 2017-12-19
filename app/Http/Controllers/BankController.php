<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Admin\CompareController;
use AppBundle\Entity\Bank;
use AppBundle\Entity\City;
use AppBundle\Entity\ExchangeRate;
use AppBundle\Entity\Review;
use AppBundle\Form\Admin\ReviewAnswerType;
use AppBundle\Form\ReviewType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/banki")
 */

class BankController extends RouteController
{
    /**
     * @Route("/compare/{act}/{id}/", name="bank_compare",
     *          requirements={"id": "\d+"},
     *          options={"expose"=true},
     * )
     */
    public function compareAction($act, $id = null)
    {
        $props = $this->getDoctrine()->getRepository('AppBundle:CompareItem')->findBy(['type'=>CompareController::BANK_COMPARE]);

        $compare = $this->get(CompareManager::class);
        $compare->init(Bank::class, 'banki', ':templates:banki_list.html.twig', $props);
        return $compare->$act($id);
    }


    /**
     * @Route("/{bankAlt}/otdeleniya/",
     *     name="branches_page",
     *     defaults={"type": "otdeleniya", "cityAlt":"almaty"},
     * )
     * @Route("/{bankAlt}/bankomaty/",
     *     name="atm_page",
     *     defaults={"type": "bankomaty", "cityAlt":"almaty"},
     * )
     * @Route("/{bankAlt}/otdeleniya/{cityAlt}/",
     *     name="branches_city_page",
     *     defaults={
     *          "type": "otdeleniya",
     *      },
     * )
     * @Route("/{bankAlt}/bankomaty/{cityAlt}/",
     *     name="atm_city_page",
     *     defaults={
     *          "type": "bankomaty"
     *      },
     * )
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function branchesAction($bankAlt = null,$cityAlt, $type, Request $request)
    {
        $this->setRoutePrefix($request, 'bank');
        $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByAltName($cityAlt);
        $bank = $this->getDoctrine()->getRepository('AppBundle:Bank')->findOneBy(['altName' => $bankAlt, 'city'=>$city]);

        if (!$bank) {
            return $this->redirectToRoute('bank_index');
        }

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Главная", "homepage");
        $breadcrumbs->addRouteItem("Банки", "bank_index");
        $breadcrumbs->addRouteItem($bank->getName(), "bank_page", ["altName" => $bank->getAltName()]);


        if($type == 'otdeleniya'){
            $breadcrumbs->addItem("Отделения");
            $branches = $this->getDoctrine()->getRepository('AppBundle:Bank')->findByParent($bank);
        }else{
            $branches = $this->getDoctrine()->getRepository('AppBundle:Atm')->findByBank($bank);
        }


        $params = [
            'bank' => $bank,
            'branches' => $branches,
            'type'=>$type,
            'city'=>$city
        ];

        return $this->render(':bank:branches.html.twig', $params);

    }


    /**
     * @Route("/", name="bank_index")
     * @Method("GET")
     *
     * @return Response
     */
    public function indexAction()
    {
        $banks = $this->getDoctrine()->getRepository('AppBundle:Bank')->getBanks('ratingClients', 'DESC', 0, 30);

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Главная", "homepage");
        $breadcrumbs->addItem("Банки", "bank_index");

        $params = [
            'banks' => $banks,
        ];

        return $this->render(':bank:index.html.twig', $params);
    }

    /**
     * @Route("/{altName}/{city}/", name="bank_page", defaults={"city" = "null"})
     * @Route("/{altName}/", name="bank_page_without_city")
     * @Method("GET")
     *
     * @param $altName
     * @param $city
     * @param Request $request
     * @return Response
     */
    public function bankAction($altName, $city = null, Request $request)
    {
        $this->setRoutePrefix($request, 'bank');

        $cityManager = $this->get("app.city_manager");

        // Check if altName is a City
        $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByAltName($altName);
        if ($city) {
            $banks = $this->getDoctrine()->getRepository('AppBundle:Bank')->getBanks('ratingClients', 'DESC', 0, 30, null, $city);

            $cityManager->setCity($altName);

            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addItem("Банки в " . $city->getName());

            $params = [
                'city' => $city,
                'banks' => $banks,
            ];

            return $this->render(':bank:index.html.twig', $params);
        }

        $city = $cityManager->getCityEntity();

        // Check if altName is a Bank
        /* @var $bank Bank */
        $bank = $this->getDoctrine()->getRepository('AppBundle:Bank')->findOneByAltName($altName);
        if ($bank) {
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addRouteItem("Банки", "bank_index");
            $breadcrumbs->addItem($bank->getName());

            $rates = $this->get('app.tools')->getBankRates($bank, $city);

            $currencies = $this->getDoctrine()->getRepository('AppBundle:Currency')->getExchangeCurrencies();
            $branches = $this->getDoctrine()->getRepository('AppBundle:Bank')->findBy(['parent' => $bank, 'city' => $city,]);
            $atms = $this->getDoctrine()->getRepository('AppBundle:Atm')->findByBank($bank);
            $reviews = $this->getDoctrine()->getRepository('AppBundle:Review')->findByBank($bank);

            $params = [
                'bank' => $bank,
                'branches' => $branches,
                'reviewsCount' => count($reviews),
                'reviews' => $reviews,
                'atms' => $atms,
                'branchCount' => count($branches),
                'atmCount' => count($atms),
                'currencies' => $currencies,
            ];

            $params = array_merge($rates, $params);

            return $this->render(':bank:page.html.twig', $params);
        }
        // Else redirect to bank list
        return $this->redirectToRoute('bank_index');
    }


    /**
     * @Route("/{bank}/kursy/{city}/", name="rates_page", options={"expose"=true})
     * @Method("GET")
     *
     * @param Bank $bank
     * @param City $city
     * @param Request $request
     * @return Response
     */
    public function ratesAction($bank, $city, Request $request)
    {
        $this->setRoutePrefix($request, 'bank');

        if (!$bank) {
            return $this->redirectToRoute('bank_index');
        }

        $cityManager = $this->get("app.city_manager");
//        $city = $cityManager->getCityEntity();
        $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByAltName($city);
        $bank = $this->getDoctrine()->getRepository('AppBundle:Bank')->findOneByAltName($bank);

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Главная", "homepage");
        $breadcrumbs->addRouteItem("Банки", "bank_index");
        $breadcrumbs->addRouteItem($bank->getName(), "bank_page", ["altName" => $bank->getAltName()]);
        $breadcrumbs->addItem("Курсы");

        $selectDate = $request->query->get('date');
        if (!empty($selectDate)) {
            $selectDate = date('Y-m-d', strtotime($selectDate));
        }

        $bankLastRates = [];
        /* @var $bankRates ExchangeRate  */
        $bankRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->getBankRates($bank, $city, ExchangeRate::TYPE_NO_CASH);
        $bankRatesCash = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->getBankRates($bank, $city, ExchangeRate::TYPE_CASH);

        if(!empty($bankRatesCash)){
            $bankRatesCash = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->transformToArray($bankRatesCash);
        }

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

        $ratesInOthersBanks = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->getRatesInOtherBanks($city, $bank, null, null, 1);

        $currencies = $this->getDoctrine()->getRepository('AppBundle:Currency')->getExchangeCurrencies();

        $exchangersRates = $this
            ->getDoctrine()
            ->getRepository('AppBundle:ExchangeRate')
            ->findBy(['type' => ExchangeRate::TYPE_NO_CASH], null, 5, 0);

        $params = [
            'bank' => $bank,
            'currencies' => $currencies,
            'bankRates' => $bankRates,
            'bankLastRates' => $bankLastRates,
            'city' => $city,
            'ratesInOthersBanks' => $ratesInOthersBanks,
            'bankRatesCash' => $bankRatesCash,
            'exchangersRates' => $exchangersRates
        ];
        return $this->render(':bank:rates.html.twig', $params);
    }

    /**
     * @Route("/{altName}/otzivy/", name="bank_reviews_page")
     * @ParamConverter("bank", class="AppBundle:Bank")
     * @Method({"GET", "POST"})
     *
     * @param Bank $bank
     * @param Request $request
     *
     * @return Response
     */
    public function reviewsAction(Bank $bank = null, Request $request)
    {
        $this->setRoutePrefix($request, 'bank');

        if (!$bank) {
            return $this->redirectToRoute('bank_index');
        }

        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);

        if ($this->getRequest()->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $review->setBank($bank);

                $em = $this->getDoctrine()->getManager();
                $em->persist($review);
                $em->flush();

                // TODO Це якесь лайно, але чому на автоматі на додається категорія я не розумію :(
                // TODO Это какое-то дерьмо, но почему на автомате на добавляется категория я не понимаю :(
                // TODO That's some bullshhit, but why isnt category automatically adding i dont understand :(

                $formData = $request->request->all();
                $em->refresh($review);
                $category = $em->getRepository('AppBundle:Category')->find($formData['app_review_front']['category']);
                $review->setCategory($category);
                $review->setCreatedAt(new \DateTime());
                $em->flush();

                $settings = $this->getDoctrine()->getRepository('AppBundle:Settings')->findOneBy([]);

                $message = \Swift_Message::newInstance();
                $body = "Для просмотра отзыва перейдите по <a href='".$request->getHost().$this->generateUrl('admin_review_edit', ['id' => $review->getId()])."'>ссылке<a>";

                $message->setSubject('На сайте оставлен новый отзыв')
                    ->setFrom($review->getEmail())
                    ->setTo($settings->getAdminEmail())
                    ->setBody(
                        $body,
                        'text/html'
                    );

                $this->get('mailer')->send($message);

                header("location: ".$request->getUri());
                exit();
            }

            $formData = $request->request->all();

            if (isset($formData['app_review_answer'])) {

                $em = $this->getDoctrine()->getManager();

                $parenReview = $this->getDoctrine()->getRepository('AppBundle:Review')->find($formData['app_review_answer']['parent']);
                $reviewAnswer = new Review();
                $reviewAnswer->setAuthor($formData['app_review_answer']['author']);
                $reviewAnswer->setEmail($formData['app_review_answer']['email']);
                $reviewAnswer->setDescription($formData['app_review_answer']['description']);
                $reviewAnswer->setBank($bank);
                $reviewAnswer->setIsAdmin(isset($formData['app_review_answer']['admin']));
                $reviewAnswer->setParent($parenReview);
                $reviewAnswer->setCreatedAt(new \DateTime());
                $em->persist($reviewAnswer);
                $em->flush();

                $settings = $this->getDoctrine()->getRepository('AppBundle:Settings')->findOneBy([]);

                $message = \Swift_Message::newInstance();
                $body = "Для просмотра отзыва перейдите по <a href='".$request->getHost().$this->generateUrl('admin_review_edit', ['id' => $reviewAnswer->getId()])."'>ссылке<a>";

                $message->setSubject('На сайте оставлен новый отзыв')
                    ->setFrom($reviewAnswer->getEmail())
                    ->setTo($settings->getAdminEmail())
                    ->setBody(
                        $body,
                        'text/html'
                    );

                $this->get('mailer')->send($message);

                header("location: ".$request->getUri());
                exit();
            }
        }

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Главная", "homepage");
        $breadcrumbs->addRouteItem("Банки", "bank_index");
        $breadcrumbs->addRouteItem($bank->getName(), "bank_page", ["altName" => $bank->getAltName()]);
        $breadcrumbs->addItem("Отзывы");

        $branches = $this->getDoctrine()->getRepository('AppBundle:Bank')->findByParent($bank);
        $atms = $this->getDoctrine()->getRepository('AppBundle:Atm')->findByBank($bank);

        $city = $this->get('app.city_manager')->getCityEntity();
        $rates = $this->get('app.tools')->getBankRates($bank, $city);
        $currencies = $this->getDoctrine()->getRepository('AppBundle:Currency')->getExchangeCurrencies();
        $categories = $this->getDoctrine()->getRepository('AppBundle:Category')->findAll();
        $reviews = $this->getDoctrine()->getRepository('AppBundle:Review')->findBy(['bank' => $bank]);

        $params = [
            'bank' => $bank,
            'branches' => $branches,
            'atms' => $atms,
            'currencies' => $currencies,
            'categories' => $categories,
            'city' => $city,
            'reviews' => $reviews,
            'form' => $form->createView(),
        ];

        $params = array_merge($rates, $params);

        return $this->render(':bank:reviews.html.twig', $params);

    }

    /**
     * @Route("/ajax/getBranches/", name="bank_ajax_get_branches",
     *          options={"expose"=true},
     *          condition="request.isXmlHttpRequest()"
     * )
     * @Method({"POST"})
     */
    public function getBranchesAction(Request $request)
    {
        $altName = $request->request->get('altName');
        $rateType = $request->request->get('rateType');
        $bank = $this->getDoctrine()->getRepository('AppBundle:Bank')->findOneBy(['altName' => $altName]);
        $currentCity = $this->get('app.city_manager')->getCityEntity();

        if ('cash' == $rateType) {
            $branches = $this->getDoctrine()->getRepository('AppBundle:Bank')->findBy(['parent' => $bank, 'city' => $currentCity]);
        } else {
            $branches = $this->getDoctrine()->getRepository('AppBundle:Bank')->findBy(['parent' => $bank, 'city' => !null]);
        }

        $output = $this->renderView(':bank:inner_block_bank_branches.html.twig', ['branches' => $branches, 'mapId' => $bank->getId()]);

        $jsonResponseHelper = $this->get('app.json_response_helper');
        $response = $jsonResponseHelper->prepareJsonResponse();

        $response->setData($output);

        return $response;
    }

//    /**
//     * @Route("/search", name="bank_search")
//     * @Method("GET")
//     *
//     * @param Request $request
//     * @return Response
//     */
//    public function findBankAction(Request $request)
//    {
//        $this->setRoutePrefix($request, 'bank');
//
//        $banks = $this->getDoctrine()->getRepository('AppBundle:Bank')->findByAnyWord($request->request->get('data'));
//
//        $breadcrumbs = $this->get("white_october_breadcrumbs");
//        $breadcrumbs->addRouteItem("Главная", "homepage");
//        $breadcrumbs->addItem("Банки");
//
//        $params = [
//            'banks' => $banks,
//        ];
//
//        return $this->render(':bank:index.html.twig', $params);
//    }

    /**
     * @Route("/ajax/search/", name="bank_search",
     *          options={"expose"=true},
     *          condition="request.isXmlHttpRequest()"
     * )
     * @Method("POST")
     */
    public function getFilteredAction(Request $request)
    {
        $this->setRoutePrefix($request, 'bank');
        $cityManager = $this->get("app.city_manager");
        $city = $cityManager->getCityEntity();

        $filter = $request->request->get('data');
        $filter = $filter[0]['value'];
        if ($filter != null) {
//            $banks = $this->getDoctrine()->getRepository('AppBundle:Bank')->findByAnyWord($filter);
            $banks = $this->getDoctrine()->getRepository('AppBundle:Bank')->findByAnyWord($filter);

        }
        else{
//            $banks = $this->getDoctrine()->getRepository('AppBundle:Bank')->findBy(['parent' => null]);
//            $banks = $this->getDoctrine()->getRepository('AppBundle:Bank')->getBanks('ratingClients', 'DESC', 0, 30, null);

        }
        $output = '';
        if ($banks != null) {
            foreach ($banks as $bank){
                $rates = $this->get('app.tools')->getBankRates($bank, $city);

                $currencies = $this->getDoctrine()->getRepository('AppBundle:Currency')->getExchangeCurrencies();
                $branches = $this->getDoctrine()->getRepository('AppBundle:Bank')->findByParent($bank);
                $atms = $this->getDoctrine()->getRepository('AppBundle:Atm')->findByBank($bank);
                $reviews = $this->getDoctrine()->getRepository('AppBundle:Review')->findByBank($bank);

                $params = [
                    'bank' => $bank,
                    'branches' => $branches,
                    'reviewsCount' => count($reviews),
                    'atms' => $atms,
                    'branchCount' => count($branches),
                    'atmCount' => count($atms),
                    'currencies' => $currencies,
                ];

                $params = array_merge($rates, $params);

                foreach ($banks as $bank) {
                    $output .= $this->renderView('bank/index_bank_block_search.html.twig', ['bank' => $params]);
                }
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
