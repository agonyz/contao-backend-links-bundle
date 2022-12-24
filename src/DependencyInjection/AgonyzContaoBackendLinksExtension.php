<?php

declare(strict_types=1);

/*
 * This file is part of agonyz/contao-backend-links-bundle.
 *
 * (c) agonyz
 *
 * @license LGPL-3.0-or-later
 */

namespace Agonyz\ContaoBackendLinksBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class AgonyzContaoBackendLinksExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('listeners.yml');
    }
}
