<?php

namespace ManuelAguirre\Bundle\TranslationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Translation
 *
 * @ORM\Table(name="translation_bundle_translation")
 * @ORM\Entity(repositoryClass="ManuelAguirre\Bundle\TranslationBundle\Entity\TranslationRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *  fields={"code", "domain"},
 *  message="The code is already in use for this domain"
 * )
 */
class Translation implements \Serializable
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
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $domain = 'messages';

    /**
     * @var boolean
     *
     * @ORM\Column(name="new", type="boolean")
     */
    private $new;

    /**
     * @var boolean
     *
     * @ORM\Column(name="autogenerated", type="boolean")
     */
    private $autogenerated;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = true;

    /**
     * @ORM\Column(name="trans_values", type="array", nullable=true)
     */
    private $values;

    /**
     * @ORM\Column(name="files", type="array", nullable=true)
     */
    private $files = array();

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * Posibles valores: local, file
     * @ORM\Column(name="last_changed", type="string", nullable=true)
     */
    private $lastChanged = 'local';
    /**
     * Indica si se ha establecido un valor para el atributo $lastChanged, para que los callback
     * de doctrine no reemplaze el valor de dicho atributo en ese caso.
     * @var bool
     */
    private $updatedLastChanged = false;

    /**
     * @ORM\Column(name="hash", type="string", nullable=true)
     */
    private $hash;
    /**
     * Indica si se ha establecido un valor para el atributo $hash, para que los callback
     * de doctrine no reemplaze el valor de dicho atributo en ese caso.
     * @var bool
     */
    private $updatedHash = false;

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
     * Set active
     *
     * @param boolean $active
     *
     * @return Translation
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Translation
     */
    public function setCode($code)
    {
        $this->code = trim($code);

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return trim($this->code);
    }

    /**
     * Set new
     *
     * @param boolean $new
     *
     * @return Translation
     */
    public function setNew($new)
    {
        $this->new = $new;

        return $this;
    }

    /**
     * Get new
     *
     * @return boolean
     */
    public function getNew()
    {
        return $this->new;
    }

    /**
     * Set autogenerated
     *
     * @param boolean $autogenerated
     *
     * @return Translation
     */
    public function setAutogenerated($autogenerated)
    {
        $this->autogenerated = $autogenerated;

        return $this;
    }

    /**
     * Get autogenerated
     *
     * @return boolean
     */
    public function getAutogenerated()
    {
        return $this->autogenerated;
    }

    /**
     * Constructor
     */
    public function __construct($code = null, $domain = null)
    {
        $this->setCode($code);
        $this->setDomain($domain);
    }

    public function setValue($locale, $value)
    {
        $this->values[$locale] = $value;

        return $this;
    }

    /**
     * Set domain
     *
     * @param string $domain
     *
     * @return Translation
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set files
     *
     * @param array $files
     *
     * @return Translation
     */
    public function setFiles($files)
    {
        $this->files = $files;

        return $this;
    }

    /**
     * Get files
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Set values
     *
     * @param array $values
     *
     * @return Translation
     */
    public function setValues($values)
    {
        $this->values = $values;

        return $this;
    }

    /**
     * Get values
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     *
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize(array(
            $this->code,
            $this->domain,
            $this->new,
            $this->autogenerated,
            $this->active,
            $this->values,
            $this->files,
        ));
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     *
     * @link http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     *
     * @return void
     */
    public function unserialize($serialized)
    {
        list($this->code,
            $this->domain,
            $this->new,
            $this->autogenerated,
            $this->active,
            $this->values,
            $this->files,)
            = unserialize($serialized);
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->createdAt = new \DateTime('now');
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate()
     */
    public function setUpdatedValue()
    {
        $this->updatedAt = new \DateTime('now');
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
        $this->updatedHash = true;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function setHashValue()
    {
        if ($this->updatedHash) {
            //Si se hizo una actualización del hash, no lo actualizamos acá
            return;
        }

        $this->setHash(uniqid(md5(serialize($this->getValues()))));
    }

    /**
     * @return mixed
     */
    public function getLastChanged()
    {
        return $this->lastChanged ?: 'local';
    }

    /**
     * @param mixed $lastChanged
     */
    public function setLastChanged($lastChanged)
    {
        if ($lastChanged == 'file') {
            $this->lastChanged = 'file';
        } else {
            $this->lastChanged = 'local';
        }

        $this->updatedLastChanged = true;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function setLastChangedOnSave()
    {
        if (!$this->updatedLastChanged) {
            $this->lastChanged = 'local';
        }
    }
}
