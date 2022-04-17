<?php


namespace Framework\View\Engine;
use function view;
use Framework\View\View;


class PhpEngine implements Engine
{
    // protected string $path;
    // protected ?string $layout;
    // protected string $contents;
    protected $layouts =[];
   

    // public function render(string $path, array $data = []): string
    public function render(View $view): string
    {
        // $this->path = $path;

        // extract($data);

        ob_start();
        // include($this->path);
        $contents = ob_get_contents();
        ob_end_clean();

        // if($this->layout){
        if($layout = $this->layouts[$view->path] ?? null):
        // $__layout = $this->layout;
        // $this->layout = null;
        // $this->contents = $contents;

        // $contentsWithLayout =view($__layout, $data);
        $contentsWithLayout = view($layout, array_merge(
            $view->data,
            ['contents'=>$contents],
        ));
            return $contentsWithLayout;
        endif;

        return $contents;
    }

    protected function extends(string $template)
    {
        $this->layout = $template;
        return $this;
    }

    protected function escape(string $content): string
    {
        return htmlspecialchars($content, ENT_QUOTES);
    }
}