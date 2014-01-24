<?php

namespace Display\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Menu
 *
 * @ORM\Table(name="display_menu")
 * @ORM\Entity(repositoryClass="Display\CmsBundle\Entity\MenuRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Menu extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Page $page
     * @ORM\OneToOne(targetEntity="Page")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     */
    private $page;

    /**
     * @var int $position
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position;

    /**
     * @var Menu $parent
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="cascade")
     */
    private $parent;

    /**
     * @var Menu $children
     * @ORM\OneToMany(targetEntity="Menu", mappedBy="parent", cascade={"persist", "remove"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $children;

    /**
     *
     * @var int $nbChildren
     * @ORM\Column(name="nb_children", type="integer", nullable=false)
     */
    private $nbChildren;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set page
     *
     * @param Page $page
     * @return Menu
     */
    public function setPage(Page $page = null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Menu
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set parent
     *
     * @param Menu $parent
     * @return Menu
     */
    public function setParent(Menu $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Menu
     */
    public function getParent()
    {
        return $this->parent;
    }


    /**
     * Add children
     *
     * @param Menu $child
     * @return Menu
     */
    public function addChild(Menu $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove children
     *
     * @param Menu $child
     */
    public function removeChild(Menu $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set children number
     *
     * @param int $nbChildren
     * @return Menu
     */
    public function setNbChildren($nbChildren)
    {
        $this->nbChildren = $nbChildren;

        return $this;
    }

    /**
     * Get children number
     *
     * @return Menu
     */
    public function getNbChildren()
    {
        return $this->nbChildren;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function feedChildrenNumber()
    {
        $this->setNbChildren($this->getChildren()->count());
    }
}
