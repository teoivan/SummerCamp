<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $password = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $birth_day = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\Choice([0, 1, 2, 3])]
    private ?int $gender = null;

    /**
     * @var Collection<int, Workout>
     */
    #[ORM\OneToMany(targetEntity: Workout::class, mappedBy: 'type', orphanRemoval: true)]
    private Collection $workouts;

    public function __construct()
    {
        $this->workouts = new ArrayCollection();
    }

//    #[Assert\IsTrue(message: 'The password cannot match your name')]
//    public function isPasswordSafe(): bool
//    {
//        return $this->name !== $this->password;
//    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getBirthDay(): ?\DateTimeInterface
    {
        return $this->birth_day;
    }

    public function setBirthDay(\DateTimeInterface $birth_day): static
    {
        $this->birth_day = $birth_day;

        return $this;
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function setGender(int $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return Collection<int, Workout>
     */
    public function getWorkouts(): Collection
    {
        return $this->workouts;
    }

    public function addWorkout(Workout $workout): static
    {
        if (!$this->workouts->contains($workout)) {
            $this->workouts->add($workout);
            $workout->setType($this);
        }

        return $this;
    }

    public function removeWorkout(Workout $workout): static
    {
        if ($this->workouts->removeElement($workout)) {
            // set the owning side to null (unless already changed)
            if ($workout->getType() === $this) {
                $workout->setType(null);
            }
        }

        return $this;
    }
}
