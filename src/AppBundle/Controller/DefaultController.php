<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Swift_Message;
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
            if ($nombre_fichero === 'proyecto_trescantos') continue;

            $nombre_final = ucwords(pathinfo($nombre_fichero, PATHINFO_FILENAME));
            $nombre_final = str_replace('_', ' ', $nombre_final);
            $nombre_final = str_replace('-', ' ', $nombre_final);

            $imagenes[] = [
                filectime(pathinfo($nombre_fichero, PATHINFO_DIRNAME)) =>
                    [
                        'nombre'   => $nombre_final,
                        'fichero'  => "images/$recurso/$nombre_fichero"
                    ]
                ];
            $imagenes_por_nombre[$nombre_final] = [
                $nombre_final =>
                    [
                        'nombre'   => $nombre_final,
                        'fichero'  => "images/$recurso/$nombre_fichero"
                    ]
            ];
        }

        krsort($imagenes);

        switch ($recurso){
            case 'cocinas_instaladas':
                $title = "Cocinas instaladas";
                array_multisort(array_keys($imagenes), SORT_NATURAL, $imagenes);
                break;
            case 'cocinas':
                $title = "¡¡¡ Haz tu sueño realidad !!!";
                $imagenes = $imagenes_por_nombre;
                array_multisort(array_keys($imagenes), SORT_NATURAL, $imagenes);
                break;
            case 'electrodomesticos':
                $title = "Nuestros proveedores son los más prestigiosos fabricantes";
                break;
            case 'encimeras':
                $title = "El lugar donde realmente trabajas";
                break;
            case 'catalogo':
                $title = "Catálogo Studio Zentro";
                $imagenes = $imagenes_por_nombre;
                array_multisort(array_keys($imagenes), SORT_NATURAL, $imagenes);
                break;
            default:
                $title = "Galería de $recurso";

                break;
        }

        return $this->render(':custom:galeria.html.twig', ['imagenes' => $imagenes, 'tipo' => $recurso, 'titleh1' => $title]);
    }

  /**
   * @Route("/galeria/{recurso}/{subrecurso}", name="subgaleria")
   * @param Request $request
   * @param $recurso
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function subGaleriaAction(Request $request, $recurso = "cocinas_instaladas", $subrecurso = "proyecto_trescantos")
  {
    $directorio = "images/$recurso/$subrecurso";
    $gestor_dir = opendir($directorio);
    while (false !== ($nombre_fichero = readdir($gestor_dir))) {
      if ($nombre_fichero === '.' || $nombre_fichero === "..") continue;


      $nombre_final = ucwords(pathinfo($nombre_fichero, PATHINFO_FILENAME));
      $nombre_final = str_replace('_', ' ', $nombre_final);
      $nombre_final = str_replace('-', ' ', $nombre_final);

      $imagenes[] = [
        filectime(pathinfo($nombre_fichero, PATHINFO_DIRNAME)) =>
          [
            'nombre'   => $nombre_final,
            'fichero'  => "images/$recurso/$subrecurso/$nombre_fichero"
          ]
      ];
      $imagenes_por_nombre[$nombre_final] = [
        $nombre_final =>
          [
            'nombre'   => $nombre_final,
            'fichero'  => "images/$recurso/$subrecurso/$nombre_fichero"
          ]
      ];
    }

    krsort($imagenes);

    switch ($subrecurso){
//      case 'cocinas_instaladas':
//        $title = "Cocinas instaladas";
//        array_multisort(array_keys($imagenes), SORT_NATURAL, $imagenes);
//        break;
//      case 'cocinas':
//        $title = "¡¡¡ Haz tu sueño realidad !!!";
//        $imagenes = $imagenes_por_nombre;
//        array_multisort(array_keys($imagenes), SORT_NATURAL, $imagenes);
//        break;
      case 'proyecto_trescantos':
        $title = "Galería de proyecto en Tres Cantos";
        $imagenes = $imagenes_por_nombre;
        array_multisort(array_keys($imagenes), SORT_NATURAL, $imagenes);
        break;

      default:
        $title = "Galería de $subrecurso";

        break;
    }

    return $this->render(':custom:galeria.html.twig', ['imagenes' => $imagenes, 'tipo' => $recurso, 'titleh1' => $title]);
  }

    /**
     * @Route("/quienes-somos", name="quienessomos")
     */
    public function quienesSomosAction(Request $request)
    {
        return $this->render(':custom:quienesSomos.html.twig');
    }

    /**
     * @Route("/promociones", name="promociones")
     */
    public function promosActions(Request $request)
    {
        return $this->render(':custom:promociones.html.twig');
    }

    /**
     * @Route("/reformas", name="reformas")
     */
    public function reservaAction(Request $request)
    {
        return $this->render(':custom:contacto.html.twig');
    }

    /**
     * @Route("/envio-contacto", name="envio_contacto")
     */
    public function sendMail(Request $request) {
        parse_str($request->getContent(), $form);

        $from    = $this->container->getParameter('mailer_user');
        $to      = $from;
        $cc      = array('raulesteban1967@gmail.com', 'fer.merinol@gmail.com'); //raul@studiozentro.es,

        if (empty($form['comentarios']) || empty($form['email']) || empty($form['nombre'])) {
            $this->addFlash('warning', 'Error. Por favor, complete su email, comentario y nombre');
            return $this->render(':custom:contacto.html.twig');
        }

        /** @var Swift_Message $message */
        $message = Swift_Message::newInstance()
            ->setSubject('[StudioZentro]: Formulario de contacto')
            ->setFrom($from)
            ->setTo($to) //$to
            ->setCc($cc)
            ->setBody(
                $this->renderView(
                    ':mail:contacto.html.twig',
                    ['form' => $form]
                ),
                'text/html'
            )
        ;
        if ($this->get('mailer')->send($message)) {
            $this->addFlash('success', 'Gracias, presupuesto enviado');
        } else {
            $this->addFlash('warning', 'Lo sentimos mensaje fallido. Contacte por teléfono');
        }

        return $this->render(':custom:contactoDone.html.twig');
    }
}
