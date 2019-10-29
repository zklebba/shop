<?php

namespace App\Service\Constraint;

use FOS\RestBundle\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class MultipleParamsValidator extends ConstraintValidator
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return array
     */
    abstract function getOptions();

    public function validate($params, Constraint $constraint)
    {
        if (null === $params || '' === $params || !is_array($params)) {
            return;
        }

        $allOptions = $this->getOptions();

        foreach ($allOptions as $name => $options) {
            $constraints = [];

            $value = $params[$name] ?? null;

            if (isset($options['pattern'])) {
                $constraints[] = $this->getRegexConstraint($name, $options['pattern']);
            }

            if (isset($options['allowNull']) && $options['allowNull'] === false) {
                $constraints[] = new NotNull();
            }

            if (isset($options['allowBlank']) && $options['allowBlank'] === false) {
                $constraints[] = new NotBlank();
            }

            try {
                $errors = $this->validator->validate($value, $constraints);

                if (count($errors)) {
                    $this->context->getViolations()->addAll($errors);
                }
            } catch (ValidatorException $e) {
                $violation = new ConstraintViolation(
                    $e->getMessage(),
                    $e->getMessage(),
                    array(),
                    $value,
                    '',
                    null,
                    null,
                    $e->getCode()
                );

                $this->context->getViolations()->add($violation);
            }
        }
    }

    protected function getRegexConstraint($filedName, $pattern)
    {
        return new Regex([
            'pattern' => '#^(?:'.$pattern.')$#xsu',
            'message' => sprintf(
                'Parameter \'%s\' value, does not match pattern \'%s\'',
                $filedName,
                $pattern
            )
        ]);
    }

}
