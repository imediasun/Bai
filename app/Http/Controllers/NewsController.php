<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bank;
use AppBundle\Entity\Category;
use AppBundle\Entity\City;
use AppBundle\Entity\News;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/novosti")
 */
class NewsController extends RouteController
{
    /**
     * @Route("/", name="news_index")
     * @Method("GET")
     *
     * @return Response
     */
    public function indexAction()
    {
        $news = $this->getDoctrine()->getRepository('AppBundle:News')->findAll();
        $bankNews = $this->getDoctrine()->getRepository('AppBundle:News')->getBankNews();

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Главная", "homepage");
        $breadcrumbs->addItem("Новости", "news_index");

        $params = [
            'news' => $news,
            'bankNews' => $bankNews,
        ];
        return $this->render(':news:index.html.twig', $params);
    }

    /**
     * @Route("/{category}/{altName}/", name="news_page_category", defaults={"altName" = "null"})
     * @Route("/{category}/", name="news_page")
     * @Method("GET")
     *
     * @param $category
     * @param $altName
     * @param Request $request
     *
     * @return Response
     */
    public function newsAction($category, $altName = null, Request $request)
    {
        $this->setRoutePrefix($request, 'news');

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Главная", "homepage");
        $breadcrumbs->addRouteItem("Новости", "news_index");

        /* @var $category Category */

        if ($altName == null){
            $category = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneByAltName($category);
            $news = $this->getDoctrine()->getRepository('AppBundle:News')->findByCategory($category);

            $breadcrumbs->addItem($category->getBreadcrumbs()?:$category->getName());

            $params = [
                'news' => $news,
                'category' => $category,
            ];

            return $this->render(':news:index.html.twig', $params);
        }

        $news = $this->getDoctrine()->getRepository('AppBundle:News')->findOneByAltName($altName);

        /* @var $news News */
        $breadcrumbs->addItem($news->getBreadcrumbs()?:$news->getName());

        return $this->render(':news:page.html.twig', [
            'news' => $news,
        ]);
    }

}
