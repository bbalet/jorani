<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PositionRepository;
use App\Entity\Position;


class PositionController extends AbstractController
{
    #[Route('/position', name: 'app_position')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Position::class);
        $positions = $repository->findAll();

        return $this->render('position/index.html.twig', [
            'controller_name' => 'PositionController',
            'positions' => $positions
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
