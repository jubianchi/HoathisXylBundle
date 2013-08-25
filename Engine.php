<?php
namespace Hoathis\Bundle\XylBundle;

use Hoa\Stringbuffer\Read;
use Hoa\Stringbuffer\ReadWrite;
use Hoa\Xml\Exception\Exception as XmlException;
use Hoa\Xyl\Interpreter\Html\Html;
use Hoa\Xyl\Xyl;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;

class Engine implements EngineInterface
{
    protected $parser;
    protected $loader;

    public function __construct(TemplateNameParserInterface $parser, Loader $loader)
    {
        $this->parser = $parser;
        $this->loader = $loader;
    }

    /**
     * Renders a template.
     *
     * @param mixed $name       A template name or a TemplateReferenceInterface instance
     * @param array $parameters An array of parameters to pass to the template
     *
     * @throws \InvalidArgumentException if the template does not exist
     * @throws \RuntimeException if the template cannot be rendered
     *
     * @return string The evaluated template as a string
     *
     * @api
     */
    public function render($name, array $parameters = array(), Xyl $xyl = null)
    {
        if (false === $this->exists($name)) {
            throw new \InvalidArgumentException(sprintf('Unable to find template "%s".', $name));
        }

        try {
            $xyl = $xyl ?: new Xyl($this->load($name), new ReadWrite(), new Html());
        } catch (XmlException $exception) {
            throw new \RuntimeException(sprintf('Unable to load template "%s".', $name), -1, $exception);
        }

        try {
            $xyl->interprete();
            $xyl->render();
        } catch(\Exception $exception) {
            throw new \RuntimeException(sprintf('Unable to render template "%s".', $name), -1, $exception);
        }

        return $xyl->getOutputStream()->readAll();
    }

    protected function load($name)
    {
        return new Read($this->loader->load($name));
    }

    /**
     * Returns true if the template exists.
     *
     * @param mixed $name A template name or a TemplateReferenceInterface instance
     *
     * @return Boolean true if the template exists, false otherwise
     *
     * @api
     */
    public function exists($name)
    {
        return $this->loader->exists($name);
    }

    /**
     * Returns true if this class is able to render the given template.
     *
     * @param mixed $name A template name or a TemplateReferenceInterface instance
     *
     * @return Boolean true if this class supports the given template, false otherwise
     *
     * @api
     */
    public function supports($name)
    {
        $template = $this->parser->parse($name);

        return 'xyl' === $template->get('engine');
    }
}