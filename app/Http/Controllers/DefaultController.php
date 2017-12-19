<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $tools = $this->get('app.tools');

        $currencies = $this->getDoctrine()->getRepository('AppBundle:Currency')->getExchangeCurrencies();
        $news = $this->getDoctrine()->getRepository('AppBundle:News')->findBy([], ['createdAt' => 'DESC'], 15);
        $articles = $this->getDoctrine()->getRepository('AppBundle:Article')->findBy([], ['createdAt' => 'DESC'], 5);
        $deposits = $this->getDoctrine()->getRepository('AppBundle:Deposit')->findBy(['parent' => null]);

        $query = $this->getDoctrine()->getRepository('AppBundle:CreditCard')->createQueryBuilder('p')
            ->join('p.creditCardProps', 'card_prop', 'p.id = card_prop.creditCard_id')
            ->where('card_prop.propOption = 238')
            ->getQuery();;
        $debet_cards = $query->getResult();

        $query = $this->getDoctrine()->getRepository('AppBundle:CreditCard')->createQueryBuilder('p')
            ->join('p.creditCardProps', 'card_prop', 'p.id = card_prop.creditCard_id')
            ->where('card_prop.propOption = 239')
            ->getQuery();;
        $credit_cards = $query->getResult();

        $mortgages = $this->getDoctrine()->getRepository('AppBundle:Mortgage')->findBy(['parent' => null]);
        $loans = $this->getDoctrine()->getRepository('AppBundle:Mortgage')->findBy(['parent' => null]);

        $creditsCount = $tools->getCreditsCount();
        $autoCreditsCount = $tools->getAutoCreditsCount();

        return $this->render(':default:index.html.twig', [
            'currencies' => $currencies,
            'news' => $news,
            'articles' => $articles,
            'creditsCount' => $creditsCount,
            'autoCreditsCount' => $autoCreditsCount,
            'depositsCount' => count($deposits),
            'debetCardsCount' => count($debet_cards),
            'creditCardsCount' => count($credit_cards),
            'mortgagesCount' => count($mortgages),
            'loansCount' => count($loans),
        ]);
    }

    /**
     * @Route("/widget", name="widget")
     */
    public function widgetAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $currencies = $em->getRepository('AppBundle:Currency')->findAll();
        $cities = $em->getRepository('AppBundle:City')->findAll();
        $banks = $em->getRepository('AppBundle:Bank')->findAll();

        return $this->render(':default:widget.html.twig', [
            'currencies' => $currencies,
            'cities' => $cities,
            'banks' => $banks,
        ]);
    }

    /**
     * @Route("/widget_html", name="widgetHtml")
     */
    public function widgetHtmlAction(Request $request)
    {
        $orient_horizontal = $request->get('orient_horizontal');
        $width = $request->get('width');

        $banks = $request->get('banks');


        $em = $this->getDoctrine()->getManager();
        $rates = $em->getRepository('AppBundle:ExchangeRate')->findAll();
        $cities = $em->getRepository('AppBundle:City')->findAll();
        $banks = $em->getRepository('AppBundle:Bank')->findAll();


        return $this->render(':default:widgetHtml.html.twig', [
            'orient_horizontal' => $orient_horizontal,
            'width' => $width,
            'color_links' => $request->get('color_links'),
            'color_text' => $request->get('color_text'),
            'color_back' => $request->get('color_back'),
            'currencies' => $rates,
            'cities' => $cities,
            'banks' => $banks,
        ]);
    }
}
