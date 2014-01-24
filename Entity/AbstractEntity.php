<?php
namespace Display\CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AbstractEntity
 * @package Display\CmsBundle\Entity
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class AbstractEntity
{
    /**
     * @var \DateTime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var \DateTime $updatedAt
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    protected $updatedAt;

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function feedDate()
    {
        $now = new \DateTime();
        if (null === $this->getCreatedAt()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return AbstractEntity
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return AbstractEntity
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
