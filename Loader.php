<?php
namespace Hoathis\Bundle\XylBundle;

use Symfony\Component\Templating\TemplateReferenceInterface;

interface Loader
{
    /**
     * Locates the template, given its name.
     *
     * @param string $template The template's name
     *
     * @throws \InvalidArgumentException if the template could not be located
     *
     * @return string The path to the template file
     */
    public function locate($template);

    /**
     * Check if the template exists.
     *
     * @param string $template The template's name
     *
     * @return boolean If the template source code is handled by this loader or not
     */
    public function exists($template);

    /**
     * Returns the path to the template.
     *
     * @param string|\Symfony\Component\Templating\TemplateReferenceInterface $template The template
     *
     * @throws \Hoa\Xyl\Exception if the template could not be loaded
     *
     * @return string The path to the template file
     *
     */
    public function load($template);
}