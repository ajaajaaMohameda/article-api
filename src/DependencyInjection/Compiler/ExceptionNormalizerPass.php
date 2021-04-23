<?php
namespace App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ExceptionNormalizerPass implements CompilerPassInterface
{
    public function process(\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        $exceptionlistenerDefinition = $container->findDefinition('App\EventSubscriber\ExceptionListener');

        
        $normalizers = $container->findTaggedServiceIds('app.normalizer');

        foreach($normalizers as $id => $tags) {
            $exceptionlistenerDefinition->addMethodCall('addNormalizer', [new Reference($id)]);
        }
    }
}