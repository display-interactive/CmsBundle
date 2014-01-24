<?php

namespace Display\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Fragment
 *
 * @ORM\Table(name="display_fragment")
 * @ORM\Entity(repositoryClass="Display\CmsBundle\Entity\FragmentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Fragment extends AbstractEntity
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
     *
     * @var string $name
     * @ORM\Column(name="name", type="string", length=256, nullable=false)
     */
    private $name;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Fragment
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * @return string
     */
    public function __toString()
    {
        return $this->getContent();
    }
}
