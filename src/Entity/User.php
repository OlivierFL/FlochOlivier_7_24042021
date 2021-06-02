<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\EntityListeners({"App\Doctrine\UserSetCompanyListener"})
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email")
 * @Hateoas\Relation(
 *     "self",
 *     href=@Hateoas\Route(
 *         "api_user_detail",
 *         parameters={
 *             "id": "expr(object.getId())"
 *         },
 *         absolute=true
 *     )
 * )
 * @Hateoas\Relation(
 *     "delete",
 *     href=@Hateoas\Route(
 *         "api_user_delete",
 *         parameters={
 *             "id": "expr(object.getId())"
 *         },
 *         absolute=true
 *     )
 * )
 * @Hateoas\Relation(
 *     "company",
 *     embedded=@Hateoas\Embedded("expr(object.getCompany())")
 * )
 */
class User
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 255
    )]
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank]
    #[Assert\Length(
        max: 255
    )]
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(
        max: 255
    )]
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Exclude
     */
    private $company;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

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

    public function getCompany(): ?UserInterface
    {
        return $this->company;
    }

    public function setCompany(?UserInterface $company): self
    {
        $this->company = $company;

        return $this;
    }
}
