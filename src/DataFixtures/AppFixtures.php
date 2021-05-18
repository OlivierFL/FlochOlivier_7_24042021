<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\Company;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;
    /**
     * @var array
     */
    private array $brands = [];
    /**
     * @var array
     */
    private array $companies = [];

    /**
     * AppFixtures constructor.
     *
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(private UserPasswordEncoderInterface $encoder)
    {
        $this->faker = Factory::create('fr');
    }

    /**
     * @param ObjectManager $manager
     *
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $this->loadCompanies($manager);
        $this->loadUsers($manager);
        $this->loadBrands($manager);
        $this->loadProducts($manager);

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     *
     * @throws Exception
     */
    private function loadUsers(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; ++$i) {
            $user = new User();
            $user->setFirstName($this->faker->firstName());
            $user->setLastName($this->faker->lastName());
            $user->setEmail($this->faker->email());
            if (0 === $i) {
                $user->setCompany($this->companies[0]);
            } else {
                $user->setCompany($this->getRandomCompany());
            }
            $manager->persist($user);
        }
    }

    /**
     * @param ObjectManager $manager
     *
     * @throws Exception
     */
    private function loadCompanies(ObjectManager $manager): void
    {
        $company = new Company();
        $company->setName('Company');
        $company->setPassword($this->encoder->encodePassword($company, 'Company1234'));
        $company->setLogoUrl('apple.png');
        $company->setLogoAltText($company->getName());
        $this->companies[] = $company;

        $manager->persist($company);

        $company = new Company();
        $company->setName('Company Test');
        $company->setPassword($this->encoder->encodePassword($company, '1234Company'));
        $company->setLogoUrl('apple.png');
        $company->setLogoAltText($company->getName());
        $this->companies[] = $company;

        $manager->persist($company);
    }

    /**
     * @param ObjectManager $manager
     *
     * @throws Exception
     */
    private function loadProducts(ObjectManager $manager): void
    {
        for ($i = 0; $i < 50; ++$i) {
            $product = new Product();
            $product->setName($this->faker->company());
            $product->setDescription($this->getDescription($this->faker));
            $product->setPrice($this->faker->randomFloat(2, 50.00, 1500.00));
            $product->setCoverImgUrl('phone.jpeg');
            $product->setCoverImgAltText($this->faker->text(255));
            $product->setBrand($this->getRandomBrand());
            $manager->persist($product);
        }
    }

    /**
     * @param ObjectManager $manager
     *
     * @throws Exception
     */
    private function loadBrands(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; ++$i) {
            $brand = new Brand();
            $brand->setName($this->faker->company());
            $brand->setLogoUrl('apple.png');
            $brand->setLogoAltText($this->faker->text(255));
            $manager->persist($brand);
            $this->brands[] = $brand;
        }
    }

    /**
     * @param Generator $faker
     *
     * @throws Exception
     *
     * @return string
     */
    private function getDescription(Generator $faker): string
    {
        $description = [$faker->paragraph(), $faker->paragraphs(random_int(1, 5), true)];
        $key = array_rand($description);

        return $description[$key];
    }

    /**
     * @return Brand
     */
    private function getRandomBrand(): Brand
    {
        $key = array_rand($this->brands);

        return $this->brands[$key];
    }

    /**
     * @return Company
     */
    private function getRandomCompany(): Company
    {
        $key = array_rand($this->companies);

        return $this->companies[$key];
    }
}
