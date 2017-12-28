<?php

use \App\SeoRecord;
use \Illuminate\Support\Facades\Route;

class SeoHelper
{
    public static function getUniqueSeoRecords()
    {
        $request = request();
        $uri = $request->getRequestUri();
        // find seo record by full uri

        $seoRecord = SeoRecord::where('url', $uri)
            ->where('is_unique_seo_data', true)
            ->get();

        return $seoRecord;
    }

    public function getUrls()
    {
        $raw_routes = Route::getRoutes()->getIterator();
        $routes = [];
        $base_url = url('/') . '/';
        $url404 = $base_url.'404';
        $url500 = $base_url.'500';

        foreach ($raw_routes as $item) {
            if(in_array('GET', $item->methods) &&
                stristr($item->uri, 'admin') === FALSE &&
                stristr($item->uri, 'api/') === FALSE &&
                stristr($item->uri, 'sitemap') === FALSE &&
                url($item->uri) != $url404 &&
                url($item->uri) != $url500
            ){
                $routes[] = $item;
            }
        }

        return $routes;
    }

    public static function getSeoRecord()
    {

        $request = request();
        $routePrefix = $request->route()->getPrefix();

        $uri = $request->getRequestUri();
        // find seo record by full uri
        $seoRecord = SeoRecord::where('url', $uri)
            ->where('is_unique_seo_data', false)
            ->get();

//        if (!is_null($seoRecord)) {

            return $seoRecord;
//        }
//        dd(request()->route()->uri());

//        // find seo record by path
//        $route = $request->route()->getAction();
//        $path = $route->getPath();
//
//        $seoRecord = $this->em->getRepository('AppBundle:SeoRecord')->findOneBy(array('url' => $path));
//        if (!is_null($seoRecord)) {
//            $seoRecord = $this->setVariables($seoRecord, $params);
//
//            return $seoRecord;
//        }
//
//        // find seo record by search route
//        $collection = new RouteCollection();
//        $allSeoRecords = $this->em->getRepository('AppBundle:SeoRecord')->findAll();
//
//        /* @var $defaultSeoRecord SeoRecord */
//        $defaultSeoRecord = $this->em->getRepository('AppBundle:SeoRecord')->findOneByUrl('/');
//        foreach ($allSeoRecords as $seoRecord) {
//            $route = new Route($seoRecord->getUrl());
//
//            if (strpos($seoRecord->getUrl(), '{currency}') !== false) {
//                $route->setRequirement('currency', 'usd|eur|rub|gbp|chf|jpy|cny|inr|kgs|try');
//            }
//
//            $collection->add($seoRecord->getUrl(), $route);
//        }
//        $context = new RequestContext($_SERVER['REQUEST_URI']);
//        $matcher = new UrlMatcher($collection, $context);
//
//        try {
//            $route = $matcher->match($request->getPathInfo());
//
//            $seoRecord = $this->em->getRepository('AppBundle:SeoRecord')->findOneBy(array('url' => $route['_route']));
//
//            if (!is_null($seoRecord)) {
//                $seoRecord = $this->setVariables($seoRecord, $params);
//
//                return $seoRecord;
//            }
//
//        } catch (\Exception $e) {
//        }
//
//        if (!empty($routePrefix) && isset($params['altName']) ) {
//            $entity = $this->em->getRepository("AppBundle:" . (ucfirst($routePrefix)))->findOneByAltName($params['altName']);
//            if ($entity) {
//                $defaultSeoRecord->setMetaDescription($entity->getMetaDescription()?:$defaultSeoRecord->getMetaDescription());
//                $defaultSeoRecord->setHeaderTitle($entity->getMetaTitle()?:$defaultSeoRecord->getHeaderTitle());
//                $defaultSeoRecord->setBreadcrumbs($entity->getBreadcrumbs()?:$defaultSeoRecord->getBreadcrumbs());
//            }
//        }
//        return $defaultSeoRecord;
    }
}