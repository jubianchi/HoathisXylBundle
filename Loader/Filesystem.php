<?php
namespace Hoathis\Bundle\XylBundle\Loader;

use Hoa\Xyl\Exception;
use Hoathis\Bundle\XylBundle\Loader;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;

class Filesystem implements Loader
{
    protected $locator;
    protected $parser;

    /**
     * Constructor.
     *
     * @param FileLocatorInterface        $locator A FileLocatorInterface instance
     * @param TemplateNameParserInterface $parser  A TemplateNameParserInterface instance
     */
    public function __construct(FileLocatorInterface $locator, TemplateNameParserInterface $parser)
    {
        $this->locator = $locator;
        $this->parser = $parser;
    }

    public function locate($template)
    {
        return $this->locator->locate($this->parser->parse($template));
    }

    public function exists($template)
    {
        try {
            $this->locate($template);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function load($template)
    {
        $logicalName = (string) $template;

        try {
            return $this->locate($logicalName);
        } catch(\InvalidArgumentException $exception) {
            throw new Exception(sprintf('Unable to find template "%s".', $logicalName), -1, func_get_args(), $exception);
        }
    }
}