<?php
namespace Hoathis\Bundle\XylBundle\Tests\Units\DependencyInjection;

use atoum;
use Hoathis\Bundle\XylBundle\DependencyInjection\Configuration as TestedClass;
use mock\Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration extends atoum
{
    public function testGetConfigTreeBuilder()
    {
        $this
            ->if($treeBuilder = new TreeBuilder())
            ->and($configuration = new TestedClass())
            ->then
                ->object($configuration->getConfigTreeBuilder($treeBuilder))->isInstanceOf('\\Symfony\\Component\\Config\\Definition\\Builder\\TreeBuilder')
                ->mock($treeBuilder)
                    ->call('root')->withArguments('xyl')->once()
        ;
    }
}