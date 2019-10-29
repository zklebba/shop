<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(RegistryInterface $registry, ValidatorInterface $validator)
    {
        parent::__construct($registry, Customer::class);

        $this->validator = $validator;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function customerExist(string $email): bool
    {
        $result = $this->createQueryBuilder('c')
            ->select('c.id')
            ->where('c.email = :email')
            ->setMaxResults(1)
            ->setParameter(':email', $email)
            ->getQuery()
            ->getResult();

        return (boolean) count($result);
    }

    public function save(Customer $customer)
    {
        $errors = $this->validator->validate($customer);

        if (count($errors)) {
            throw new ValidatorException('Customer object is invalid');
        }

        $this->getEntityManager()->persist($customer);
        $this->getEntityManager()->flush();
    }
}
