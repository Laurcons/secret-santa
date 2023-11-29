<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\RateLimiter\RateLimit;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function getRoles(): array
    {
        $arr = ["ROLE_PARTICIPANT"];
        if ($this->isAdmin())
            $arr[] = "ROLE_ADMIN";
        return $arr;
    }

    public function eraseCredentials()
    {
        // do not uncomment unless password upgrading is implemented
        // $this->setPasscode(null);
    }

    public function getUserIdentifier(): string
    {
        return $this->getNickname();
    }

    public function getPassword(): ?string
    {
        return $this->getPasscode();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nickname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $passcode = null;

    #[ORM\OneToOne(mappedBy: 'participant', cascade: ['persist', 'remove'])]
    private ?ParticipantInfo $participantInfo = null;

    #[ORM\OneToOne(mappedBy: 'gifter', cascade: ['persist', 'remove'], fetch: 'EAGER')]
    private ?Assignation $gifterAssignation = null;

    #[ORM\OneToOne(mappedBy: 'giftee', cascade: ['persist', 'remove'])]
    private ?Assignation $gifteeAssignation = null;

    #[ORM\Column(name: 'is_admin', options: ['default' => false])]
    private ?bool $admin = null;

    #[ORM\Column(length: 255, nullable: true, options: ['charset' => 'utf8mb4'])]
    private ?string $emoji = null;

    // set by App\Security\RateLimiterProtectionListener
    private ?RateLimit $limit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): static
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getPasscode(): ?string
    {
        return $this->passcode;
    }

    public function setPasscode(?string $passcode): static
    {
        $this->passcode = $passcode;

        return $this;
    }

    public function getParticipantInfo(): ?ParticipantInfo
    {
        return $this->participantInfo;
    }

    public function setParticipantInfo(ParticipantInfo $participantInfo): static
    {
        // set the owning side of the relation if necessary
        if ($participantInfo->getParticipant() !== $this) {
            $participantInfo->setParticipant($this);
        }

        $this->participantInfo = $participantInfo;

        return $this;
    }

    public function getGifterAssignation(): ?Assignation
    {
        return $this->gifterAssignation;
    }

    public function setGifterAssignation(Assignation $assignation): static
    {
        // set the owning side of the relation if necessary
        if ($assignation->getGifter() !== $this) {
            $assignation->setGifter($this);
        }

        $this->gifterAssignation = $assignation;

        return $this;
    }

    public function getGifteeAssignation(): ?Assignation
    {
        return $this->gifteeAssignation;
    }

    public function setGifteeAssignation(Assignation $gifted): static
    {
        // set the owning side of the relation if necessary
        if ($gifted->getGiftee() !== $this) {
            $gifted->setGiftee($this);
        }

        $this->gifteeAssignation = $gifted;

        return $this;
    }

    public function isAdmin(): ?bool
    {
        return $this->admin;
    }

    public function setIsAdmin(bool $isAdmin): static
    {
        $this->admin = $isAdmin;

        return $this;
    }

    public function getEmoji(): ?string
    {
        return $this->emoji;
    }

    public function setEmoji(?string $emoji): static
    {
        $this->emoji = $emoji;

        return $this;
    }

    public function getRateLimit(): ?RateLimit
    {
        return $this->limit;
    }

    public function setRateLimit(?RateLimit $limit): static
    {
        $this->limit = $limit;
        return $this;
    }
}
