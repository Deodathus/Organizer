<?php

declare(strict_types=1);

namespace Framework\Request;

use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

final class RequestParamConvertor implements ParamConverterInterface
{
    #[NoReturn]
    public function apply(Request $request, ParamConverter $configuration): void
    {
        $class = $configuration->getClass();
        $request->attributes->set($configuration->getName(), call_user_func([$class, 'fromRequest'], $request));
    }

    #[Pure]
    public function supports(ParamConverter $configuration): bool
    {
        if (($class = $configuration->getClass()) !== null) {
            return method_exists($class, 'fromRequest');
        }

        return false;
    }
}
