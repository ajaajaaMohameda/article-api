<?php

namespace App\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Entity\Article;
abstract class AbstractRepository extends ServiceEntityRepository
{
    private $paginatorInterface;

    public function __construct(PaginatorInterface  $paginator, ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
        $this->paginator = $paginator;
    }
    protected function paginate(Query $query, $limit = 20, $offset = 0)
    {
        if (0 == $limit || 0 == $offset) {
            throw new \LogicException('$limit & $offstet must be greater than 0.');
        }

        $currentPage = ceil(($offset + 1) / $limit);
        
        return $this->paginator->paginate($query, $currentPage , $limit);
    }
}