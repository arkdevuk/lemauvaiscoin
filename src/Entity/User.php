<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(operations: [
    new Get(),
    new Put(security: 'is_granted(\'ROLE_ADMIN\') or object == user'),
    new GetCollection(security: 'is_granted(\'ROLE_ADMIN\')'),
    new Post(security: 'is_granted(\'ROLE_ADMIN\')'),
])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(readable: true, writable: false)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    #[ApiProperty(readable: false, writable: false)]
    private ?string $salt;

    #[ORM\Column(length: 255)]
    #[ApiProperty(readable: false, writable: false)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[ApiProperty(security: 'is_granted(\'ROLE_ADMIN\') or object == user')]
    private ?string $email = null;

    #[ORM\Column]
    #[ApiProperty(security: 'is_granted(\'ROLE_ADMIN\') or object == user')]
    private array $roles;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Annonce::class)]
    #[ApiProperty(readable: false, writable: false)]
    private Collection $annonces;

    #[ORM\Column]
    private ?bool $premium = null;

    public function __construct()
    {
        $this->salt = crc32(uniqid('', true));
        $this->roles = ['ROLE_USER'];
        $this->annonces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection<int, Annonce>
     */
    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    public function addAnnonce(Annonce $annonce): self
    {
        if (!$this->annonces->contains($annonce)) {
            $this->annonces->add($annonce);
            $annonce->setOwner($this);
        }

        return $this;
    }

    public function removeAnnonce(Annonce $annonce): self
    {
        if ($this->annonces->removeElement($annonce)) {
            // set the owning side to null (unless already changed)
            if ($annonce->getOwner() === $this) {
                $annonce->setOwner(null);
            }
        }

        return $this;
    }

    public function isPremium(): ?bool
    {
        return $this->premium;
    }

    public function setPremium(bool $premium): self
    {
        $this->premium = $premium;

        return $this;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    #[ApiProperty(security: 'is_granted(\'ROLE_ADMIN\') or object == user')]
    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
