<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(RegistryInterface $registry, ValidatorInterface $validator)
    {
        parent::__construct($registry, Order::class);
        $this->validator = $validator;
    }

    public function getList($email)
    {
        return $this->createQueryBuilder('o')
            ->leftJoin(Customer::class, 'customer', Join::WITH, 'o.customer = customer.id')
            ->orderBy('o.orderDate', 'DESC')
            ->where('customer.email = :email')
            ->setParameter(':email', $email)
            ->getQuery()
            ->getResult();
    }

    public function createOrder(Order $order)
    {
        $errors = $this->validator->validate($order);

        if (count($errors)) {
            throw new ValidatorException('Order object is invalid');
        }

        foreach ($order->getOrderDetails() as $detail) {
            /**
             * @var OrderDetails $detail
             */

            $stock = $detail->getProduct()->getStock();
            $stock->setQuantity($stock->getQuantity() - $detail->getQuantity());

            $this->getEntityManager()->persist($detail);
        }

        $this->getEntityManager()->persist($order);
        $this->getEntityManager()->flush();
    }

    public function startOrderTransaction()
    {
        $this->getEntityManager()->getConnection()->beginTransaction();
    }

    public function rollbackOrderTransaction()
    {
        $this->getEntityManager()->getConnection()->rollBack();
    }

    public function commitOrderTransaction()
    {
        $this->getEntityManager()->getConnection()->commit();
    }

    /**
     * @return string
     * @throws
     */
    public function genUUID()
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addScalarResult('uuid', 'uuid');

        $query = $this->getEntityManager()->createNativeQuery('SELECT UUID() as uuid', $rsm);
        $result = $query->getSingleResult();

        return $result['uuid'];
    }

    /**
     * @param Order $order
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(Order $order)
    {
        $this->getEntityManager()->flush($order);
    }
}
