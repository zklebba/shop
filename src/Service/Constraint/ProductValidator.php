<?php

namespace App\Service\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class ProductValidator extends MultipleParamsValidator
{

    /**
     * @return array
     */
    public function getOptions()
    {
        return [
            'name' => [
                'pattern' => '.{0,30}',
                'allowNull' => false,
                'allowBlank' => false,
            ],

            'description' => [
                'pattern' => '.{0,3000}'
            ],

            'price' => [
                'pattern' => '\d+(?:\.\d{1,2})?',
            ],

            'picture' => [
                'pattern' => '(?:http|https):\/\/.*\.(?:jpg|jpeg|png|gif)',
            ],

            'category' => [
                'pattern' => '\d+',
            ],

            'quantity' => [
                'pattern' => '\d+',
            ]
        ];
    }

}
