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
        return $this->render(':custom:home.html.twig', [
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
     * @Route("/encimeras", name="encimeras")
     */
    public function cartaAction(Request $request)
    {
        return $this->render(':custom:carta.html.twig');
    }

    /**
     * @Route("/promociones", name="promociones")
     */
    public function promocionesAction(Request $request)
    {
        return $this->render(':custom:promociones.html.twig');
    }

    /**
     * @Route("/galeria/{recurso}", name="galeria")
     * @param Request $request
     * @param $recurso
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function galeriaAction(Request $request, $recurso = "cocinas")
    {
        $directorio = "images/$recurso";
        $gestor_dir = opendir($directorio);
        while (false !== ($nombre_fichero = readdir($gestor_dir))) {
            if ($nombre_fichero === '.' || $nombre_fichero === "..") continue;
            $imagenes[] = [
                filectime(pathinfo($nombre_fichero, PATHINFO_DIRNAME)) =>
                    [
                        'nombre'   => ucwords(pathinfo($nombre_fichero, PATHINFO_FILENAME)),
                        'fichero'  => "images/$recurso/$nombre_fichero"
                    ]
                ];
        }

        krsort($imagenes);

        return $this->render(':custom:galeria.html.twig', ['imagenes' => $imagenes, 'tipo' => $recurso]);
    }

    /**
     * @Route("/electrodomesticos", name="electrodomesticos")
     */
    public function quienesSomosAction(Request $request)
    {
        return $this->render(':custom:quienesSomos.html.twig');
    }

    /**
     * @Route("/reformas", name="reformas")
     */
    public function reservaAction(Request $request)
    {
        return $this->render(':custom:contacto.html.twig');
    }

    /**
     * @Route("/presupuestos", name="presupuestos")
     */
    public function presupuestosAction(Request $request)
    {
        return $this->render(':custom:reserva.html.twig');
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
