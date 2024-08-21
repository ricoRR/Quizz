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

class CategorieController extends AbstractController
{
    
    #[Route('/categorie/{id}', name: 'app_quizz_categorie')]
    public function showquizz(EntityManagerInterface $entityManager, $id, Request $request): Response
    {   
        $categorie = $entityManager->getRepository(Categorie::class)->find($id);

        if (!$categorie) {

            return $this->render('error/index.html.twig', [
                'message' => 'No category found for id ' . $id,
            ]);
            
        }

        if ($request->isMethod('POST')) {
            $formData = $request->request->all();

          
            $result = 0;
            $answer=[];
            foreach ($formData as $questionId => $responseId) {
                $question = $entityManager->getRepository(Question::class)->find($questionId);
                $response = $entityManager->getRepository(Reponse::class)->find($responseId);
                array_push($answer,$response->getReponse());
                if ($response->getReponseExpected()) {
                    $result++;
                }
            }

            $history = new History();

            $user = $this->getUser();
            if ($user instanceof User) {
                $history->setuserName($user->getEmail());
            }
            
            $history->setResult($result);
            $history->setAnswer($answer);
            $history->setCreatedAt(new \DateTime());
            $entityManager->persist($history);
            $entityManager->flush();

            return $this->redirectToRoute('app_history_quizz', ['id' => $history->getId()]);
        }

        return $this->render('categorie/index.html.twig', [
            'categorie' => $categorie,
        ]);
    }
    
    
    #[Route('/categorie/delete/{id}', name: 'app_quizz_categorie_delete')]
    public function deletequizz(EntityManagerInterface $entityManager, $id): Response
    {   


        try {
            $categories = $entityManager->getRepository(Categorie::class)->find($id);

            $entityManager->remove($categories);

            $entityManager->flush();
            $this->addFlash('success', 'The Categorie was deleted.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'The Categorie could not be deleted.');
        }

           return $this->redirectToRoute('app_home');
      
    }
    
    #[Route('/categorie/edit/{id}', name: 'app_quizz_categorie_edit')]
    public function editquizz(EntityManagerInterface $entityManager, $id,Request $request): Response
    {   


        $category = $entityManager->getRepository(Categorie::class)->find($id);
        

        $form = $this->createForm(EditCategorieFormType::class, $category);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($category);
            $entityManager->flush();


            $this->addFlash('success', 'Quizz edit successfully.');

            return $this->redirectToRoute('app_home'); 

            }
            
        return $this->render('categorie/edit.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/categorie_create', name: 'create_categorie')]
    public function createquizz(EntityManagerInterface $entityManager, Request $request): Response
    {
        $category = new Categorie();

        for ($i = 0; $i < 10; $i++) {
            $question = new Question();
            $question->setQuestion("Question " . ($i + 1));
            
            for ($j = 0; $j < 3; $j++) {
                $response = new Reponse();
                $response->setReponse("Response " . ($j + 1));
                $response->setReponseExpected($j == 0); 
                
                $question->addResponse($response);
            }

            $category->addQuestion($question);
        }

        $form = $this->createForm(CreateCategorieFormType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Category created successfully.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('categorie/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/categorie/sure_delete/{id}', name: 'app_quizz_categorie_delete_verif')]
        public function verifquizzdelete(EntityManagerInterface $entityManager, $id): Response
        {   
            $categories = $entityManager->getRepository(Categorie::class)->find($id);



            return $this->render('categorie/delete.html.twig', [
                'categories' => $categories,
            ]);
        }

    
   


}
