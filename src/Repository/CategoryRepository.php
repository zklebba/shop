<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(RegistryInterface $registry, ValidatorInterface $validator)
    {
        parent::__construct($registry, Category::class);

        $this->validator = $validator;
    }

    /**
     * @param Category $category
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Category $category)
    {
        $errors = $this->validator->validate($category);

        if (count($errors)) {
            throw new ValidatorException('Category object is invalid');
        }

        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush($category);
    }

    /**
     * @param $categories
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveList(&$categories)
    {
        foreach ($categories as &$category) {
            $errors = $this->validator->validate($category);

            if (count($errors)) {
                throw new ValidatorException('Category object is invalid');
            }

            $this->getEntityManager()->persist($category);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @param $id
     * @return Category|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     *
     * @return Category
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    /**
     * @return Category[]
     */
    public function getList()
    {
        return $this->findAll();
    }
}
