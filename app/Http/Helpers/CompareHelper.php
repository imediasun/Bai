<?php


class CompareHelper
{
    public function getList()
    {
        $list = $this->session->get($this->session_slug);
        $comparisonList = array();
        foreach ($list as $item){
            $comparisonList[] = $this->em->getRepository($this->entity)->find($item);
        }
        return $comparisonList;
    }

    public function compareList()
    {
        $comparisonList = $this->getList();
        //$this->getPropLine();
        $manager = $this;

        $props = $this->props;

        return new Response($this->templating->render($this->template, compact('comparisonList', 'manager', 'props')));
    }

    public function dropCompare()
    {
        $this->session->set($this->session_slug, null);
        return new Response('drop');
    }

    public function toggleCompare($id)
    {
        if($this->session->get($this->session_slug)){
            $list = $this->session->get($this->session_slug);
            if (in_array($id, $list)){
                unset($list[array_search($id, $list)]);
                $this->session->set($this->session_slug, $list);
                return new Response('remove');
            }
            $list[] = $id;
            $this->session->set($this->session_slug, $list);
            return new Response('add');
        }
        $list = array($id);
        $this->session->set($this->session_slug, $list);
        return new Response('add');
    }
}