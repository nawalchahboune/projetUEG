<?php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Twig\Environment;

class AccessDeniedListener
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Vérifie si l'exception est une AccessDeniedHttpException
        if ($exception instanceof AccessDeniedHttpException) {
            $response = new Response(
                $this->twig->render('bundles/TwigBundle/Exception/error403.html.twig'),
                Response::HTTP_FORBIDDEN
            );

            $event->setResponse($response);
        }
    }
}