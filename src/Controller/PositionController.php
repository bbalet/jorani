<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Repository\PositionRepository;
use App\Form\Type\PositionType;
use App\Entity\Position;


class PositionController extends AbstractController
{
    #[Route('/position', name: 'list_position')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Position::class);
        $positions = $repository->findAll();

        return $this->render('position/index.html.twig', [
            'controller_name' => 'PositionController',
            'positions' => $positions
        ]);
    }

    #[Route('/position', name: 'create_position')]
    public function create(ValidatorInterface $validator, Request $request, EntityManagerInterface $entityManager): Response
    {
        $position = new Position();
        $position->setName('Keyboard');
        $position->setDescription('Ergonomic and stylish!');

        $form = $this->createForm(PositionType::class, $position);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $position = $form->getData();

            // ... perform some action, such as saving the task to the database

            return $this->redirectToRoute('list_position');
        }

        return $this->render('task/new.html.twig', [
            'form' => $form,
        ]);

        $errors = $validator->validate($position);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }
        $entityManager->persist($position);
        $entityManager->flush();
        return new Response('Saved new position with id '.$position->getId());
    }

    #[Route('/position/edit/{id}', name: 'position_edit')]
    public function update(ValidatorInterface $validator, EntityManagerInterface $entityManager, int $id): Response
    {
        $position = $entityManager->getRepository(Position::class)->find($id);

        if (!$position) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $position->setName('New position name!');
        $entityManager->flush();

        return $this->redirectToRoute('position_show', [
            'id' => $position->getId()
        ]);
    }

    #[Route('/position/{id}', name: 'position_show')]
    public function show(int $id, PositionRepository $positionRepository): Response
    {
        $position = $positionRepository
            ->find($id);

        return $this->render('position/show.html.twig', [
            'position' => $position
        ]);
    }
}
