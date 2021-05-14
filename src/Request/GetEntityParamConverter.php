<?php

namespace App\Request;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class GetEntityParamConverter implements ParamConverterInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $entity = $this->entityManager->getRepository($configuration->getClass())->find($request->attributes->get('id'));

        $request->attributes->set($configuration->getName(), $entity);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(ParamConverter $configuration): bool
    {
        $entity = strtolower((str_replace('App\Entity\\', '', $configuration->getClass())));

        return $configuration->getName() === $entity;
    }
}
