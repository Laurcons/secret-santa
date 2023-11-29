<?php

namespace App\Entity;

use App\Repository\ParticipantInfoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipantInfoRepository::class)]
class ParticipantInfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'participantInfo', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participant $participant = null;

    #[ORM\Column(length: 4096, nullable: true)]
    private ?string $wishlist = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParticipant(): ?Participant
    {
        return $this->participant;
    }

    public function setParticipant(Participant $participant): static
    {
        $this->participant = $participant;

        return $this;
    }

    public function getWishlist(): ?string
    {
        return $this->wishlist;
    }

    public function setWishlist(?string $wishlist): static
    {
        $this->wishlist = $wishlist;

        return $this;
    }
}
