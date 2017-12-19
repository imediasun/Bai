<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TemplateController extends Controller
{
    public function votingAction($code){
        $currency = $this->getDoctrine()->getRepository('AppBundle:Currency')->findOneByCode(strtoupper($code));
        $month = self::getDateName(intval(date('m'))+1);

        $yesChois = count($this->getDoctrine()->getRepository('AppBundle:VotingItem')->findBy(['choice' => '1', 'curCode'=>$code]));
        $noChois = count($this->getDoctrine()->getRepository('AppBundle:VotingItem')->findBy(['choice' => '0', 'curCode'=>$code]));
        if  ($yesChois == 0){ $yesChois = 1; }
        if  ($noChois == 0){ $noChois = 1; }
        $yesChois = round($yesChois/($yesChois + $noChois)*100);
        $noChois = 100 -$yesChois;

        return $this->render(':templates:voting.html.twig', compact('currency', 'month', 'noChois', 'yesChois', 'code'));
    }

    static public function getDateName($month){
        $monthNames = [
            '',
            'январе',
            'феврале',
            'марте',
            'апреле',
            'мае',
            'июне',
            'июле',
            'августе',
            'сентябре',
            'октябре',
            'ноябре',
            'декабре',
        ];
        return $monthNames[intval($month)];
    }
}
