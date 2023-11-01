<?php 

namespace App\EventListener;

use Twig\Environment;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

//L'attribut PHP 8 permet d'accrocher l'écouteur à un certain évènement (hook), 
// évitant d'avoir à le configurer dans le fichier `services.yaml`
#[AsEventListener(event: KernelEvents::EXCEPTION)]
class PageNotFindListener{

    //Initialisation de l'envoronement Twig
    public function __construct(private Environment $twig){}

    //En cas de requète HTTP et d'une route introuvale (status erreur 404)
    //Cette methode prend ExceptionEvent en paramètre
    public function onKernelException(ExceptionEvent $event):void{
        //Le plus simple
        // $exception = new \Exception('Some special exception');
        // $event->setThrowable($exception);
        //$event accede a une methode de ExceptionEvent
        $exception = $event->getThrowable();
        //Si la requète HTTP aboutit => on s'arrete la !
        if(!$exception instanceof NotFoundHttpException){
            return;
        }
        //Si la requète HTTP echoue => on appel une vue Twig
        $content = $this->twig->render('notifications/page_not_found_exception.html.twig');
        $event->setResponse((new Response())->setContent($content));
    }
}