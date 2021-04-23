<?php

namespace App\Normalizer;

abstract class AbstractNormalizer implements NormalizerInterface
{
    protected $exceptionTypes;

    public function __construct(array $exceptiontypes)
    {
        $this->exceptionTypes = $exceptiontypes;
    }

    public function supports(\Exception $exception)
    {
        return in_array(get_class($exception), $this->exceptionTypes);
    }
}