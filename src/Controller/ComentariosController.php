<?php

namespace App\Controller;

use App\Entity\Comentarios;
use App\Form\ComentariosType;
use App\Repository\EstudiantesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Comentarios controller.
 *
 * @Route("/comentarios")
 */
class ComentariosController extends AbstractController
{
    /**
     * Lists all Comentarios entities.
     * @Route("/anual/{actual}", name="comentarios_index", methods={"GET"}, defaults={"actual"="2023"})
     */
    public function index($actual)
    {
        $em = $this->getDoctrine()->getManager();

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $informe = $em->getRepository('App:Informe')->findOneByAnio($actual, $user->getAcademico());

        $comentarios = $informe->getcomentarios();
        $enviado = $informe->isEnviado();


        return $this->render('comentarios/index.html.twig', array(
            'comentarios' => $comentarios,
            'enviado'=>$enviado,
            'anual'=>$actual,
        ));

    }

    /**
     * Creates a new comentarios entity.
     *
     * @Route("/new", name="comentarios_new", methods={"GET","POST"})
     */
    public function new(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $securityContext = $this->container->get('security.token_storage');
        $user = $securityContext->getToken()->getUser();
        $academico = $user->getAcademico();
        $twigglobals = $this->get("twig")->getGlobals();
        $actual = $twigglobals["actual"];

        $em = $this->getDoctrine()->getManager();
        $informe = $em->getRepository('App:Informe')->findOneByAnio($actual, $academico);

        $comentario = new Comentarios();
        $form = $this->createForm('App\Form\ComentariosType', $comentario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $comentario->setInforme($informe);
            $em->persist($comentario);
            $em->flush();

            return $this->redirectToRoute('comentarios_index');
            //return $this->redirectToRoute('dashboard');
        }

        return $this->render('comentarios/new.html.twig', array(
            'comentario' => $comentario,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Comentarios entity.
     *
     * @Route("/{id}", name="comentarios_show", methods={"GET"})
     */
    public function showAction(Comentarios $comentario)
    {
        $securityContext = $this->container->get('security.token_storage');
        $user = $securityContext->getToken()->getUser();
        $academico = $user->getAcademico();
        $twigglobals = $this->get("twig")->getGlobals();
        $actual = $twigglobals["actual"];

        $em = $this->getDoctrine()->getManager();

        $informe = $em->getRepository('App:Informe')->findOneByAnio($actual, $academico);

        $enviado = $informe->isEnviado();


        $deleteForm = $this->createDeleteForm($comentario);

        // check for "view" access: calls all voters
//        $this->denyAccessUnlessGranted('view', $comentario);

        return $this->render('comentarios/show.html.twig', array(
            'comentario' => $comentario,
            'enviado'=>$enviado,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Comentarios entity.
     *
     * @Route("/{id}/edit", name="comentarios_edit", methods={"GET","POST"})
     */
    public function editAction(Request $request, Comentarios $comentario)
    {
        $deleteForm = $this->createDeleteForm($comentario);
        $editForm = $this->createForm('App\Form\ComentariosType', $comentario);
//        $this->denyAccessUnlessGranted('edit', $comentario);

        $editForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comentario);
            $em->flush();

            //return $this->redirectToRoute('estudiantes_show', array('id' => $estudiante->getId()));
            return $this->redirectToRoute('comentarios_index');

        }

        return $this->render('comentarios/edit.html.twig', array(
            'id'=>$comentario->getId(),
            'comentario' => $comentario,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Comentarios entity.
     *
     * @Route("/{id}", name="comentarios_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, Comentarios $comentario)
    {
        $form = $this->createDeleteForm($comentario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comentario);
            $em->flush();
        }

        return $this->redirectToRoute('comentarios_index');
    }

    /**
     * Creates a form to delete a Comentarios entity.
     *
     * @param Comentarios $estudiante The Comentarios entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Comentarios $comentarios)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('comentarios_delete', array('id' => $comentarios->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}