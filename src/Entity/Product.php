<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\EntityListeners({"App\Doctrine\AbsoluteUrlListener"})
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @Hateoas\Relation(
 *     "self",
 *     href=@Hateoas\Route(
 *         "api_product_detail",
 *         parameters={"id": "expr(object.getId())"},
 *         absolute=true
 *     )
 * )
 * @Hateoas\Relation(
 *     "brand",
 *     embedded=@Hateoas\Embedded("expr(object.getBrand())")
 * )
 */
class Product
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
    private $name;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $coverImgUrl;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $coverImgAltText;

    /**
     * @ORM\ManyToOne(targetEntity=Brand::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Exclude
     */
    private $brand;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCoverImgUrl(): ?string
    {
        return $this->coverImgUrl;
    }

    public function setCoverImgUrl(string $coverImgUrl): self
    {
        $this->coverImgUrl = $coverImgUrl;

        return $this;
    }

    public function getCoverImgAltText(): ?string
    {
        return $this->coverImgAltText;
    }

    public function setCoverImgAltText(string $coverImgAltText): self
    {
        $this->coverImgAltText = $coverImgAltText;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }
}
