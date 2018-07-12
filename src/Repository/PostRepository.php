<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;

class PostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function orderPostsByDescOrder()
    {
        $queryBuilder = $this->createQueryBuilder('p')
                            ->orderBy('p.id','DESC');

        return $adapter = new DoctrineORMAdapter($queryBuilder);
    }

    public function search($term)
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->andWhere('p.title LIKE :searchTerm' )
            ->setParameter('searchTerm', '%'.$term.'%');

        return $adapter = new DoctrineORMAdapter($queryBuilder);

    }
}