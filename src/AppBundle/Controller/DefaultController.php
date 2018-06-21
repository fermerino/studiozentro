<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/inicio", name="homepage")
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render(':restaulandia:home.html.twig', [
        ]);
    }

    /**
     * @Route("/proximamente", name="proximamente")
     */
    public function proximamenteAction(Request $request)
    {
        return $this->render(':default:proximamente.html.twig');
    }

    /**
     * @Route("/carta", name="carta")
     */
    public function cartaAction(Request $request)
    {
        return $this->render(':restaulandia:carta.html.twig');
    }

    /**
     * @Route("/promociones", name="promociones")
     */
    public function promocionesAction(Request $request)
    {
        return $this->render(':restaulandia:promociones.html.twig');
    }

    /**
     * @Route("/galeria/{tipo}", name="galeria")
     * @param Request $request
     * @param $tipo
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function galeriaAction(Request $request, $tipo)
    {

        if (!in_array($tipo, ['infantil', 'platos'])) {
            return $this->render(':restaulandia:videos.html.twig');
        }

        $directorio = "images/$tipo";
        $gestor_dir = opendir($directorio);
        while (false !== ($nombre_fichero = readdir($gestor_dir))) {
            if ($nombre_fichero === '.' || $nombre_fichero === "..") continue;
            $imagenes[] = [
                filectime(pathinfo($nombre_fichero, PATHINFO_DIRNAME)) =>
                    [
                        'nombre'   => ucwords(pathinfo($nombre_fichero, PATHINFO_FILENAME)),
                        'fichero'  => "images/$tipo/$nombre_fichero"
                    ]
                ];
        }

        krsort($imagenes);

        return $this->render(':restaulandia:galeria.html.twig', ['imagenes' => $imagenes, 'tipo' => $tipo]);
    }

    /**
     * @Route("/quienes-somos", name="quienesSomos")
     */
    public function quienesSomosAction(Request $request)
    {
        return $this->render(':restaulandia:quienesSomos.html.twig');
    }

    /**
     * @Route("/reserva", name="reserva")
     */
    public function reservaAction(Request $request)
    {
        return $this->render(':restaulandia:reserva.html.twig');
    }
//
//    /**
//     * @Route("/{recurso}", name="404", requirements={
//     *     "recurso" = "(!reserva|!quienes-somos)$"
//     * })
//     */
//    public function notFoundAction(Request $request, $recurso)
//    {
//        return  $this->render(':exception:error404.html.twig');
//    }
}
