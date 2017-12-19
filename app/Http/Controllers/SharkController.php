<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Shark;
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
 * @Route("/mfo")
 */
class SharkController extends RouteController
{
    /**
     * @Route("/", name="shark_index")
     * @Method("GET")
     *
     * @return Response
     */
    public function indexAction()
    {
        $sharks = $this->getDoctrine()->getRepository('AppBundle:Shark')->findAll();

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Главная", "homepage");
        $breadcrumbs->addItem("МФО", "shark_index");

        $params = [
            'sharks' => $sharks,
        ];

        return $this->render(':shark:index.html.twig', $params);
    }

    /**
     * @Route("/{altName}/", name="shark_page")
     * @Method("GET")
     *
     * @param $altName
     * @param Request $request
     * @return Response
     */
    public function sharkAction($altName, Request $request)
    {
        $this->setRoutePrefix($request, 'shark');

        $cityManager = $this->get("app.city_manager");
        $city = $cityManager->getCityEntity();

        // Check if altName is a Shark
        /* @var $shark Shark */
        $shark = $this->getDoctrine()->getRepository('AppBundle:Shark')->findOneByAltName($altName);
        if ($shark) {
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addRouteItem("Главная", "homepage");
            $breadcrumbs->addRouteItem("МФО", "shark_index");
            $breadcrumbs->addItem($shark->getName());

//            $rates = $this->get('app.tools')->getSharkRates($shark, $city);

            $currencies = $this->getDoctrine()->getRepository('AppBundle:Currency')->getExchangeCurrencies();
            $reviews = $this->getDoctrine()->getRepository('AppBundle:Review')->findByShark($shark);

            $params = [
                'shark' => $shark,
                'reviewsCount' => count($reviews),
                'currencies' => $currencies,
            ];

//            $params = array_merge($rates, $params);

            return $this->render(':shark:page.html.twig', $params);
        }

        // Else redirect to shark list
        return $this->redirectToRoute('shark_index');

    }

    /**
     * @Route("/{altName}/otdeleniya/", name="shark_branches_page")
     * @ParamConverter("shark", class="AppBundle:Shark")
     * @Method("GET")
     *
     * @param Shark $shark
     * @param Request $request
     *
     * @return Response
     */
    public function branchesAction(Shark $shark = null, Request $request)
    {
        $this->setRoutePrefix($request, 'shark');

        if (!$shark) {
            return $this->redirectToRoute('shark_index');
        }

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Главная", "homepage");
        $breadcrumbs->addRouteItem("Банки", "shark_index");
        $breadcrumbs->addRouteItem($shark->getName(), "shark_page", ["altName" => $shark->getAltName()]);
        $breadcrumbs->addItem("Отделения");

        $branches = $this->getDoctrine()->getRepository('AppBundle:Shark')->findByParent($shark);
        $atms = $this->getDoctrine()->getRepository('AppBundle:Atm')->findByShark($shark);

        $params = [
            'shark' => $shark,
            'branches' => $branches,
            'atms' => $atms,
        ];

        return $this->render(':shark:branches.html.twig', $params);

    }

    /**
     * @Route("/{altName}/sharkomaty/", name="shark_atms_page")
     * @ParamConverter("shark", class="AppBundle:Shark")
     * @Method("GET")
     *
     * @param Shark $shark
     * @param Request $request
     * @return Response
     */
    public function atmAction(Shark $shark = null, Request $request)
    {
        $this->setRoutePrefix($request, 'shark');

        if (!$shark) {
            return $this->redirectToRoute('shark_index');
        }

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Главная", "homepage");
        $breadcrumbs->addRouteItem("Банки", "shark_index");
        $breadcrumbs->addRouteItem($shark->getName(), "shark_page", ["altName" => $shark->getAltName()]);
        $breadcrumbs->addItem("Банкоматы");

        $atms = $shark->getAtm();
        if (count($atms) > 0) {
            $atm = $atms[0];
        } else {
            $atm = null;
        }

        $params = [
            'shark' => $shark,
            'atm' => $atm,
        ];

        return $this->render(':shark:atms.html.twig', $params);

    }

