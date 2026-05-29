<?php

namespace App\Entity;

use App\Repository\ContactMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactMessageRepository::class)]
class ContactMessage
{
    public const TYPE_CONTACT    = 'contact';
    public const TYPE_GENERAL    = 'general';
    public const TYPE_BOOKING    = 'booking';
    public const TYPE_PARTNER    = 'partner';
    public const TYPE_LEVEL      = 'level';
    public const TYPE_BUG        = 'bug';
    public const TYPE_SUGGESTION = 'suggestion';

    public const TYPES = [
        'Contact général'           => self::TYPE_CONTACT,
        'Commentaire général'       => self::TYPE_GENERAL,
        'Système de réservation'    => self::TYPE_BOOKING,
        'Recherche de partenaires'  => self::TYPE_PARTNER,
        'Système de niveaux'        => self::TYPE_LEVEL,
        'Rapport de bug'            => self::TYPE_BUG,
        "Suggestion d'amélioration" => self::TYPE_SUGGESTION,
    ];

    public const STATUS_NEW     = 'nouveau';
    public const STATUS_READ    = 'lu';
    public const STATUS_HANDLED = 'traite';

    public const STATUSES = [
        'Nouveau' => self::STATUS_NEW,
        'Lu'      => self::STATUS_READ,
        'Traité'  => self::STATUS_HANDLED,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'contactMessages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 200)]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 20, nullable: true, options: ['default' => self::TYPE_CONTACT])]
    private ?string $type = self::TYPE_CONTACT;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $rating = null;

    #[ORM\Column(length: 20, nullable: true, options: ['default' => self::STATUS_NEW])]
    private ?string $status = self::STATUS_NEW;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $reportedUser = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 3, scale: 1, nullable: true)]
    private ?string $reportedCurrentLevel = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 3, scale: 1, nullable: true)]
    private ?string $reportedSuggestedLevel = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $reportedPlayerName = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $reportedPlayerPhone = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getReportedUser(): ?User
    {
        return $this->reportedUser;
    }

    public function setReportedUser(?User $reportedUser): static
    {
        $this->reportedUser = $reportedUser;

        return $this;
    }

    public function getReportedCurrentLevel(): ?string
    {
        return $this->reportedCurrentLevel;
    }

    public function setReportedCurrentLevel(?string $reportedCurrentLevel): static
    {
        $this->reportedCurrentLevel = $reportedCurrentLevel;

        return $this;
    }

    public function getReportedSuggestedLevel(): ?string
    {
        return $this->reportedSuggestedLevel;
    }

    public function setReportedSuggestedLevel(?string $reportedSuggestedLevel): static
    {
        $this->reportedSuggestedLevel = $reportedSuggestedLevel;

        return $this;
    }

    public function getReportedPlayerName(): ?string
    {
        return $this->reportedPlayerName;
    }

    public function setReportedPlayerName(?string $reportedPlayerName): static
    {
        $this->reportedPlayerName = $reportedPlayerName;

        return $this;
    }

    public function getReportedPlayerPhone(): ?string
    {
        return $this->reportedPlayerPhone;
    }

    public function setReportedPlayerPhone(?string $reportedPlayerPhone): static
    {
        $this->reportedPlayerPhone = $reportedPlayerPhone;

        return $this;
    }

    public function getTypeLabel(): string
    {
        $label = array_search($this->type, self::TYPES, true);

        return $label !== false ? $label : (string) $this->type;
    }

    public function isFeedback(): bool
    {
        return $this->type !== null && $this->type !== self::TYPE_CONTACT;
    }

    public function isLevelReport(): bool
    {
        return $this->type === self::TYPE_LEVEL;
    }
}
