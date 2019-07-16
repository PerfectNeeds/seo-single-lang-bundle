<?php

namespace PN\SeoBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use PN\SeoBundle\Entity\SeoBaseRoute;
use PN\SeoBundle\Entity\Seo;

class SeoService {

    protected $em;
    protected $context;
    protected $router;
    public $seoClass;
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->router = $container->get('router');
        $this->container = $container;
        $this->seoClass = $this->container->getParameter('pn_seo_class');
    }

    private function checkSlugIfExist(SeoBaseRoute $seoBaseRoute, $slug) {
        $em = $this->em;

        return $em->getRepository($this->seoClass)->findOneBy(['slug' => $slug, 'seoBaseRoute' => $seoBaseRoute->getId()]);
    }

    public function getSlug(Request $request, $slug, $entityClass) {
        if (!is_object($entityClass)) {
            throw new \Exception("Please enter a entity class");
        }
        $em = $this->em;
        $seoBaseRoute = $em->getRepository('PNSeoBundle:SeoBaseRoute')->findByEntity($entityClass);


        $seoEntityDefaultLocale = $this->checkSlugIfExist($seoBaseRoute, $slug);
        if ($seoEntityDefaultLocale) {
            $entity = $seoEntityDefaultLocale->getRelationalEntity();
            return $entity;
        }

        $seoEntityInAllLocale = $em->getRepository($this->seoClass)->findOneSeo($seoBaseRoute->getId(), $slug);
        if ($seoEntityInAllLocale) {
            return $seoEntityInAllLocale->getRelationalEntity();
        }

        return null;
    }

}
