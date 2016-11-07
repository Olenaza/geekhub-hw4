<?php

namespace Views;

class Renderer
{
    private $templatesDirectory;

    /**
     * Renderer constructor.
     * @param $templatesDirectory
     */
    public function __construct($templatesDirectory)
    {
        $this->templatesDirectory = $templatesDirectory;
    }

    public function render($template, $data)
    {
        ob_start();
        eval($data);
        require $this->templatesDirectory . $template;
        $renderedHtml = ob_get_clean();
        return $renderedHtml;
    }
}