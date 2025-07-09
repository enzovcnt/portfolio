<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Project;
use App\Form\ContactForm;
use App\Form\ImageForm;
use App\Form\ProjectForm;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_project')]
    public function index(ProjectRepository $projectRepository, Request $request, MailerInterface $mailer): Response
    {

        $form = $this->createForm(ContactForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $email = (new Email())
                ->from($data['email'])
                ->to('mail@enzovincent.org')
                ->subject('Nouveau message de contact')
                ->text("Nom: {$data['nom']}\nEmail: {$data['email']}\nMessage:\n{$data['message']}");

            $mailer->send($email);

            $this->addFlash('success', 'Votre message a bien été envoyé !');
            return $this->redirectToRoute('app_project');
        }
        return $this->render('home/index.html.twig', [
            'projects' => $projectRepository->findAll(),
            'contactForm' => $form->createView(),
        ]);
    }

    #[Route('/project/{id}', name: 'app_project_show', priority: -1)]
    public function show(Project $project): Response
    {
        return $this->render('home/show.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/project/new', name: 'app_project_new')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_project');
        }
        if (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_project');
        }
        $project = new Project();
        $form = $this->createForm(ProjectForm::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($project);
            $manager->flush();
            return $this->redirectToRoute('app_project');
        }

        return $this->render('home/create.html.twig', [
            'form' =>  $form->createView(),
        ]);

    }

    #[Route('/project/{id}/edit', name: 'app_project_edit')]
    public function edit(Request $request, Project $project, EntityManagerInterface $manager): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_project');
        }
        if (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_project');
        }

        $form = $this->createForm(ProjectForm::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            return $this->redirectToRoute('app_project_show', ['id' => $project->getId()]);
        }
        return $this->render('home/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/project/{id}/delete', name: 'app_project_delete')]
    public function delete(Project $project, EntityManagerInterface $manager): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_project');
        }
        if (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_project');
        }

        $manager->remove($project);
        $manager->flush();
        return $this->redirectToRoute('app_project');
    }

    #[Route('/project/addimage/{id}', name: 'app_project_addimage')]
    public function addImage(Project $project, Request $request, EntityManagerInterface $manager) : Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_project');
        }
        if (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_project');
        }

        $image = new Image();
        $formImage = $this->createForm(ImageForm::class, $image);
        $formImage->handleRequest($request);
        if($formImage->isSubmitted() && $formImage->isValid()){
            $image->setProject($project);
            $manager->persist($image);
            $manager->flush();
            return $this->redirectToRoute('app_project_addimage', ['id' => $project->getId()]);
        }


        return $this->render('home/image.html.twig', [
            'projet' => $project,
            'formImage' => $formImage->createView(),
        ]);
    }

    #[Route('/projet/removeImage/{id}', name: 'app_removeImage')]
    public function removeImage(Image $image, EntityManagerInterface $manager) : Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_project');
        }
        if (!in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_project');
        }

        $projectId = $image->getProject()->getId();
        $manager->remove($image);
        $manager->flush();


        return $this->redirectToRoute('app_project_addimage', ['id' => $projectId]);
    }
}
