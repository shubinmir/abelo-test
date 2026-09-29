<?php

declare(strict_types=1);

namespace App\Core;

use Smarty\Smarty;

final class View
{
    private Smarty $smarty;

    public function __construct(string $templateDir, string $compileDir)
    {
        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir($templateDir);
        $this->smarty->setCompileDir($compileDir);

        $this->smarty->setEscapeHtml(true);
    }

    public function render(string $template, array $data = []): string
    {
        $tpl = $this->smarty->createTemplate($template);
        $tpl->assign($data);

        return $tpl->fetch();
    }
}
