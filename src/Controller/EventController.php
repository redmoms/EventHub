<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/event')]
final class EventController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;    
    }
    
    #[Route(name: 'app_event_index', methods: ['GET'])]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $event = $this->entityManager->getRepository(Event::class)->findAll();
        $user = $this->getUser();
        
        $pagination = $paginator->paginate(
            $event, /* query NOT result */
            $request->query->getInt('page', 1), /* page number */
            10 /* limit per page */
        );

        return $this->render('event/index.html.twig', [
            'events' => $pagination,
            'user' => $user
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($event);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->getPayload()->getString('_token'))) {
            $this->entityManager->remove($event);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/{id}/register', name: 'app_event_register', methods: ['GET', 'POST'])]
    public function eventRegister(Event $event): Response
    {
        $user = $this->getUser();
        $event->addUser($user);
        $this->entityManager->flush();
        
        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);


    }
}
