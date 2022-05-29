<?php

namespace ManuelAguirre\Bundle\TranslationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranslationValue
 *
 * @ORM\Table(name="translation_bundle_translation_value")
 * @ORM\Entity(repositoryClass="ManuelAguirre\Bundle\TranslationBundle\Entity\TranslationValueRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class TranslationValue
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
     * @ORM\Column(name="locale", type="string", length=10)
     */
    private $locale;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", nullable=true)
     */
    private $value;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @var Translation
     * @ORM\ManyToOne(targetEntity="ManuelAguirre\Bundle\TranslationBundle\Entity\Translation", inversedBy="values")
     */
    private $translation;

    function __construct($locale = null, $value = null)
    {
        $this->locale = $locale;
        $this->value = $value;
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
     *
     * @return TranslationValue
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return TranslationValue
     */
    public function setValue($value)
    {
        $this->value = trim($value);

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return TranslationValue
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return TranslationValue
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set translation
     *
     * @param \ManuelAguirre\Bundle\TranslationBundle\Entity\Translation $translation
     *
     * @return TranslationValue
     */
    public function setTranslation(\ManuelAguirre\Bundle\TranslationBundle\Entity\Translation $translation = null)
    {
        $this->translation = $translation;

        return $this;
    }

    /**
     * Get translation
     *
     * @return \ManuelAguirre\Bundle\TranslationBundle\Entity\Translation
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setPersistValues()
    {
        if (null == $this->getValue() and $this->translation) {
            $this->setValue($this->getTranslation()->getCode());
        }

        $this->setCreated(new \DateTime());
    }

    /**
     * @ORM\PreUpdate()
     */
    public function setUpdateValues()
    {
        $this->setUpdated(new \DateTime());
    }
}
