<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Question;
use App\Entity\Categorie;
use App\Entity\History;
use App\Form\EditUserFormType;
use App\Form\EditCategorieFormType;
use App\Form\AnswerQuestionType;
use App\Form\CreateCategorieFormType;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Reponse;

class HistoryController extends AbstractController
{
    
    #[Route('/history/{id}', name: 'app_history_quizz')]
    public function showHistory(EntityManagerInterface $entityManager, $id): Response
    {   
        $history = $entityManager->getRepository(History::class)->find($id);

        if(!$history){
            return $this->render('error/index.html.twig', [
                'message' => 'No History found for id ' . $id,
            ]);
        }
        

        return $this->render('history/show.html.twig', [
            'history' => $history,
        ]);
    }
    
    #[Route('/history', name: 'app_history')]
    public function showHistoryGlobal(EntityManagerInterface $entityManager): Response
    {   
        $historyg = $entityManager->getRepository(History::class)->findALL();

        return $this->render('historyglobal/index.html.twig', [
            'historyg' => $historyg,
        ]);
    }


 


}
