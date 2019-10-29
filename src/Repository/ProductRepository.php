<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\ProductDetails;
use App\Entity\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(RegistryInterface $registry, ValidatorInterface $validator)
    {
        parent::__construct($registry, Product::class);

        $this->validator = $validator;
    }

    /**
     * @param Product $product
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Product $product)
    {
        $errors = $this->validator->validate($product);

        if (count($errors)) {
            throw new ValidatorException('Product object is invalid');
        }

        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush($product);
    }

    /**
     * @param $products
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveList(&$products)
    {
        foreach ($products as &$product) {
            $errors = $this->validator->validate($product);

            if (count($errors)) {
                throw new ValidatorException('Product object is invalid');
            }

            $this->getEntityManager()->persist($product);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @param $id
     * @return object|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     *
     * @return Product
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    /**
     * @param int $categoryId
     * @return array
     */
    public function getProductsTeasersList($categoryId = null)
    {
        $builder = $this->createQueryBuilder('product')
            ->select([
                'product.id',
                'product.name',
                'product.description',
                'product.price',
                'product.picture',
                'stock.quantity',
                'details.title',
                'category',
            ])
            ->leftJoin(Stock::class, 'stock', Join::WITH, 'product.stock = stock.id')
            ->leftJoin(ProductDetails::class, 'details', Join::WITH, 'product.details = details.id')
            ->leftJoin(Category::class, 'category', Join::WITH, 'product.category = category.id');

        if ($categoryId) {
            $builder
                ->where('category.id = :categoryId')
                ->setParameter(':categoryId', $categoryId);
        }

        return $builder->getQuery()->getScalarResult();
    }

    /**
     * @param array $ids
     * @return Product[]
     */
    public function getListByIds($ids = [])
    {
        $expr = $this->getEntityManager()->getExpressionBuilder();
        $in = $expr->in('product.id', $ids);

        $builder = $this->getEntityManager()->createQueryBuilder()
            ->select('product')
            ->from(Product::class, 'product')
            ->where($in);

        return $builder->getQuery()->getResult();
    }
}
