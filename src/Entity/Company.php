<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Rollerworks\Component\PasswordStrength\Validator\Constraints as RollerworksPassword;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\EntityListeners({"App\Doctrine\CompanyListener"})
 * @ORM\Entity(repositoryClass=CompanyRepository::class)
 * @UniqueEntity("name")
 * @Hateoas\Relation(
 *     "users_list",
 *     href=@Hateoas\Route(
 *         "api_users_list",
 *         absolute=true
 *     )
 * )
 * @Hateoas\Relation(
 *     "user_create",
 *     href=@Hateoas\Route(
 *         "api_user_create",
 *         absolute=true
 *     )
 * )
 */
class Company implements UserInterface
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 255
    )]
    private $name;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @RollerworksPassword\PasswordStrength(minLength=6, minStrength=3)
     * @RollerworksPassword\PasswordRequirements(requireNumbers=true, requireCaseDiff=true)
     */
    #[Assert\NotBlank]
    #[Assert\NotCompromisedPassword]
    #[Assert\Length(
        max: 255
    )]
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Assert\NotBlank]
    private $logoUrl;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 255
    )]
    private $logoAltText;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="company", orphanRemoval=true)
     * @Serializer\Exclude
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->name;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    public function setLogoUrl(?string $logoUrl): self
    {
        $this->logoUrl = $logoUrl;

        return $this;
    }

    public function getLogoAltText(): ?string
    {
        return $this->logoAltText;
    }

    public function setLogoAltText(?string $logoAltText): self
    {
        $this->logoAltText = $logoAltText;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setCompany($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        // set the owning side to null (unless already changed)
        if ($this->users->removeElement($user) && $user->getCompany() === $this) {
            $user->setCompany(null);
        }

        return $this;
    }
}
