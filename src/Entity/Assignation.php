<?php

namespace App\Entity;

use App\Repository\AssignationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssignationRepository::class)]
class Assignation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'gifterAssignation', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participant $gifter = null;

    #[ORM\OneToOne(inversedBy: 'gifteeAssignation', cascade: ['persist', 'remove'], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participant $giftee = null;

    #[ORM\Column(length: 4096, nullable: true)]
    private ?string $presentStatus = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGifter(): ?Participant
    {
        return $this->gifter;
    }

    public function setGifter(Participant $gifter): static
    {
        $this->gifter = $gifter;

        return $this;
    }

    public function getGiftee(): ?Participant
    {
        return $this->giftee;
    }

    public function setGiftee(Participant $giftee): static
    {
        $this->giftee = $giftee;

        return $this;
    }

    public function getPresentStatus(): ?string
    {
        return $this->presentStatus;
    }

    public function setPresentStatus(?string $presentStatus): static
    {
        $this->presentStatus = $presentStatus;

        return $this;
    }
}
