<?php
namespace Hoathis\Bundle\XylBundle\Tests\Units\Loader;

use atoum;
use Hoathis\Bundle\XylBundle\Loader\Filesystem as TestedClass;
use mock\Symfony\Component\Config\FileLocatorInterface as FileLocator;
use mock\Symfony\Component\Templating\TemplateNameParserInterface as TemplateNameParser;

class Filesystem extends atoum
{
    public function testClass()
    {
        $this
            ->testedClass->isSubClassOf('\\Hoathis\\Bundle\\XylBundle\\Loader')
        ;
    }

    public function testLocate()
    {
        $this
            ->given($locator = new FileLocator())
            ->and($this->calling($locator)->locate = $located = uniqid())
            ->and($parser = new TemplateNameParser())
            ->and($this->calling($parser)->parse = $parsed = uniqid())
            ->if($loader = new TestedClass($locator, $parser))
            ->and($template = uniqid())
            ->then
                ->string($loader->locate($template))->isEqualTo($located)
                ->mock($parser)
                    ->call('parse')->withArguments($template)->once()
                ->mock($locator)
                    ->call('locate')->withArguments($parsed)->once()
        ;
    }

    public function testExists()
    {
        $this
            ->given($locator = new FileLocator())
            ->and($this->calling($locator)->locate = $located = uniqid())
            ->and($parser = new TemplateNameParser())
            ->if($loader = new TestedClass($locator, $parser))
            ->then
                ->boolean($loader->exists(uniqid()))->isTrue()
            ->if($this->calling($locator)->locate->throw = new \InvalidArgumentException())
            ->then
                ->boolean($loader->exists(uniqid()))->isFalse()
        ;
    }

    public function testLoad()
    {
        $this
            ->given($locator = new FileLocator())
            ->and($this->calling($locator)->locate = $located = uniqid())
            ->and($parser = new TemplateNameParser())
            ->if($loader = new TestedClass($locator, $parser))
            ->and($template = uniqid())
            ->then
                ->string($loader->load($template))->isEqualTo($located)
            ->if($this->calling($locator)->locate->throw = $exception = new \InvalidArgumentException())
            ->then
                ->exception(function() use ($loader, $template) {
                    $loader->load($template);
                })
                    ->isInstanceOf('\\Hoa\\Xyl\\Exception')
                    ->hasMessage(sprintf('Unable to find template "%s".', $template))
                ->object($this->exception->getPrevious())->isIdenticalTo($exception)
        ;
    }
}