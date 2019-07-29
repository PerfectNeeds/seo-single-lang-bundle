<?php

namespace PN\SeoBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use PN\SeoBundle\Entity\Seo;
use PN\SeoBundle\Entity\SeoBaseRoute;
use PN\Utils\General;

class SeoFormTypeService {

    public $container;
    public $em;
    public $seoClass;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->seoClass = $this->container->getParameter('pn_seo_class');
    }

    public function checkAndGenerateSlug($entity, $seoEntity) {

        if (!$seoEntity instanceof Seo) {
            throw new Exception('$seoEntity Must be instance of Seo ');
        }

        $em = $this->em;
        $seoBaseRoute = $em->getRepository('PNSeoBundle:SeoBaseRoute')->findByEntity($entity);


        if ($seoEntity->getSlug() == null) {
            $slug = $this->getSlug($entity, $seoEntity);
            $seoEntity->setSlug($slug);
        }
        $slugIfExist = $this->checkSlugIfExist($seoBaseRoute, $entity, $seoEntity);
        if ($slugIfExist) {
            return $this->generateSlug($seoBaseRoute, $entity, $seoEntity);
        }
        return $seoEntity->getSlug();
    }

    public function checkSlugIfExist(SeoBaseRoute $seoBaseRoute, $entity, $seoEntity) {
        $em = $this->em;
        $slug = $this->getSlug($entity, $seoEntity);
        if (!method_exists($entity, "getSeo") OR $entity->getSeo()->getId() == null) { // new
            $checkSeo = $em->getRepository($this->seoClass)->findOneBy(array('seoBaseRoute' => $seoBaseRoute->getId(), 'slug' => $slug, 'deleted' => FALSE));
        } else { // edit
            $checkSeo = $em->getRepository($this->seoClass)->findBySlugAndBaseRouteAndNotId($seoBaseRoute->getId(), $slug, $entity->getSeo()->getId());
        }

        if ($checkSeo != null) {
            return true;
        }
        return false;
    }

    //DONE
    private function generateSlug(SeoBaseRoute $seoBaseRoute, $entity, $seoEntity) {
        $tempSlug = $this->getSlug($entity, $seoEntity);
        $i = 0;
        do {
            if ($i == 0) {
                $slug = General::seoUrl($tempSlug);
            } else {
                $slug = General::seoUrl($tempSlug . '-' . $i);
            }
            $seoEntity->setSlug($slug);
            $slugIfExist = $this->checkSlugIfExist($seoBaseRoute, $entity, $seoEntity);
            $i++;
        } while ($slugIfExist == true);
        return $slug;
    }

    //DONE
    private function getSlug($entity, $seoEntity) {
        if ($seoEntity->getSlug()) {
            return $seoEntity->getSlug();
        } else {
            $title = $this->getTitle($entity);
            return General::seoUrl($title);
        }
        return null;
    }

    //DONE
    private function getTitle($entity) {
        $title = $this->getEntityTitle($entity);

        if ($title == null) {
            $title = General::generateRandString();
        }

        return $title;
    }

    //DONE
    private function getEntityTitle($entity) {
        if (method_exists($entity, "getTitle")) {
            return $entity->getTitle();
        } elseif (method_exists($entity, "getName")) {
            return $entity->getName();
        } elseif (method_exists($entity, "__toString")) {
            return $entity->__toString();
        }
        return null;
    }

}
