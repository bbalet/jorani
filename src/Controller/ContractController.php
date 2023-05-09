<?php

namespace App\Controller;

use App\Entity\Contract;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\CIBridgeService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contract controller
 * @license AGPL-3.0
 * @copyright Copyright (c) 2023 Benjamin BALET
 */
class ContractController extends AbstractController
{
    #[Route('/contract', name: 'app_contract')]
    public function index(EntityManagerInterface $entityManager, Request $request, CIBridgeService $ciBridge): Response
    {
        $ciBridge->checkIfLoggedInOrRedirect();
        
        $contracts = $entityManager->getRepository(Contract::class)->findAll();

        return $this->render('contract/index.html.twig', [
            'controller_name' => 'ContractController',
            'session_id' => $ciBridge->getSessionId(),
            'languageCode' => $request->getLocale(),
            'data' => $ciBridge->isLoggedIn(),
            'contracts' => $contracts
        ]);
    }
}
