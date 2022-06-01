<?php

namespace ManuelAguirre\Bundle\TranslationBundle\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use ManuelAguirre\Bundle\TranslationBundle\Model\TranslationLastEdit;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table("translation_bundle_translation")]
#[ORM\Entity(repositoryClass: TranslationRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(
    fields: ["code", "domain"],
    message: "The code is already in use for this domain",
)]
class Translation
{
    #[ORM\Column]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private ?int $id;

    #[ORM\Column]
    #[Assert\NotBlank]
    private string $code;

    #[ORM\Column]
    #[Assert\NotBlank]
    private string $domain = 'messages';

    #[ORM\Column]
    private bool $active = true;

    #[ORM\Column(name: "trans_values", type: "json", nullable: true)]
    private array $values;

    #[ORM\Column(name: "created_at", nullable: true, updatable: false)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: "updated_at", nullable: true)]
    private DateTimeImmutable $updatedAt;

    /**
     * Posibles valores: local, file
     * @todo Convertir a un enum
     */
    #[ORM\Column(
        name: "last_changed",
        type: 'string',
        length: 10,
        nullable: true,
        enumType: TranslationLastEdit::class,
    )]
    private TranslationLastEdit $lastChanged;

    /**
     * Indica si se ha establecido un valor para el atributo $lastChanged, para que los callback
     * de doctrine no reemplaze el valor de dicho atributo en ese caso.
     */
    private bool $updatedLastChanged = false;

    #[ORM\Column(nullable: true)]
    private string|null $hash;

    /**
     * Indica si se ha establecido un valor para el atributo $hash, para que los callback
     * de doctrine no reemplaze el valor de dicho atributo en ese caso.
     */
    private bool $updatedHash = false;

    public function __construct(?string $code = null, ?string $domain = null)
    {
        $code and $this->setCode($code);
        $domain and $this->setDomain($domain);
        $this->lastChanged = TranslationLastEdit::LOCAL;
    }

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function setCode(string $code): void
    {
        $this->code = trim($code);
    }

    public function getCode(): string
    {
        return trim($this->code);
    }

    public function setValue(string $locale, string $value): void
    {
        $this->values[$locale] = $value;
    }

    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    #[ORM\PrePersist]
    public function setCreatedValue(): void
    {
        $this->createdAt = new \DateTimeImmutable('now');
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable('now');
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt ??= new DateTimeImmutable();
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt ??= new DateTimeImmutable();
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): void
    {
        $this->hash = $hash;
        $this->updatedHash = true;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setHashValue(): void
    {
        if ($this->updatedHash) {
            //Si se hizo una actualización del hash, no lo actualizamos acá
            return;
        }

        $this->setHash(uniqid(md5(serialize($this->getValues()))));
    }

    public function getLastChanged(): TranslationLastEdit
    {
        return $this->lastChanged ??= TranslationLastEdit::LOCAL;
    }

    public function setLastChanged(TranslationLastEdit $lastChanged): void
    {
        $this->lastChanged = $lastChanged;
        $this->updatedLastChanged = true;
    }

    #[ORM\PrePersist, ORM\PreUpdate]
    public function setLastChangedOnSave(): void
    {
        if (!$this->updatedLastChanged) {
            $this->lastChanged = TranslationLastEdit::LOCAL;
        }
    }
}
