<?php

namespace Display\CmsBundle\Tests\Entity;

use Display\CmsBundle\Entity\Page;
use Display\CmsBundle\Entity\PageRepository;

class PageTest extends EntityTestCase
{
    public function testGetterSetter()
    {
        $homepage = new Page();
        $now = new \DateTime();
        $homepage
            ->setSlug(null)
            ->setLocale('fr')
            ->setTitle('Accueil')
            ->setContent('Accueil Content')
            ->setStatus(PageRepository::STATUS_PUBLISH)
            ->setCacheable(false)
            ->setCachedAt($now)
        ;

        $this->assertEquals('fr', $homepage->getLocale());
        $this->assertEquals('Accueil', $homepage->getTitle());
        $this->assertEquals('Accueil Content', $homepage->getContent());
        $this->assertEquals(PageRepository::STATUS_PUBLISH, $homepage->getStatus());
        $this->assertNull($homepage->getSlug());
        $this->assertFalse($homepage->getCacheable());
        $this->assertEquals($now, $homepage->getCachedAt());

        $child = $this->createPage('child', 'child');
        $child->setParent($homepage);
        $this->assertEquals($child->getParent(), $homepage);

        $child2 = $this->createPage('child2', 'child2');
        $child2->setParent($homepage);
        $this->assertEquals($child2->getParent(), $homepage);

        $this->entityManager->persist($homepage);
        $this->entityManager->persist($child);
        $this->entityManager->persist($child2);
        $this->entityManager->flush();
        $this->entityManager->detach($homepage);
        $this->entityManager->detach($child);
        $this->entityManager->detach($child2);

        $homepage = $this->getRepository()->findOneBy(array('title' => 'Accueil', 'locale' => 'fr'));


        $this->assertEquals(2, $homepage->getChildren()->count());
        $this->assertEquals('fr', $homepage->getLocale());

        $this->assertEquals('Accueil', $homepage->getTitle());
        $this->assertEquals('Accueil Content', $homepage->getContent());
        $this->assertEquals(PageRepository::STATUS_PUBLISH, $homepage->getStatus());
        $this->assertNull($homepage->getSlug());
        $this->assertFalse($homepage->getCacheable());
        $this->assertEquals($now, $homepage->getCachedAt());


        $this->assertFalse(null === $homepage->getCreatedAt());
        $this->assertFalse(null === $homepage->getUpdatedAt());
        $this->assertEquals($homepage->getUpdatedAt(), $homepage->getCreatedAt());

        $child = $this->getRepository()->findOneBy(array('title' => 'Child', 'locale' => 'fr'));

        $this->assertFalse(null === $child->getCreatedAt());
        $this->assertFalse(null === $child->getUpdatedAt());
        $this->assertEquals($child->getUpdatedAt(), $child->getCreatedAt());
        $this->assertEquals($child->getParent()->getTitle(), $homepage->getTitle());
    }

    public function dataProvider()
    {
        return array(
            array(
                array('parent' => null, 'page' => 'page', 'subpage' => 'subpage'),
                array('parent2' => null, 'page2' => 'page2', 'subpage2' => 'subpage2')
            ),
        );
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetUrl($data)
    {
        $urls = $entities = array();
        $prev = null;
        foreach ($data as $name => $slug) {
            $entity = $this->createPage($name, $slug);
            if ($prev) {
                $entity->setParent($prev);
                $urls[] = $slug;
            }

            $this->assertEquals($entity->getUrl(), implode('/', $urls));
            $entities[] = $prev = $entity;
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
        foreach ($entities as $entity) {
            $this->entityManager->detach($entity);
        }

        $repository = $this->getRepository();
        $subpage = $repository->findBySlug($slug, 'fr');
        $this->assertEquals($subpage->getUrl(), implode('/', $urls));
        $this->assertEquals($subpage->getParent()->getUrl(), $subpage->getParent()->getSlug());
        $this->assertEquals($subpage->getParent()->getParent()->getUrl(), $subpage->getParent()->getParent()->getSlug());
    }

    /**
     * @depends testGetUrl
     * @dataProvider dataProvider
     */
    public function testGetBreadcrumb($data)
    {
        $entities = $breadcrumbs = array();
        $prev = null;
        $iterator = 1;
        foreach ($data as $name => $slug) {
            $entity = $this->createPage($name, $slug);
            $entity->setParent($prev);
            $entities[] = $prev = $entity;
            $this->entityManager->persist($entity);
            $breadcrumbs[] = array(
                'id' => $iterator,
                'title' => ucfirst($name)
            );

            $iterator++;
        }

        $this->entityManager->flush();
        foreach ($entities as $entity) {
            $this->entityManager->detach($entity);
        }

        $repository = $this->getRepository();
        $subpage = $repository->findBySlug($slug, 'fr');


        $this->assertEquals($subpage->getBreadcrumb(), $breadcrumbs);
    }


    /**
     * @param string $text
     * @param string $slug
     * @param string $locale
     * @param string $status
     * @return Page
     */
    private function createPage($text, $slug = null, $locale = 'fr', $status = PageRepository::STATUS_PUBLISH)
    {
        $page = new Page();
        $now = new \DateTime();
        $page
            ->setSlug($slug)
            ->setCacheable(false)
            ->setTitle(ucfirst($text))
            ->setContent($text)
            ->setLocale($locale)
            ->setStatus($status)
            ->setCachedAt($now)
        ;

        return $page;
    }

    /**
     * @return PageRepository
     */
    private function getRepository()
    {
        return $this->entityManager->getRepository('DisplayCmsBundle:Page');
    }
}