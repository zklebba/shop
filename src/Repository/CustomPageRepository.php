<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\CustomPage;
use App\Entity\ProductDetails;
use App\Entity\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @method CustomPage|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomPage|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomPage[]    findAll()
 * @method CustomPage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomPageRepository extends ServiceEntityRepository
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(RegistryInterface $registry, ValidatorInterface $validator)
    {
        parent::__construct($registry, CustomPage::class);
        $this->validator = $validator;
    }

    /**
     * @param CustomPage $page
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(CustomPage $page)
    {
        $errors = $this->validator->validate($page);

        if (count($errors)) {
            throw new ValidatorException('Custom Page object is invalid');
        }

        $this->getEntityManager()->persist($page);
        $this->getEntityManager()->flush($page);
    }

    public function getPagesList()
    {
        $builder = $this->createQueryBuilder('customPage')
            ->select([
                'customPage.id',
                'customPage.name',
            ]);

        return $builder->getQuery()->getScalarResult();
    }
}
