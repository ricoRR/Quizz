<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Categorie;
use App\Entity\Visit;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    
    public function show(EntityManagerInterface $entityManager,Request $request): Response
    {   
        $ip = $request->getClientIp();
        $visit = new Visit();
        $visit->setIp($ip);
        $visit->setVisitedAt(new \DateTime());

        $entityManager->persist($visit);
        $entityManager->flush();
        
        $quizz = $entityManager->getRepository(Categorie::class)->findAll();

        return $this->render('home/index.html.twig', [
            'categorie' => $quizz,
        ]);        
    }
}