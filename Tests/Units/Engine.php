<?php
namespace Hoathis\Bundle\XylBundle\Tests\Units;

use atoum;
use Hoa\File\Read;
use Hoa\File\ReadWrite;
use Hoa\Xyl\Exception;
use Hoa\Xyl\Interpreter\Html\Html;
use Hoathis\Bundle\XylBundle\Engine as TestedClass;
use mageekguy\atoum\mock\streams\fs\file;
use mock\Hoa\Xyl\Xyl;
use mock\Hoathis\Bundle\XylBundle\Loader;
use mock\Symfony\Component\Templating\TemplateNameParserInterface as TemplateNameParser;
use mock\Symfony\Component\Templating\TemplateReferenceInterface as TemplateReference;

class Engine extends atoum
{
    public function testClass()
    {
        $this
            ->testedClass->isSubClassOf('\\Symfony\\Component\\Templating\\EngineInterface')
        ;
    }

    public function testRender()
    {
        $this
            ->given($parser = new TemplateNameParser())
            ->and($loader = new Loader())
            ->and($this->calling($loader)->exists = true)
            ->and($inStream = file::get())
            ->and($inStream->setContents('<?xml version="1.0" encoding="utf-8"?><document xmlns="http://hoa-project.net/xyl/xylophone"><header><h1>title</h1></header></document>'))
            ->and($outStream = file::get())
            ->and($xyl = new Xyl(new Read($inStream), new ReadWrite($outStream), new Html()))
            ->if($engine = new TestedClass($parser, $loader))
            ->and($template = uniqid())
            ->then
                ->string($engine->render($template, array(), $xyl))->isEqualTo(<<<HTML
<!DOCTYPE html>

<!--[if lt IE 7]><html class="ie6"><![endif]-->
<!--[if    IE 7]><html class="ie7"><![endif]-->
<!--[if    IE 8]><html class="ie8"><![endif]-->
<!--[if (gte IE 9)|!(IE)]>
<html>
<![endif]-->
<head>

  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta http-equiv="content-type" content="text/javascript; charset=utf-8" />
  <meta http-equiv="content-type" content="text/css; charset=utf-8" />
</head>
<body>

<header><h1>title</h1></header>

</body>
</html>
HTML
)
            ->if($this->calling($loader)->exists = false)
            ->then
                ->exception(function() use ($engine, $template) {
                    $engine->render($template);
                })
                    ->isInstanceOf('\\InvalidArgumentException')
                    ->hasMessage(sprintf('Unable to find template "%s".', $template))
            ->if($this->calling($loader)->exists = true)
            ->then
                ->exception(function() use ($engine, $template, $xyl) {
                    $engine->render($template);
                })
                    ->isInstanceOf('\\RuntimeException')
                    ->hasMessage(sprintf('Unable to load template "%s".', $template))
                    ->object($this->exception->getPrevious())->isInstanceOf('\\hoa\\Xml\\Exception\\Exception')
            ->if($this->calling($xyl)->render->throw = $exception = new Exception(uniqid()))
            ->then
                ->exception(function() use ($engine, $template, $xyl) {
                    $engine->render($template, array(), $xyl);
                })
                    ->isInstanceOf('\\RuntimeException')
                    ->hasMessage(sprintf('Unable to render template "%s".', $template))
                ->object($this->exception->getPrevious())->isIdenticalTo($exception)

        ;
    }

    public function testExists()
    {
        $this
            ->given($parser = new TemplateNameParser())
            ->and($loader = new Loader())
            ->and($this->calling($loader)->exists = true)
            ->if($engine = new TestedClass($parser, $loader))
            ->and($template = uniqid())
            ->then
                ->boolean($engine->exists($template))->isTrue()
                ->mock($loader)
                    ->call('exists')->withArguments($template)->once()
            ->if($this->calling($loader)->exists = false)
            ->then
                ->boolean($engine->exists($template))->isFalse()
        ;
    }

    public function testSupports()
    {
        $this
            ->given($parser = new TemplateNameParser())
            ->and($loader = new Loader())
            ->and($reference = new TemplateReference())
            ->and($this->calling($parser)->parse = $reference)
            ->and($this->calling($reference)->get = 'xyl')
            ->if($engine = new TestedClass($parser, $loader))
            ->then
                ->boolean($engine->supports(uniqid()))->isTrue()
                ->mock($reference)
                    ->call('get')->withArguments('engine')->once()
            ->if($this->calling($reference)->get = uniqid())
            ->then
                ->boolean($engine->supports(uniqid()))->isFalse()
        ;
    }
}