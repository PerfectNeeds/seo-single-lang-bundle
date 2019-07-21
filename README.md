Getting Started With PNSeoBundle for single languages web apps
==================================

### Prerequisites
1. Symfony 3.4
3. [PNServiceBundle](https://github.com/PerfectNeeds/service-bundle)


Installation
------------

Installation is a quick (I promise!) 7 step process:

1. Download PNSeoBundle using composer
2. Enable the Bundle in AppKernel
3. Create your Seo class
4. Create your SeoRepository class
5. Configure the PNSeoBundle
6. Import PNSeoBundle routing
7. Update your database schema
------------
### Step 1: Download PNSeoBundle using composer
Require the bundle with composer:
```sh
$ composer require perfectneeds/seo-single-lang-bundle "~1.0"
```
### Step 2: Enable the Bundle in AppKernel
Require the bundle with composer:
```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new PN\SeoBundle\PNSeoBundle(),
        new \PN\ServiceBundle\PNServiceBundle(),
        // ...
    );
}
```

### Step 3: Create your Seo class
The goal of this bundle is to persist some `Seo` class to a database. Your first job, then, is to create the
`Seo` class for your application. This class can look and act however
you want: add any properties or methods you find useful. This is *your*
`Seo` class.

The bundle provides base classes which are already mapped for most
fields to make it easier to create your entity. Here is how you use it:

1.  Extend the base `Seo` class (from the `Entity` folder if you are
    using any of the doctrine variants)
2.  Map the `id` field. It must be protected as it is inherited from the
    parent class.

#### Caution!

When you extend from the mapped superclass provided by the bundle, don't redefine the mapping for the other fields as it is provided by the bundle.

In the following sections, you'll see examples of how your `Seo` class should look, depending on how you're storing your seos (Doctrine ORM).

##### Note

The doc uses a bundle named `SeoBundle`. However, you can of course place your seo class in the bundle you want.

###### Caution!

If you override the __construct() method in your Seo class, be sure to call parent::__construct(), as the base Seo class depends on this to initialize some fields.


#### Doctrine ORM Seo class

If you're persisting your seo via the Doctrine ORM, then your `Seo` class should live in the Entity namespace of your bundle and look like this to start:

*You can add all relations between other entities in this class

```php
<?php
// src/PN/Bundle/SeoBundle/Entity/Seo.php

namespace PN\Bundle\SeoBundle\Entity;

use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\ORM\Mapping as ORM;

// DON'T forget the following use statement!!!
use PN\SeoBundle\Entity\Seo as BaseSeo;
use PN\SeoBundle\Model\SeoTrait;

/**
 * Seo
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table("seo", uniqueConstraints={@UniqueConstraint(name="slug_unique", columns={"slug", "seo_base_route_id"})})
 * @ORM\Entity(repositoryClass="PN\Bundle\SeoBundle\Repository\SeoRepository")
 */
class Seo extends BaseSeo {

    use SeoTrait;
   
    
    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
```

```php

### Step 4: Create your SeoRepository class
You can use this `Repository` to add any custom methods 

```php
<?php
// src/PN/Bundle/SeoBundle/Repository/SeoRepository.php


namespace PN\Bundle\SeoBundle\Repository;

use PN\SeoBundle\Repository\SeoRepository as BaseSeoRepository;

class SeoRepository extends BaseSeoRepository {

}
```


### Step 5: Configure the PNSeoBundle
Add the following configuration to your config.yml file according to which type of datastore you are using.

```ymal
# app/config/config.yml 

doctrine:
   orm:
        # search for the "ResolveTargetEntityListener" class for an article about this
        resolve_target_entities: 
            PN\SeoBundle\Entity\Seo: PN\Bundle\SeoBundle\Entity\Seo

pn_seo:
    # The fully qualified class name (FQCN) of the Seo class which you created in Step 3.
    seo_class: PN\Bundle\SeoBundle\Entity\Seo
```

### Step 6: Import PNSeoBundle routing files

```ymal
# app/config/routing.yml 

pn_seo:
    resource: "@PNSeoBundle/Resources/config/routing.yml"
```

### Step 7: Update your database schema
Now that the bundle is configured, the last thing you need to do is update your database schema because you have added a new entity, the `Seo` class which you created in Step 3.

```sh
$ php bin/console doctrine:schema:update --force
```

------
# How to use PNSeoBundle

1. Use **Seo** in Entity using Doctrine ORM
2. Use **Seo** in Form Type
3. Use **Seo** in controller
4. Use **Seo** in details page like `show.html.twig`
--------------------------
#### 1. Use Seo in Entity using Doctrine ORM

First of all you need to add a relation between an Entity need to use Seo with Seo class in `src/PN/Bundle/SeoBundle/Entity/Seo.php`
_ex. Blogger, Product, etc ..._
Example entities:  
Seo.php 
```php
<?php
// src/PN/Bundle/SeoBundle/Entity/Seo.php

namespace PN\Bundle\SeoBundle\Entity;

use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\ORM\Mapping as ORM;
use PN\SeoBundle\Entity\Seo as BaseSeo;
use PN\SeoBundle\Model\SeoTrait;

/**
 * Seo
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table("seo", uniqueConstraints={@UniqueConstraint(name="slug_unique", columns={"slug", "seo_base_route_id"})})
 * @ORM\Entity(repositoryClass="PN\Bundle\SeoBundle\Repository\SeoRepository")
 */
class Seo extends BaseSeo {

    use SeoTrait;
    
    /**
     * @ORM\OneToOne(targetEntity="\PN\Bundle\CMSBundle\Entity\DynamicPage", mappedBy="seo")
     */
    protected $dynamicPage;
    
    // Add here your own relations
    
    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
```

DynamicPage.php
```php
<?php

namespace PN\Bundle\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PN\ServiceBundle\Model\DateTimeTrait;


/**
 * DynamicPage
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="dynamic_page")
 * @ORM\Entity(repositoryClass="PN\Bundle\CMSBundle\Repository\DynamicPageRepository")
 */
class DynamicPage {

    use DateTimeTrait;
    ....

    /**
     * @ORM\OneToOne(targetEntity="\PN\Bundle\SeoBundle\Entity\Seo", inversedBy="dynamicPage", cascade={"persist", "remove" })
     */
    protected $seo;
    
    ....
}

```

#### 2. Use _Seo_ in Form Type
You need to add Seo Type in any Form type to use this magical tool

DynamicPageType.php
```php
<?php

namespace PN\Bundle\CMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

// DON'T forget the following use statement!!!
use PN\SeoBundle\Form\SeoType;


class DynamicPageType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('seo', SeoType::class)
                ......
                ;
    }
    .....
}
```
### 3. Use _Seo_ in controller
###### You need to call this method to get an Entity based on slug, this method use in any action you will call an Entity through the slug

DynamicPageController.php
```php
    /**
     * @Route("/{slug}", name="fe_dynamic_page_show", methods={"GET", "POST"})
     */
    public function showAction(Request $request, $slug) {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->get("fe_seo")->getSlug($request, $slug, new DynamicPage());
        if ($entity instanceof RedirectResponse) {
            return $entity;
        }
        if (!$entity) {
            throw $this->createNotFoundException();
        }
        
        // your own logic
    }
```

### Options:
##### request:
**type**: `Symfony\Component\HttpFoundation\Request` object
Instance of Request 

##### slug:
**type**: `string` 
The slug value that passed from route parameter 

##### entityClass:
**type**: `Object` 
An istance of any Entity 


### 4. Use Seo in details page like _show.html.twig_
this snippet of code used to add the meta tags and html title in `base.html.twig`
So you need to add 2 an empty blocks in `base.html.twig` (**metaTag** and **title**)
```twig
{% set seo = dynamicPage.seo %}
{% use '@PNSeo/FrontEnd/seo.html.twig' %}
```


Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/PerfectNeeds/seo-multi-lang-bundle).

When reporting a bug, it may be a good idea to reproduce it in a basic project
built using the [Symfony Standard Edition](https://github.com/symfony/symfony-standard)
to allow developers of the bundle to reproduce the issue by simply cloning it
and following some steps.