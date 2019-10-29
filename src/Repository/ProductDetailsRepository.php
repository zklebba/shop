<?php

namespace App\Repository;

use App\Entity\ProductDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProductDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductDetails[]    findAll()
 * @method ProductDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductDetailsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProductDetails::class);
    }
}
