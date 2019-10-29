<?php

namespace App\Service\Constraint;

use App\Repository\ProductRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class OrderValidator extends MultipleParamsValidator
{
    /**
     * @var ProductRepository
     */
    private $productRepo;

    public function __construct(ValidatorInterface $validator, ProductRepository $repository)
    {
        parent::__construct($validator);

        $this->productRepo = $repository;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return [
            'product' => [
                'pattern' => '\d+',
                'allowNull' => false,
                'allowBlank' => false,
            ],

            'price' => [
                'pattern' => '\d+(?:\.\d{1,2})?',
                'allowNull' => false,
                'allowBlank' => false,
            ],

            'quantity' => [
                'pattern' => '\d+',
                'allowNull' => false,
                'allowBlank' => false,
            ]
        ];
    }

    public function validate($params, Constraint $constraint)
    {
        parent::validate($params, $constraint);

        $id = $params['product'];

        /**
         * @var $product \App\Entity\Product
         */
        if (!($product = $this->productRepo->getById($id))) {
            $violation = new ConstraintViolation(
                'Product not found',
                '',
                array(),
                $id,
                '',
                null,
                null
            );

            $this->context->getViolations()->add($violation);
        }

        $quantity = $params['quantity'];

        if ($quantity > $product->getStock()->getQuantity()) {
            $violation = new ConstraintViolation(
                'There is not enough quantity of product in stock',
                '',
                array(),
                $quantity,
                '',
                null,
                null
            );

            $this->context->getViolations()->add($violation);
        }
    }

}
