<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Credit;
use AppBundle\Entity\Review;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Route("/test")
 */
class ComparetestController extends Controller
{
    /**
     * @Route("", name="compare_test")
     */
    public function indexAction(){
        $act = 'compareList';
        $id = '';
        $compare = $this->get(CompareManager::class);
        $compare->init(Credit::class, 'kredity', ':templates:kredity_list.html.twig');
        return $compare->$act($id);

    }

}