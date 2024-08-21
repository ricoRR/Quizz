<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Visit;
use App\Entity\History;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;



use Symfony\Component\HttpFoundation\Request;
use App\Entity\Reponse;

class StatsController extends AbstractController
{
    
    #[Route('/stats', name: 'app_stats')]
    public function showStats(EntityManagerInterface $entityManager): Response
    {
        $loggedLastMonth = $entityManager->getRepository(User::class)->findUsersLoggedInLastMonth();

        $usersData = array_map(function ($user) {
            return [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'last_login' => $user->getLastLogin() ? $user->getLastLogin()->format('Y-m-d H:i:s') : 'Never',
            ];
        }, $loggedLastMonth);
        
        $NotloggedLastMonth = $entityManager->getRepository(User::class)->findUsersNotLoggedInLastMonth();

        $users2Data = array_map(function ($user) {
            return [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'last_login' => $user->getLastLogin() ? $user->getLastLogin()->format('Y-m-d H:i:s') : 'Never',
            ];
        }, $NotloggedLastMonth );

        return $this->render('stats/show.html.twig', [
            'users' => $usersData,
            'users2' => $users2Data,
        ]);
    }
    
    #[Route('/graph', name: 'app_graph')]
    public function showGraph(EntityManagerInterface $entityManager): Response
    {   

        $yearInterval = new \DateInterval('P1Y');
        $monthInterval = new \DateInterval('P1M');
        $weekInterval = new \DateInterval('P1W');
        $dayInterval = new \DateInterval('P1D');


        $data = [
            'year' => [
                'visitors' => $entityManager->getRepository(Visit::class)->countVisit($yearInterval) + $entityManager->getRepository(User::class)->countUser($yearInterval),
                'quizz' => $entityManager->getRepository(History::class)->countQuizz($yearInterval),
            ],
             'month' => [
                'visitors' => $entityManager->getRepository(Visit::class)->countVisit($monthInterval)+ $entityManager->getRepository(User::class)->countUser($monthInterval),
                'quizz' => $entityManager->getRepository(History::class)->countQuizz($monthInterval),
            ],
             'week' => [
                'visitors' => $entityManager->getRepository(Visit::class)->countVisit($weekInterval)+ $entityManager->getRepository(User::class)->countUser($weekInterval),
                'quizz' => $entityManager->getRepository(History::class)->countQuizz($weekInterval),
            ],'day' => [
                'visitors' => $entityManager->getRepository(Visit::class)->countVisit($dayInterval) + $entityManager->getRepository(User::class)->countUser($dayInterval),
                'quizz' => $entityManager->getRepository(History::class)->countQuizz($dayInterval),
            ],'total' => [
                'visitors' => count($entityManager->getRepository(Visit::class)->findAll())+ count($entityManager->getRepository(User::class)->findAll()),
                'quizz' => count($entityManager->getRepository(History::class)->findAll()),
            ]];


            

   
        

        return $this->render('graph/show.html.twig', [
            'data' => $data
        ]);
    }
    
    
    #[Route('/sendemail/{id}', name: 'send_email')]
    public function sendEmail(EntityManagerInterface $entityManager,MailerInterface $mailer,$id): Response
    {   
       
        try {

            $user = $entityManager->getRepository(User::class)->find($id);

            $email = (new TemplatedEmail())
            ->from(new Address('no-reply@mailtrap.club', 'Send email'))
            ->to($user->getEmail())
            ->subject('Send email blabla')
            ->htmlTemplate('stats/send.html.twig');
            

            $mailer->send($email);

            return $this->render('sucess/index.html.twig', [
                'message' => 'the email was sent sucessfully for  ' . $id,
            ]);
        } catch (\Exception $e) {
            return $this->render('error/index.html.twig', [
                'message' => 'could not send email for id ' . $id,
            ]);
        }


        
    }
    
    
    
    
   


}