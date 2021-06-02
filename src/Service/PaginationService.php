<?php

namespace App\Service;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

class PaginationService
{
    public function __construct(
        private int $limit
    ) {
    }

    /**
     * @param Request                          $request
     * @param ServiceEntityRepositoryInterface $repository
     * @param array                            $criteria
     *
     * @return array
     */
    public function getDataPaginated(Request $request, ServiceEntityRepositoryInterface $repository, array $criteria): array
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = $request->query->getInt('limit', $this->limit);
        $orderBy = $request->query->get('sort') ?? 'id';
        $direction = $request->query->get('direction') ?? 'asc';
        $offset = ($page - 1) * $limit;

        $total = $repository->count($criteria);
        $data = $repository->findBy($criteria, [$orderBy => $direction], $limit, $offset);

        return compact('data', 'page', 'limit', 'total');
    }
}
