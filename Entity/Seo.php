<?php

namespace PN\SeoBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class Seo {

    /**
     * @ORM\ManyToOne(targetEntity="PN\SeoBundle\Entity\SeoBaseRoute")
     */
    protected $seoBaseRoute;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="slug", type="string", length=255)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_description", type="text", nullable=true)
     */
    protected $metaDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="focus_keyword", type="string", length=255, nullable=true)
     */
    protected $focusKeyword;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_keyword", type="string" , nullable=true)
     */
    protected $metaKeyword;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_tags", type="text" , nullable=true)
     */
    protected $metaTags;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="smallint", nullable=true)
     */
    protected $state;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $lastModified;

    /**
     * @ORM\Column(name="deleted", type="boolean", options={"default" = 0}),nullable=false)
     */
    protected $deleted = false;

    /**
     * @ORM\OneToOne(targetEntity="PN\SeoBundle\Entity\SeoPage", mappedBy="seo")
     */
    protected $seoPage;

    public function __clone() {
        $this->id = NULL;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->seoSocials = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getRelationalEntity() {
        $excludeMethods = ['id', 'seoBaseRoute', 'title', "slug", "metaDescription", "focusKeyword", "metaKeyword", "state", "lastModified", "deleted", "seoSocials", "__initializer__", "__isInitialized__", "__cloner__"];

        $allObjects = get_object_vars($this);
        foreach ($allObjects as $objectName => $objectValue) {
            if (in_array($objectName, $excludeMethods)) {
                continue;
            }
            if ($objectValue != NULL) {
                return $objectValue;
            }
        }
        return NULL;
    }

    /**
     * Now we tell doctrine that before we persist or update we call the updatedTimestamps() function.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps() {
        $this->setLastModified(new \DateTime(date('Y-m-d H:i:s')));
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Seo
     */
    public function setSlug($slug) {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug() {
        return $this->slug;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Seo
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set metaDescription
     *
     * @param string $metaDescription
     * @return Seo
     */
    public function setMetaDescription($metaDescription) {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription() {
        return $this->metaDescription;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Seo
     */
    public function setDeleted($deleted) {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted() {
        return $this->deleted;
    }

    /**
     * Set focusKeyword
     *
     * @param string $focusKeyword
     *
     * @return Seo
     */
    public function setFocusKeyword($focusKeyword) {
        $this->focusKeyword = $focusKeyword;

        return $this;
    }

    /**
     * Get focusKeyword
     *
     * @return string
     */
    public function getFocusKeyword() {
        return $this->focusKeyword;
    }

    /**
     * Set metaKeyword
     *
     * @param string $metaKeyword
     *
     * @return Seo
     */
    public function setMetaKeyword($metaKeyword) {
        $this->metaKeyword = $metaKeyword;

        return $this;
    }

    /**
     * Get metaKeyword
     *
     * @return string
     */
    public function getMetaKeyword() {
        return $this->metaKeyword;
    }

    /**
     * Set lastModified
     *
     * @param \DateTime $lastModified
     * @return Seo
     */
    public function setLastModified($lastModified) {
        $this->lastModified = $lastModified;

        return $this;
    }

    /**
     * Get lastModified
     *
     * @return \DateTime
     */
    public function getLastModified() {
        return $this->lastModified;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return Seo
     */
    public function setState($state) {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState() {
        return $this->state;
    }

    /**
     * Set seoBaseRoute
     *
     * @param \PN\SeoBundle\Entity\SeoBaseRoute $seoBaseRoute
     *
     * @return Seo
     */
    public function setSeoBaseRoute(\PN\SeoBundle\Entity\SeoBaseRoute $seoBaseRoute = null) {
        $this->seoBaseRoute = $seoBaseRoute;

        return $this;
    }

    /**
     * Get seoBaseRoute
     *
     * @return \PN\SeoBundle\Entity\SeoBaseRoute
     */
    public function getSeoBaseRoute() {
        return $this->seoBaseRoute;
    }

    /**
     * Set seo
     *
     * @param \PN\SeoBundle\Entity\SeoPage $seoPage
     *
     * @return Seo
     */
    public function setSeoPage(\PN\SeoBundle\Entity\SeoPage $seoPage = null) {
        $this->seoPage = $seoPage;

        return $this;
    }

    /**
     * Get seoPage
     *
     * @return \PN\SeoBundle\Entity\SeoPage
     */
    public function getSeoPage() {
        return $this->seoPage;
    }

    /**
     * Set metaTags
     *
     * @param string $metaTags
     * @return Seo
     */
    public function setMetaTags($metaTags) {
        $this->metaTags = $metaTags;

        return $this;
    }

    /**
     * Get metaTags
     *
     * @return string
     */
    public function getMetaTags() {
        return $this->metaTags;
    }

}
