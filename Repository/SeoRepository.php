<?php

namespace PN\SeoBundle\Repository;

use Doctrine\ORM\EntityRepository;

class SeoRepository extends EntityRepository {

    public function findBySlugAndBaseRouteAndNotId($seoBaseRouteId, $slug, $seoId) {
        $connection = $this->getEntityManager()->getConnection();
        $sql = "SELECT id FROM seo WHERE slug = :slug AND seo_base_route_id=:seoBaseRouteId AND id != :seoId AND deleted=:deleted";

        $statement = $connection->prepare($sql);
        $statement->bindValue("slug", $slug);
        $statement->bindValue("seoBaseRouteId", $seoBaseRouteId);
        $statement->bindValue("seoId", $seoId);
        $statement->bindValue("deleted", FALSE);
        $statement->execute();

        $queryResult = $statement->fetchColumn();
        if (!$queryResult) {
            return null;
        }
        return $this->find($queryResult);
    }

    public function findOneBySlugAndBaseRoute($slug, $seoBaseRouteId) {
        $connection = $this->getEntityManager()->getConnection();
        $sql = "SELECT id FROM seo WHERE slug = :slug AND seo_base_route_id=:seoBaseRouteId AND deleted=:deleted Limit 1";

        $statement = $connection->prepare($sql);
        $statement->bindValue("slug", $slug);
        $statement->bindValue("seoBaseRouteId", $seoBaseRouteId);
        $statement->bindValue("deleted", FALSE);

        $statement->execute();

        $queryResult = $statement->fetchAll();

        $result = array();
        foreach ($queryResult as $key => $r) {
            $result = $this->find($r['id']);
        }
        return $result;
    }

    public function findByFocusKeywordAndNotId($focusKeyword, $seoId) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT id FROM seo WHERE focus_keyword = :focusKeyword AND id != :seoId AND deleted=:deleted";

        $statement = $connection->prepare($sql);
        $statement->bindValue("focusKeyword", $focusKeyword);
        $statement->bindValue("seoId", $seoId);
        $statement->bindValue("deleted", FALSE);

        $statement->execute();

        $queryResult = $statement->fetchAll();

        $result = array();
        foreach ($queryResult as $key => $r) {
            $result[$key] = $this->find($r['id']);
        }
        return $result;
    }

    public function findOneSeo($seoBaseRouteId, $slug) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT s.id FROM  seo s  "
                . "WHERE seo_base_route_id=:seoBaseRouteId AND  slug=:slug "
                . "LIMIT 1";

        $statement = $connection->prepare($sql);
        $statement->bindValue("seoBaseRouteId", $seoBaseRouteId);
        $statement->bindValue("slug", $slug);
        $statement->execute();

        $queryResult = $statement->fetchColumn();
        if (!$queryResult) {
            return null;
        }
        return $this->find($queryResult);
    }

}
