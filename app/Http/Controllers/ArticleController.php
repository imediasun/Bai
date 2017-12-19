<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bank;
use AppBundle\Entity\Category;
use AppBundle\Entity\City;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/article")
 */
class ArticleController extends RouteController
{
    /**
     * @Route("/", name="article_index")
     * @Method("GET")
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $this->setRoutePrefix($request, 'article');

        $articles = $this->getDoctrine()->getRepository('AppBundle:Article')->findAll();
        $bankArticles = $this->getDoctrine()->getRepository('AppBundle:Article')->getBankArticles();

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Главная", "homepage");
        //$breadcrumbs->addItem("Статьи", "article_index");

        $params = [
            'articles' => $articles,
            'bankArticles' => $bankArticles,
        ];
        //TODO [nkl90][ask]: Почему шаблоны лежат в app/Resources, а не в бандле?
        return $this->render(':article:index.html.twig', $params);
    }

    /**
     * @Route("/{altName}/", name="article_page")
     * @Method("GET")
     *
     * @param $altName
     * @param Request $request
     *
     * @return Response
     */
    public function newsAction($altName, Request $request)
    {
        $this->setRoutePrefix($request, 'article');

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addRouteItem("Главная", "homepage");
        $breadcrumbs->addRouteItem("Статьи", "article_index");

        if ($category = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneByAltName($altName)) {
            $articles = $this->getDoctrine()->getRepository('AppBundle:Article')->findByCategory($category);

            $breadcrumbs->addItem($category->getBreadcrumbs()?:$category->getName());

            $params = [
                'articles' => $articles,
                'category' => $category,
            ];

            return $this->render(':article:index.html.twig', $params);

        } elseif ($article = $this->getDoctrine()->getRepository('AppBundle:Article')->findOneByAltName($altName)) {

            //по дизайну название вне крошки, в отдельном <h1>Заголовке</h1> поэтому убрал это. Хотя может быт и не надо? TODO [nkl90]: <- уззнать
            //$breadcrumbs->addItem($article->getBreadcrumbs()?:$article->getName());

            return $this->render(':article:page.html.twig', [
                'article' => $article,
            ]);
        }

        return $this->redirectToRoute('article_index');

    }

}
