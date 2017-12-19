<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RouteController extends Controller
{
    protected $searchFilter;

    public function setRoutePrefix(Request $request, $routePrefix = '')
    {
        $request->attributes->set('routePrefix', $routePrefix);

        $queryString = $request->request->all();

        if (isset($queryString['prop'])) {
            foreach ($queryString['prop'] as $prop => $value) {
                if (!$value) {
                    continue;
                }
                list($type, $id) = explode('_', $prop);
                $this->searchFilter .= ($this->searchFilter ? ' AND ' : '') . '( prop.prop_id = ' . $id;
                switch ($type) {
                    case 'select':
                        $this->searchFilter .= ' AND prop.prop_option_id = ' . $value;
                        break;
                    case 'single':
                        $this->searchFilter .= ' AND prop.value_from = ' . $value;
                        break;
                    case 'range':
                        $this->searchFilter .= ' AND prop.value_from >= ' . $value . ' AND prop.value_to <= ' . $value;
                        break;
                }
                $this->searchFilter .= ')';
            }
        } else {
            $this->searchFilter = '';
        }
    }
}
