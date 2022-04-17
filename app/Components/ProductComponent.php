<?php


namespace App\Components;


class ProductComponent
{
    protected string $props;
    public function __construct(array $props)
    {
        $this->props = $props;
    }

    public function render()
    {
        return render('a', [
        'href' => $this->props->href,
        ], [
        $this->props->name,
        ]);
    }

}