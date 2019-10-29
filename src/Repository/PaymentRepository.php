<?php

namespace App\Repository;

use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(RegistryInterface $registry, ValidatorInterface $validator)
    {
        parent::__construct($registry, Payment::class);

        $this->validator = $validator;
    }

    public function save(Payment $payment)
    {
        $errors = $this->validator->validate($payment);

        if (count($errors)) {
            throw new ValidatorException('Payment object is invalid');
        }

        $this->getEntityManager()->persist($payment);
        $this->getEntityManager()->flush($payment);
    }

    public function update(Payment $payment)
    {
        $this->getEntityManager()->flush($payment);
    }

    /**
     * @param $id
     * @return Payment|null
     */
    public function getById($id)
    {
        return $this->find($id);
    }
}
