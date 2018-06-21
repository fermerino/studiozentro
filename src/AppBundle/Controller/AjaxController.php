<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Elemento;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Pedido;
use AppBundle\Entity\Pieza;
use AppBundle\Entity\Textura;
use AppBundle\Entity\Carro;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ajax controller.
 *
 * @Route("data")
 */
class AjaxController extends Controller
{
    /**
     * @Route("/completo", name="get_data")
     *
     * @param Request $request
     * @return Response
     */
    public function getDataAction(Request $request)
    {
        $pathImagen = '';
        $simuladorId= '';

        if ($request->get('data')) {
            $json = json_decode($request->get('data'), true);
            $em = $this->getDoctrine()->getManager();
            /** @var    Carro $carro */
            $carro = $em->getRepository('AppBundle:Carro')->findOneById(@$json['carro_id']);
            /** @var    Textura $textura */
            $textura = $em->getRepository('AppBundle:Textura')->findOneById(@$json['textura_id']);
            /** @var    Pieza $pieza */
            $pieza = $em->getRepository('AppBundle:Pieza')->findOneById(@$json['pieza_id']);
            $simuladorId = 'simulador-'.intval($pieza->getReferencia());

            if ($carro && $textura && $pieza) {
                $pathImagen = Pedido::calcularPathImagen($carro, $pieza, $textura);
            }
        }

        $response = new Response();
        $response->setContent(json_encode(array(
            'imagen'        => $pathImagen,
            'simulador_id'  => $simuladorId,
        )));

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/textura", name="get_img_textura")
     *
     * @param Request $request
     * @return Response
     */
    public function getImgTexturaAction(Request $request)
    {
        $pathImagen = '';

        if ($request->get('data')) {
            $json = json_decode($request->get('data'), true);
            $em = $this->getDoctrine()->getManager();
            /** @var Textura $textura */
            $textura = $em->getRepository('AppBundle:Textura')->findOneById(@$json['textura_id']);
            $pathImagen = $textura ? '/' . $textura->getPath() : '';
        }

        $response = new Response();
        $response->setContent(json_encode(array(
            'imagen'        => $pathImagen
        )));

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/guardar-pedido", name="guardar_pedido")
     *
     * @param Request $request
     * @return Response
     */
    public function guardarPedidoAction(Request $request)
    {
        $json = json_decode($request->getContent());

        if (!$json) {
            return new Response("Error en la recepción de datos", 406);
        }

        $em         = $this->getDoctrine()->getManager();
        $procesado  = false;
        $pedido  = new Pedido();
        $em         ->persist($pedido);
        $email      = '';

        foreach ($json as $item) {
            /** @var    Pieza $pieza */
            $pieza     = $em->getRepository('AppBundle:Pieza')->findOneById(@$item->pieza_id);
            $email      = @$item->email;

            if (!$pieza || strlen($email) < 1) continue;

            /** @var    Textura $textura */
            $textura    = $em->getRepository('AppBundle:Textura')->findOneById(@$item->textura_id);
            $texturizado= $textura ? 1 : 0;
            $precio     = $texturizado ?
                $pieza->getCantidadTextura() * $textura->getGrupo()->getPrecio() :
                $pieza->getPrecio();

            $elemento = new Elemento();
            $elemento
                ->setPedido($pedido)
                ->setPieza($pieza)
                ->setTextura($textura)
                ->setPrecio($precio)
                ->setTieneTextura($texturizado);

            $em->persist($elemento);
            $procesado = true;
        }

        if ($procesado) {
            $pedido->setEmail($email);
            $em->persist($pedido);
            $em->flush();
            $response = new Response("Pedido registrada con el identificador: " . $pedido->getId());
        } else {
            $response = new Response("Error en el procesado del pedido", 406);
        }


        return $response;
    }
}
