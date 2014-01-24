<?php

namespace Display\CmsBundle\Entity;

use Display\CmsBundle\Util\Urlizer;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Media
 *
 * @ORM\Table(name="display_media")
 * @ORM\Entity(repositoryClass="Display\CmsBundle\Entity\MediaRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Media extends AbstractEntity
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
     * @var string $name
     * @ORM\Column(name="name", type="string", length=256, nullable=false)
     */
    private $name;

    /**
     * @var string $mime
     * @ORM\Column(name="mime", type="string", length=256, nullable=false)
     */
    private $mime;

    /**
     * @var string $path
     * @ORM\Column(name="path", type="string", length=256, nullable=false)
     */
    private $path;

    /**
     * @Assert\File(maxSize="64M")
     */
    private $file;

    private $temp;

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
     * @return Media
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
     * Set mime
     *
     * @param string $mime
     * @return Media
     */
    public function setMime($mime)
    {
        $this->mime = $mime;

        return $this;
    }

    /**
     * Get mime
     *
     * @return string
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Media
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->path)) {
            // store the old name to delete after the update
            $this->temp = $this->path;
            $this->path = null;
        } else {
            $this->path = 'initial';
        }
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return bool
     */
    public function isImage()
    {
        return (bool) preg_match('`^image/.+`', $this->getMime());
    }

    /**
     * @return string
     */
    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    /**
     * @return string
     */
    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    /**
     * @return string
     */
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    /**
     * @return string
     */
    protected function getUploadDir()
    {
        return 'uploads/medias';
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            //$filename = sha1(uniqid(mt_rand(), true));
            $filename = Urlizer::urlize($this->getName());
            $this->path = $filename.'.'.$this->getFile()->guessClientExtension();
            $this->mime = $this->getFile()->getMimeType();
            //file_put_contents('log.txt', $this->path . $this->mime.PHP_EOL, FILE_APPEND);
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $target = $this->getFile()->move($this->getUploadRootDir(), $this->path);
        //file_put_contents('log.txt', $target.PHP_EOL, FILE_APPEND);

        // check if we have an old image
        if (isset($this->temp) && $this->temp != $this->path) {
            // delete the old image
            @unlink($this->getUploadRootDir().'/'.$this->temp);
            //file_put_contents('log.txt', 'delete ' . $this->getUploadRootDir().'/'.$this->temp, FILE_APPEND);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove()
    {
        $this->temp = $this->getAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (isset($this->temp)) {
            unlink($this->temp);
        }
    }
}
