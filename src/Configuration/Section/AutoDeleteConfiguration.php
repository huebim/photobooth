<?php

namespace Photobooth\Configuration\Section;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

final class AutoDeleteConfiguration
{
    public static function getNode(): NodeDefinition
    {
        return (new TreeBuilder('auto_delete'))->getRootNode()->addDefaultsIfNotSet()
            ->ignoreExtraKeys()
            ->children()
                ->booleanNode('enabled')->defaultValue(false)->end()
                ->integerNode('ttl_minutes')
                    ->defaultValue(45)
                    ->min(0)
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value): int { return intval($value); })
                    ->end()
                ->end()
                ->arrayNode('excluded_files')
                    ->prototype('scalar')->end()
                    ->defaultValue([])
                ->end()
            ->end();
    }
}