    /**
     * @Route("/{altName}/kursy/", name="shark_rates_page", options={"expose"=true})
     * @ParamConverter("shark", class="AppBundle:Shark")
     * @Method("GET")
     *
     * @param Shark $shark
     * @param Request $request
     * @return Response
     */
    public function ratesAction(Shark $shark = null, Request $request)
    {
        $this->setRoutePrefix($request, 'shark');

        if (!$shark) {
            return $this->redirectToRoute('shark_index');
        }

        $cityManager = $this->get("app.city_manager");
        $city = $cityManager->getCityEntity();

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Главная", "homepage");
        $breadcrumbs->addRouteItem("Банки", "shark_index");
        $breadcrumbs->addRouteItem($shark->getName(), "shark_page", ["altName" => $shark->getAltName()]);
        $breadcrumbs->addItem("Курсы");

        $selectDate = $request->query->get('date');
        if (!empty($selectDate)) {
            $selectDate = date('Y-m-d', strtotime($selectDate));
        }

        $sharkLastRates = [];
        /* @var $sharkRates ExchangeRate  */
        $sharkRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->getSharkRates($shark, $city, ExchangeRate::TYPE_NO_CASH);
        if (!empty($sharkRates)) {
            if (empty($selectDate)) {
                $sharkLastRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRateHist')->getLastRatesHistory($sharkRates->getId());
                if (!empty($sharkLastRates)) {
                    $sharkLastRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRateHist')->transformToArray($sharkLastRates);
                }
                $sharkRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->transformToArray($sharkRates);
            } else {
                $sharkLastRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRateHist')->getRatesHistory($sharkRates->getId(), $selectDate);
                if (!empty($sharkLastRates)) {
                    $sharkRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRateHist')->transformToArray($sharkLastRates[0]);
                    $sharkLastRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRateHist')->transformToArray($sharkLastRates[1]);
                }
            }
        } else {
            $sharkRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->getSharkRates($shark, $city, ExchangeRate::TYPE_CASH);
            if (!empty($sharkRates)) {
                $sharkLastRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRateHist')->getLastRatesHistory($sharkRates->getId());
                if (!empty($sharkLastRates)) {
                    $sharkLastRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRateHist')->transformToArray($sharkLastRates);
                }
                $sharkRates = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->transformToArray($sharkRates);
            }
        }

        $ratesInOthersSharks = $this->getDoctrine()->getRepository('AppBundle:ExchangeRate')->getRatesInOtherSharks($city, $shark);

        $currencies = $this->getDoctrine()->getRepository('AppBundle:Currency')->getExchangeCurrencies();

        $params = [
            'shark' => $shark,
            'currencies' => $currencies,
            'sharkRates' => $sharkRates,
            'sharkLastRates' => $sharkLastRates,
            'ratesInOthersSharks' => $ratesInOthersSharks
        ];

        return $this->render(':shark:rates.html.twig', $params);

    }

    /**
     * @Route("/{altName}/reviews/", name="shark_reviews_page")
     * @ParamConverter("shark", class="AppBundle:Shark")
     * @Method({"GET", "POST"})
     *
     * @param Shark $shark
     * @param Request $request
     *
     * @return Response
     */
    public function reviewsAction(Shark $shark = null, Request $request)
    {
        $this->setRoutePrefix($request, 'shark');

        if (!$shark) {
            return $this->redirectToRoute('shark_index');
        }

        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);

        if ($this->getRequest()->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $review->setShark($shark);

                $em = $this->getDoctrine()->getManager();
                $em->persist($review);
                $em->flush();

                // TODO Це якесь лайно, але чому на автоматі на додається категорія я не розумію :(
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
                $reviewAnswer->setShark($shark);
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
        $breadcrumbs->addRouteItem("Банки", "shark_index");
        $breadcrumbs->addRouteItem($shark->getName(), "shark_page", ["altName" => $shark->getAltName()]);
        $breadcrumbs->addItem("Отзывы");

        $branches = $this->getDoctrine()->getRepository('AppBundle:Shark')->findByParent($shark);
        $atms = $this->getDoctrine()->getRepository('AppBundle:Atm')->findByShark($shark);

        $city = $this->get('app.city_manager')->getCityEntity();
        $rates = $this->get('app.tools')->getSharkRates($shark, $city);
        $currencies = $this->getDoctrine()->getRepository('AppBundle:Currency')->getExchangeCurrencies();
        $categories = $this->getDoctrine()->getRepository('AppBundle:Category')->findAll();
        $reviews = $this->getDoctrine()->getRepository('AppBundle:Review')->findBy(['shark' => $shark]);

        $params = [
            'shark' => $shark,
            'branches' => $branches,
            'atms' => $atms,
            'currencies' => $currencies,
            'categories' => $categories,
            'city' => $city,
            'reviews' => $reviews,
            'form' => $form->createView(),
        ];

        $params = array_merge($rates, $params);

        return $this->render(':shark:reviews.html.twig', $params);

    }

    /**
     * @Route("/ajax/getBranches/", name="shark_ajax_get_branches",
     *          options={"expose"=true},
     *          condition="request.isXmlHttpRequest()"
     * )
     * @Method({"POST"})
     */
    public function getBranchesAction(Request $request)
    {
        $altName = $request->request->get('altName');
        $shark = $this->getDoctrine()->getRepository('AppBundle:Shark')->findOneByAltName($altName);
        $branches = $this->getDoctrine()->getRepository('AppBundle:Shark')->findByParent($shark);
        $output = $this->renderView(':shark:inner_block_shark_branches.html.twig', ['branches' => $branches, 'mapId' => $shark->getId()]);

        $jsonResponseHelper = $this->get('app.json_response_helper');
        $response = $jsonResponseHelper->prepareJsonResponse();

        $response->setData($output);

        return $response;
    }
}
