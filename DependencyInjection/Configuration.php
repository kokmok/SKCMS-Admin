<?php

namespace SKCMS\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('skcms_admin');

        $rootNode   ->children()
                    ->arrayNode('siteInfo')
                        ->children()
                            ->scalarNode('homeRoute')->end()
                            ->arrayNode('locales')
                                ->prototype('scalar')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('menuGroups')
                        ->useAttributeAsKey('alias')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('id')->end()
                                ->scalarNode('name')->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('modules')
                        ->children()
                            //Help
                            ->arrayNode('help')
                                ->children()
                                    ->booleanNode('enabled')->defaultValue(false)->end()
                                ->end()
                                ->addDefaultsIfNotSet()
                            ->end()
                            //Blog
                            ->arrayNode('blog')
                                ->children()
                                    ->booleanNode('enabled')->defaultValue(false)->end()
                                    ->arrayNode('categories')
                                        ->children()
                                            ->booleanNode('enabled')->defaultValue(true)->end()
                                        ->end()
                        //                                    ->addDefaultsIfNotSet()
                                    ->end()
                                ->end()
                            ->addDefaultsIfNotSet()
                            ->end() //ESHOP
                            ->arrayNode('eshop')
                                ->children()
                                    ->booleanNode('enabled')->defaultValue(false)->end()
                                ->end()
                            ->addDefaultsIfNotSet()
                            ->end()
                            //CONTACT
                            ->arrayNode('contact')
                                ->children()
                                    ->booleanNode('enabled')->defaultValue(false)->end()
                                    ->arrayNode('messageEntity')
                                        ->children()
                                            ->scalarNode('name')->defaultValue('ContactMessage')->end()
                                            ->scalarNode('beautyName')->defaultValue('Contact Message')->end()
                                            ->scalarNode('bundle')->defaultValue('SKCMSContact')->end()
                                            ->scalarNode('class')->defaultValue('\SKCMS\ContactBundle\Entity\ContactMessage')->end()
                                            ->scalarNode('form')->defaultValue('\SKCMS\ContactBundle\Form\ContactMessageType')->end()
                                            ->arrayNode('listProperties')
                                                ->useAttributeAsKey('alias')
                                                ->prototype('array')
                                                    ->children()
                                                        ->scalarNode('dataName')->end()
                                                        ->scalarNode('beautyName')->end()
                                                        ->scalarNode('type')->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->addDefaultsIfNotSet()
                            ->end()
                            //USER
                            ->arrayNode('user')
                                ->children()
                                    ->booleanNode('enabled')->defaultValue(false)->end()
                                    ->arrayNode('userEntity')
                                        ->children()
                                            ->scalarNode('name')->defaultValue('User')->end()
                                            ->scalarNode('beautyName')->defaultValue('User')->end()
                                            ->scalarNode('bundle')->defaultValue('SKCMSUser')->end()
                                            ->scalarNode('class')->defaultValue('\SKCMS\UserBundle\Entity\User')->end()
                                            ->scalarNode('form')->defaultValue('\SKCMS\UserBundle\Form\UserType')->end()
                                            ->arrayNode('listProperties')
                                                ->defaultValue([])
                                                ->useAttributeAsKey('alias')
                                                ->prototype('array')
                                                    ->children()
                                                        ->scalarNode('dataName')->end()
                                                        ->scalarNode('beautyName')->end()
                                                        ->scalarNode('type')->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->addDefaultsIfNotSet()
                            ->end()
                            //END USER
                        ->end()

                    ->end()
                    ->arrayNode('entities')
                        ->useAttributeAsKey('alias')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('beautyName')->end()
                                ->scalarNode('name')->end()
                                ->scalarNode('bundle')->end()
                                ->scalarNode('class')->end()
                                ->scalarNode('form')->end()
                                ->scalarNode('type')->defaultValue('crud')->end()
                                ->scalarNode('menuGroup')->end()
                                ->scalarNode('skcmsMenuList')->defaultValue(0)->end()
                                ->booleanNode('uniqueEntity')->defaultValue(false)->end()
                                ->arrayNode('listProperties')
                                    ->useAttributeAsKey('alias')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('dataName')->end()
                                            ->scalarNode('beautyName')->end()
                                            ->scalarNode('type')->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->scalarNode('sort')->defaultValue('none')->end()
                            ->end()
                        ->end()
                    ->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
