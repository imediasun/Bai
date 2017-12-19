<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Review;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Route("/otzivy")
 */
class ReviewController extends Controller
{

    /**
     * @Route("/filter/{bankAlt}/{product}/{cityAlt}/{rating}/", name="review_filter")
     */
    public function filterAction($bankAlt = null, $product = null, $cityAlt = null, $rating = null){

        $bank = null;
        $category = null;
        $city = null;

        if($bankAlt && $bankAlt != 'null'){
            $bank = $this->getDoctrine()->getRepository('AppBundle:Bank')->findOneByAltName($bankAlt);
        }
        if($product && $product != 'null'){
            $category = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneByAltName($product);
        }
        if($cityAlt && $cityAlt != 'null'){
            $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByAltName($cityAlt);
        }
        $reviews = $this->getDoctrine()->getRepository('AppBundle:Review')->findByFilter($bank, $category, $city);

        return $this->render(':review:filter_bank.html.twig', compact('reviews'));
    }

    /**
     * @Route("/review-pagination/", name="review_pagination")
     */
    public function paginationAction(Request $request){
        $bank = $this->getDoctrine()->getRepository('AppBundle:Bank')->findOneByAltName($request->get('bank'));
        $category = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneByAltName($request->get('category'));
        $city = $this->getDoctrine()->getRepository('AppBundle:City')->find($request->get('city'));

        $reviews = $this->getDoctrine()->getRepository('AppBundle:Review')->findByFilter($bank, $category, $city, null, $request->get('page'));
        $hasMore = count($this->getDoctrine()->getRepository('AppBundle:Review')->hasMorePages($bank, $category, $city, null, $request->get('page')));

        return $this->render(':review:filter_bank.html.twig', compact('reviews', 'hasMore'));
    }

    /**
     * @Route("", name="review_all")
     * @Route("/", name="review_all_slash")
     */
    public function reviewAllAction(){
        $banks = $this->getDoctrine()->getRepository('AppBundle:Bank')->getBanks('ratingClients', 'DESC', 0, 100);
        $categories = $this->getDoctrine()->getRepository('AppBundle:Category')->getBankReviews();
        $reviews = $this->getDoctrine()->getRepository('AppBundle:Review')->findByFilter();
        return $this->render('review/review_all.html.twig', compact('banks', 'categories', 'reviews'));
    }

    /**
     * @Route("/otven-na-otziv/{review}/", name="review_answer")
     */
    public function answerAction(Review $review, Request $request){

        $newReview = new Review();
        if($request->get('username')){
            $newReview->setAuthor($request->get('username'));
        }
        if($request->get('email')){
            $newReview->setEmail($request->get('email'));
        }
        $newReview->setParent($review);
        $newReview->setDescription($request->get('description'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($newReview);
        $em->flush();

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * @Route("/review-process/", name="review_new_process")
     */
    public function newReviewProcessAction( Request $request ){

        $newReview = new Review();
        $newReview->setDescription($request->get('description'));
        $newReview->setSubject($request->get('subject'));
        $newReview->setHiddenDescription($request->get('for_admin'));
        if($request->get('username')){
            $newReview->setAuthor($request->get('username'));
        }
        if($request->get('email')){
            $newReview->setEmail($request->get('email'));
        }
        $newReview->setCity($request->get('city'));
        $newReview->setOverall($request->get('rate'));

        $bank = $this->getDoctrine()->getRepository('AppBundle:Bank')->findOneByAltName($request->get('bank'));
        $newReview->setBank($bank);


        $category = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneByAltName($request->get('servise'));
        $newReview->setCategory($category);


        $em = $this->getDoctrine()->getManager();
        $em->persist($newReview);
        $em->flush();
        return new Response('true');
    }

    /**
     * @Route("/get-product-options/{bank}/", name="get_product_options")
     */
    public function getProductOptionsAction($bank){
        $bank = $this->getDoctrine()->getRepository('AppBundle:Bank')->findOneByAltName($bank);

        return $this->render(':review/options:product_options.html.twig', compact('bank'));
    }

    /**
     * @Route("/get-city-options/{bank}/", name="get_city_options")
     */
    public function getCityOptionsAction($bank){
        if ($bank != 'null'){
            $bank = $this->getDoctrine()->getRepository('AppBundle:Bank')->findOneByAltName($bank);
            //$branch = $this->getDoctrine()->getRepository('B')
            $cities = [];
            foreach ($bank->getChildren() as $value){
                if (!in_array($value->getCity(), $cities)){
                    $cities[] = $value->getCity();
                }
            }
            return $this->render(':review/options:city_options.html.twig', compact('cities'));
        }

        $cities = $this->get('app.tools')->getAllCities();
        return $this->render(':review/options:city_options.html.twig', compact('cities'));
    }

    /**
     * @Route("/get-bank-options/{category}/", name="get_bank_options")
     */
    public function getBankOptionsAction($category){
        $switch = [
            'avtokredityi'=>'autoCredits',
            'kredityi'=>'credits',
            'depozityi'=>'deposits',
            'ipoteka'=>'mortgages',
            'кartyi'=>'creditCards',
            'debetovyie-kartyi'=>'creditCards',
            'obmen_valyut'=>'null',
            'obsluzhivanie'=>'null',
        ];
        if ($category == 'null'){
            $value = $category;
        }else{
            $value = $switch[$category];
        }
        $banks = $this->getDoctrine()->getRepository('AppBundle:Bank')->getBanks('ratingClients', 'DESC', 0, 100);
        return $this->render(':review/options:bank_options.html.twig', compact('value', 'banks'));
    }


    /**
     * @Route("/{param1}/{param2}/{param3}/{param4}", name="review_target")
     * @Route("/{param1}/{param2}/{param3}/{param4}/", name="review_target_slash")
     */
    public function targetReview($param1, $param2 = null, $param3 = null, $param4 = null){

        $banks = $this->getDoctrine()->getRepository('AppBundle:Bank')->getBanks('ratingClients', 'DESC', 0, 100);
        $categories = $this->getDoctrine()->getRepository('AppBundle:Category')->getBankReviews();

        $bank = null;
        $category = null;
        $city = null;

        $bank = $this->getDoctrine()->getRepository('AppBundle:Bank')->findOneByAltName($param1);
        if ($bank){
            if ($param2){
                $category = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneByAltName($param2);
                if ($category && $param3){
                    $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByAltName($param3);
                }else{
                    $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByAltName($param2);
                }
            }
        }else{
            $category = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneByAltName($param1);
            if ($category && $param2){
                $city = $this->getDoctrine()->getRepository('AppBundle:City')->findOneByAltName($param2);
            }
        }
        if (!$category && !$bank && !$bank) {
            throw $this->createNotFoundException('The reviews does not exist');
        }
        $reviews = $this->getDoctrine()->getRepository('AppBundle:Review')->findByFilter($bank, $category, $city);

        $params = compact('bank', 'category', 'city', $param4);
        return $this->render('review/review_target.html.twig', compact('banks', 'categories', 'reviews', 'params'));
    }
}
