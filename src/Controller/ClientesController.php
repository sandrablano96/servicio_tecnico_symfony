<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Cliente;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Constraints\NotBlank;
/**
 * Require ROLE_ADMIN for all the actions of this controller
 *
 * @IsGranted("ROLE_USER")
 */
class ClientesController extends AbstractController
{
    #[Route('/clientes', name: 'listado_clientes')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $arrayClientes = $doctrine->getRepository(Cliente::class)->findAll();
        return $this->render('clientes/index.html.twig', [
            'clientes' => $arrayClientes
        ]);
    }
    /**
     * @Route("clientes/insertar", name="insertar_cliente")
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return type
     */
    public function insertar(Request $request,ManagerRegistry $doctrine):Response{
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $cliente = new Cliente();
        $form = $this->createFormBuilder($cliente)
                ->add('nombre', TextType::class, [
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Introduzca el nombre',
                        ])
            ]
                    ])
                ->add('apellidos', TextType::class, [
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Introduzca el los apellidos',
                        ])
            ]])
                ->add('direccion', TextType::class)
                ->add('telefono', TelType::class, [
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Introduzca el teléfono',
                        ])
            ]])
                ->add('enviar', SubmitType::class)
                ->getForm();
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $cliente = $form->getData();
            $entityManager = $doctrine->getManager();
                $entityManager->persist($cliente);
                $entityManager->flush();
                $this->addFlash("aviso", "Nuevo cliente creado");
                return $this->redirectToRoute("listado_clientes");
        }
        return $this->renderForm("clientes/insertar.html.twig", ['formulario' => $form]);
    }
    /**
     * @Route("/clientes/{id<\d+>}", name="ver_cliente")
     * @param Cliente $cliente
     * @return Response
     */
    public function ver_cliente(Cliente $cliente):Response{
         return $this->render("clientes/cliente.html.twig", ["cliente" => $cliente]);
    }
    
    /**
     * @Route("/clientes/borrar/{id<\d+>}", name="borrar_cliente")
     * @return Response
     */
    public function borrar (Cliente $cliente, ManagerRegistry $doctrine):Response{
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager = $doctrine->getManager();
        $entityManager->remove($cliente);
        $entityManager->flush();
        $this->addFlash("aviso", "Cliente borrado correctamente");
        return $this->redirectToRoute("listado_clientes");
    }
    
}
