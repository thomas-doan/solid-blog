<?php

namespace App\Controller;

use App\Controller\Interfaces\AbstractCtrl;
use App\Router\Router;
use JetBrains\PhpStorm\NoReturn;

abstract class AbstractController implements AbstractCtrl
{

    public function render($view, $params = []): void
    {
        ob_start();
        extract($params);

        require_once 'src/views/' . $view . '.php';
        $content = ob_get_clean();
        require_once 'src/views/partials/header.php';
        echo $content;
        require_once 'src/views/partials/footer.php';
    }

    /**
     * @throws \Exception
     */
    #[NoReturn] public function redirect($url, $params = [], $code = 302, $message = null): void
    {
        $url = Router::url($url, $params);
        header("Location: $url", true, $code);
    }


}