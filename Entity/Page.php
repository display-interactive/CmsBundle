<?php

namespace Display\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Page
 *
 * @Assert\Callback(methods={"isHomepage"})
 * @Assert\Callback(methods={"hasMatchingLocale"})
 * @Assert\Callback(methods={"hasWrongSlug"})
 *
 * @ORM\Table(name="display_page", indexes={@ORM\Index(name="slug_index", columns={"slug"})})
 * @ORM\Entity(repositoryClass="Display\CmsBundle\Entity\PageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Page extends AbstractEntity
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
     * @Assert\NotBlank()
     * @Assert\Locale()
     *
     * @var string $locale
     * @ORM\Column(name="locale", type="string", length=8, nullable=false)
     */
    private $locale;

    /**
     * @var string $status
     * @ORM\Column(name="status", type="string", length=16, nullable=false)
     */
    private $status;

    /**
     * @var Page $parent
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @var Page $children
     * @ORM\OneToMany(targetEntity="Page", mappedBy="parent", cascade={"persist"})
     */
    private $children;

    /**
     * @var string $slug
     * @ORM\Column(name="slug", type="string", length=128, nullable=true)
     */
    private $slug;

    /**
     * @Assert\NotBlank()
     *
     * @var string $title
     * @ORM\Column(name="title", type="string", length=256, nullable=false)
     */
    private $title;

    /**
     * @var string $headerTitle
     * @ORM\Column(name="header_title", type="string", length=256, nullable=true)
     */
    private $headerTitle;

    /**
     * @var string $description
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @Assert\NotBlank()
     *
     * @var string $content
     * @ORM\Column(name="content", type="text", nullable=false)
     */
    private $content;

    /**
     * @var boolean $cacheable
     * @ORM\Column(name="cacheable", type="boolean")
     */
    private $cacheable;

    /**
     * @var boolean $cachedAt
     * @ORM\Column(name="cached_at", type="datetime", nullable=false)
     */
    private $cachedAt;

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
     * Set locale
     *
     * @param string $locale
     * @return Page
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get lang
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Page
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return Page
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set parent
     *
     * @param Page $parent
     * @return Page
     */
    public function setParent(Page $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Page
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param Page $children
     * @return Page
     */
    public function addChild(Page $children)
    {
        $this->children[] = $children;
        $children->setParent($this);

        return $this;
    }

    /**
     * Remove children
     *
     * @param Page $children
     */
    public function removeChild(Page $children)
    {
        $this->children->removeElement($children);
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
     * Set slug
     *
     * @param string $slug
     * @return Page
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $urlParts = array($this->getSlug());
        $parent = $this->getParent();
        while (null !== $parent) {
            $urlParts[] = $parent->getSlug();
            $parent = $parent->getParent();
        }

        return implode('/', array_reverse(array_filter($urlParts)));
    }

    /**
     * Get breadcrumb
     *
     * @param Page $entity
     * @return array
     */
    public function getBreadcrumb(Page $entity = null)
    {
        $breadcrumb = array();
        $parent = (null === $entity) ? $this->getParent() :  $entity->getParent();

        if (null !== $parent) {
            $breadcrumb = $this->getBreadcrumb($parent);
            $breadcrumb[] = array(
                'id' => $parent->getId(),
                'title' => $parent->getTitle()
            );
        }

        if (null === $entity) {
            $breadcrumb[] = array(
                'id' => $this->getId(),
                'title' => $this->getTitle()
            );
        }

        return $breadcrumb;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Page
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set header title
     *
     * @param string $headerTitle
     * @return Page
     */
    public function setHeaderTitle($headerTitle)
    {
        $this->headerTitle = $headerTitle;

        return $this;
    }

    /**
     * Get header title
     *
     * @return string
     */
    public function getHeaderTitle()
    {
        return $this->headerTitle;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Page
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Page
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set cacheable
     *
     * @param string $cacheable
     * @return Page
     */
    public function setCacheable($cacheable)
    {
        $this->cacheable = $cacheable;

        return $this;
    }

    /**
     * Get cacheable
     *
     * @return bool
     */
    public function getCacheable()
    {
        return $this->cacheable;
    }

    /**
     * Set cached at
     *
     * @param \DateTime $cachedAt
     * @return AbstractEntity
     */
    public function setCachedAt($cachedAt)
    {
        $this->cachedAt = $cachedAt;

        return $this;
    }

    /**
     * Get cached at
     *
     * @return \DateTime
     */
    public function getCachedAt()
    {
        return $this->cachedAt;
    }

    /**
     * @param ExecutionContextInterface $context
     */
    public function isHomepage(ExecutionContextInterface $context)
    {
        if (null === $this->getSlug() && null !== $this->getParent()) {
            $context->addViolation('You have to specify a slug if parent is not empty.', array(), null);
        }

        if (null !== $this->getSlug() && null === $this->getParent()) {
            $context->addViolation('You have to specify a parent if slug is not empty.', array(), null);
        }
    }

    /**
     * @param ExecutionContextInterface $context
     */
    public function hasMatchingLocale(ExecutionContextInterface $context)
    {
        if (null !== $this->getParent() && $this->getLocale() !== $this->getParent()->getLocale()) {
            $context->addViolation('Parent locale and page locale should match.', array(), null);
        }
    }

    /**
     * @param ExecutionContextInterface $context
     */
    public function hasWrongSlug(ExecutionContextInterface $context)
    {
        $locales = Intl::getLocaleBundle()->getLocaleNames();
        if (isset($locales[$this->getSlug()])) {
            $context->addViolation('The slug can\'t be equal to a locale code: http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes.', array(), null);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle() . ' - ' . $this->getLocale();
    }
}
