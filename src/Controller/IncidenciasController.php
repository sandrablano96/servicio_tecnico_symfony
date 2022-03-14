<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use \App\Entity\Incidencia;
use \App\Entity\Cliente;
use \App\Entity\Usuario;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use \Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use \Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Constraints\NotBlank;
/**
 * Require ROLE_ADMIN for all the actions of this controller
 *
 * @IsGranted("ROLE_USER")
 */
class IncidenciasController extends AbstractController

{
    #[Route('/incidencias', name: 'listado_incidencias')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $arrayIncidencias = $doctrine->getRepository(Incidencia::class)->findAllInOrder();
        return $this->render('incidencias/index.html.twig', [
            'incidencias' => $arrayIncidencias
        ]);
    }
    
    /**
     * Registra la incidencia en un cliente que pasa como parámetro
     * @Route("/clientes/{cliente<\d+>}/insertar", name="insertar_incidenciaCliente")
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    public function insertarConCliente(Request $request,ManagerRegistry $doctrine, Cliente $cliente):Response{
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $incidencia = new Incidencia();

        $form = $this->createFormBuilder($incidencia)
                ->add("titulo", TextType:: class, [
                     'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Introduzca el titulo de la incidencia',
                        ])
    ]
                ])
                ->add('enviar', SubmitType::class)
                ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $incidencia = $form->getData();
            $incidencia->setEstado("iniciada");
            $incidencia->setFechaCreacion(new \DateTime());
            $user = $this->getUser();
              if(!empty($user)) {
                  $incidencia->setUsuario($user);
            }

            $incidencia->setCliente($cliente);
            $entityManager = $doctrine->getManager();
                $entityManager->persist($incidencia);
                // actually executes the queries (i.e. the INSERT query)
                $entityManager->flush();
                $this->addFlash("aviso","incidencia iniciada");

            return $this->render("clientes/cliente.html.twig", ['cliente' => $cliente ]);
        } else{
            return $this->renderForm("incidencias/insertar.html.twig", ['formulario' => $form, 'cliente' => $cliente]);
        }
        
    }
    /**
     * Registra la incidencia escogiendo el cliente
     * @Route("/incidencias/insertar", name="insertar_incidencia")
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    public function insertar(Request $request,ManagerRegistry $doctrine):Response{
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $clientes = $doctrine->getRepository(Cliente::class)->findAll();
        $incidencia = new Incidencia();
        $form = $this->createFormBuilder($incidencia)
                ->add("titulo", TextType:: class, [
                    'required' => true,
                    'constraints' => [
                    new NotBlank([
                        'message' => 'Introduzca la contraseña',
                    ])
                    ]
                ])
                ->add('cliente', EntityType::class, [
                    'required' => true,
                    'class' => Cliente::class,
                    'choices' => $clientes, 
                    'choice_label' => 'nombreCompleto', 
                    'choice_value' => 'id', 
                    'constraints' => [
                    new NotBlank([
                        'message' => 'Seleccione un cliente',
                    ])
            ]
                ])
                ->add('enviar', SubmitType::class)
                ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $incidencia = $form->getData();
            $incidencia->setEstado("iniciada");
            $incidencia->setFechaCreacion(new \DateTime());
            $user = $this->getUser();
              if(!empty($user)) {
                  $incidencia->setUsuario($user);
            }
            $entityManager = $doctrine->getManager();
                $entityManager->persist($incidencia);
                // actually executes the queries (i.e. the INSERT query)
                $entityManager->flush();
                $this->addFlash("aviso","incidencia iniciada");

            return $this->redirectToRoute("listado_incidencias");
        } else{
            return $this->renderForm("incidencias/insertar.html.twig", ['formulario' => $form]);
        }
        
    }
    
    /**
     * @Route("/incidencias/{id<\d+>}", name="ver_incidencia")
     * @return Response
     */
    public function ver (Incidencia $incidencia):Response{
        return $this->render("incidencias/ver_incidencia.html.twig", ["incidencia" => $incidencia]);
    }
    
    
    /**
     * @Route("/incidencias/borrar/{id<\d+>}&{cliente<\d+>}", name="borrar_incidencia")
     * @return Response
     */
    public function borrar (Incidencia $incidencia, ManagerRegistry $doctrine):Response{
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager = $doctrine->getManager();
        $entityManager->remove($incidencia);
        $entityManager->flush();
        $this->addFlash("aviso", "Incidencia borrada correctamente");
        return $this->redirectToRoute("listado_clientes");
    }
    
    /**
     * @Route("/incidencias/editar/{id<\d+>}&{cliente<\d+>}", name="editar_incidencia")
     * @return Response
     */
    public function editar(ManagerRegistry $doctrine, Incidencia $incidencia, Request $request, Cliente $cliente){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if($request->isMethod("get")){
            $incidencia->setCliente($cliente);
        }
        
        $form = $this->createFormBuilder($incidencia)
                ->add("titulo", TextType:: class)
                ->add('enviar', SubmitType::class)
                ->add('estado', ChoiceType::class, [
                    'choices' => [
                        'Iniciada' => 'iniciada',
                        'En proceso' => 'en proceso',
                        'Resuelta' => 'resuelta'
                    ]
                    
                ])
                ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $incidencia = $form->getData();
//            $userID = $this->security->getUser()->getId();
//            if(!empty($userID)) {
//            
//                $incidencia->addUsuario($user);
//            }
            $usuario = $doctrine->getRepository(Usuario::class)->find(1);
            $incidencia->setUsuario($usuario);
            $entityManager = $doctrine->getManager();
                $entityManager->persist($incidencia);
                //$entityManager->persist($comentario);
                // actually executes the queries (i.e. the INSERT query)
                $entityManager->flush();
                $this->addFlash("aviso","incidencia modificada");

            return $this->render("clientes/cliente.html.twig", ['cliente' => $cliente ]);
        } else{
            return $this->renderForm("incidencias/editar.html.twig", ['formulario' => $form, 'cliente' => $cliente, 'incidencia' => $incidencia]);
        }
        
    }
}
