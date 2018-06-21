<?php
/**
 * Created by PhpStorm.
 * User: fmerinol
 * Date: 23/03/17
 * Time: 18:42
 */

namespace AppBundle\Event;


use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class MantenimientoListener
{
    function onKernelRequest(GetResponseEvent $event)
    {
        $ipsPermitidas = [
            "83.113.67.107",
            "185.73.174.6",
            "127.0.0.1",
            "::1",
        ];

        if (in_array($_SERVER['REMOTE_ADDR'], $ipsPermitidas)){
            return;
        }

        //$event->setResponse(new RedirectResponse('proximamente'));
        //$event->setResponse(new RedirectResponse('http://originalcircle.es/index2.html'));
        //$event->stopPropagation();

        return new RedirectResponse('proximamente');
    }
}