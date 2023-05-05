<?php

namespace App\Controller;

use App\Entity\CiSession;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContractController extends AbstractController
{
    #[Route('/contract', name: 'app_contract')]
    public function index(EntityManagerInterface $entityManager, TranslatorInterface $translator, Request $request): Response
    {
        $isLoggedIn = false;
        $session_id = $_COOKIE['jorani_session'];
        $session = $entityManager->getRepository(CiSession::class)->find($session_id);
        if (is_null($session)) {
            //Not authenticated
        } else {
            session_start();
            $session_data = session_decode(stream_get_contents($session->getData()));
            $isLoggedIn = $_SESSION['logged_in'];
            $languageCode = $_SESSION['language_code'];
            $translator->setLocale($languageCode);
/*
            $controller->fullname = $controller->session->userdata('firstname') . ' ' .
            $controller->session->userdata('lastname');
            $controller->is_manager = $controller->session->userdata('is_manager');
            $controller->is_admin = $controller->session->userdata('is_admin');
            $controller->is_hr = $controller->session->userdata('is_hr');
            $controller->user_id = $controller->session->userdata('id');
            $controller->manager = $controller->session->userdata('manager');
            $controller->language = $controller->session->userdata('language');
            $controller->language_code = $controller->session->userdata('language_code');
*/
        }        

        return $this->render('contract/index.html.twig', [
            'controller_name' => 'ContractController',
            'session_id' => $session_id,
            'languageCode' => $languageCode,
            'data' => $isLoggedIn
        ]);
    }
}
