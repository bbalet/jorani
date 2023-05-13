<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\CIBridgeService;
use App\Form\Type\PositionType;
use App\Entity\Position;


class PositionController extends AbstractController
{
    private TranslatorInterface $translator;
    private EntityManagerInterface $entityManager;

    public function __construct(TranslatorInterface $translator, EntityManagerInterface $entityManager, CIBridgeService $ciBridge)
    {
        $this->translator = $translator;
        $this->entityManager = $entityManager;
        // $ciBridge->checkIfLoggedInOrRedirect();
    }

    #[Route('/position', name: 'position_list')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Position::class);
        $positions = $repository->findAll();

        return $this->render('position/index.html.twig', [
            'controller_name' => 'PositionController',
            'positions' => $positions
        ]);
    }

    #[Route('/position/create', name: 'position_create')]
    public function create(ValidatorInterface $validator, Request $request): Response
    {
        $position = new Position();
        $form = $this->createForm(PositionType::class, $position);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $position = $form->getData();
            $errors = $validator->validate($position);
            if (count($errors) > 0) {
                return new Response((string) $errors, 400);
            }
            $this->entityManager->persist($position);
            $this->entityManager->flush();
            $this->addFlash('success', $this->translator->trans('The position has been succesfully created.'));
            return $this->redirectToRoute('position_list');
        }
        return $this->render('position/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/position/edit/{id}', name: 'position_edit')]
    public function update(ValidatorInterface $validator, Request $request, int $id): Response
    {
        $position = $this->entityManager->getRepository(Position::class)->find($id);
        if (!$position) {
            throw $this->createNotFoundException(
                'No position found for id '.$id
            );
        }
        $form = $this->createForm(PositionType::class, $position);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $position = $form->getData();
            $errors = $validator->validate($position);
            if (count($errors) > 0) {
                return new Response((string) $errors, 400);
            }
            $this->entityManager->persist($position);
            $this->entityManager->flush();
            $this->addFlash('success', $this->translator->trans('The position has been succesfully updated.'));
            return $this->redirectToRoute('position_list');
        }
        return $this->render('position/edit.html.twig', [
            'form' => $form,
            'position' => $position,
        ]);
    }

    #[Route('/position/delete/{id}', name: 'position_delete')]
    public function delete(int $id): Response
    {
        $position = $this->entityManager->getRepository(Position::class)->find($id);
        if (!$position) {
            throw $this->createNotFoundException(
                'No position found for id '.$id
            );
        }
        $this->entityManager->remove($position);
        $this->entityManager->flush();
        $this->addFlash('success', $this->translator->trans('The position has been succesfully deleted.'));
        return $this->redirectToRoute('position_list');
    }
}
