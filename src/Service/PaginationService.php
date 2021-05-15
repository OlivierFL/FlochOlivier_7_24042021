<?php

namespace App\Service;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class PaginationService
{
    public function __construct(
        private PaginatorInterface $paginator,
        private NormalizationService $normalization
    ) {
    }

    /**
     * @param array $data
     * @param int   $page
     * @param int   $limit
     *
     * @throws ExceptionInterface
     *
     * @return array
     */
    public function getProductsPaginated(array $data, int $page, int $limit): array
    {
        $dataPaginated = $this->paginator->paginate(
            $data,
            $page,
            $limit
        );

        $dataPaginated = $this->normalization->normalize($dataPaginated);

        $total = \count($data);

        return $this->formatResult($dataPaginated, $total, $page, $limit);
    }

    /**
     * @param array $data
     * @param int   $total
     * @param int   $page
     * @param int   $limit
     *
     * @return array
     */
    private function formatResult(array $data, int $total, int $page, int $limit): array
    {
        $result = [];
        $result['page'] = $page;
        $result['limit'] = $limit;
        $result['total'] = $total;
        $result['data'] = $data;

        return $result;
    }
}
