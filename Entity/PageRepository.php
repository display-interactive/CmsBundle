<?php

namespace Display\CmsBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PageRepository
 */
class PageRepository extends EntityRepository
{
    // Page status
    const STATUS_DRAFT    = 'Draft';
    const STATUS_REVIEWED = 'Reviewed';
    const STATUS_PUBLISH  = 'Published';
    const STATUS_HIDDEN   = 'Hidden';

    /**
     * Return list of available status
     *
     * @return array
     */
    public static function getAvailableStatus()
    {
        return array(
            self::STATUS_DRAFT    => self::STATUS_DRAFT,
            self::STATUS_REVIEWED => self::STATUS_REVIEWED,
            self::STATUS_PUBLISH  => self::STATUS_PUBLISH,
            self::STATUS_HIDDEN   => self::STATUS_HIDDEN
        );
    }

    /**
     * @param string $locale
     * @param string $status
     * @return Page
     */
    public function findHome($locale, $status = self::STATUS_PUBLISH)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('partial p.{id, locale, slug, title, headerTitle, description, content, cacheable, cachedAt}')
            ->from('DisplayCmsBundle:Page', 'p')
            ->where('p.parent IS NULL')
            ->andWhere('p.locale = :locale')
            ->setParameter('locale', $locale)
        ;

        if (null !== $status) {
            $qb
                ->andWhere('p.status = :status')
                ->setParameter('status', $status)
            ;
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $slug
     * @param string $locale
     * @param string $status
     * @return Page
     */
    public function findBySlug($slug, $locale, $status = self::STATUS_PUBLISH)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('partial p.{id, locale, slug, title, headerTitle, description, content, cacheable, cachedAt}, partial pr.{id, slug, title}')
            ->from('DisplayCmsBundle:Page', 'p')
            ->join('p.parent', 'pr')
            ->where('p.slug = :slug')
            ->setParameter('slug', $slug)
            ->andWhere('p.locale = :locale')
            ->setParameter('locale', $locale)
        ;

        if (null !== $status) {
            $qb
                ->andWhere('p.status = :status')
                ->setParameter('status', $status)
            ;
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $locale
     * @param array $ids
     * @return array
     */
    public function findByLocale($locale, $ids = array())
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('p')
            ->from('DisplayCmsBundle:Page', 'p')
            ->where('p.locale = :locale')
            ->setParameter('locale', $locale);
        ;

        if (count($ids)) {
            $qb
                ->andWhere($qb->expr()->notIn('p.id', ':ids'))
                ->setParameter('ids', $ids)
            ;
        }

        return $qb->getQuery()->getResult();
    }
}