<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use App\Form\ChangeEmailFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Form\EditUserFormType;
use App\Form\CreateUserFormType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    } 
    
    #[Route('/all_users', name: 'all_user')]
    public function showUsers(EntityManagerInterface $entityManager): Response
    {
        $all_user = $entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/all_user.html.twig', [
            'all_user' => $all_user,
        ]);
    }  
    #[Route('/user_delete/{id}', name: 'delete_user_verification')]
    public function DeleteUserVerification(EntityManagerInterface $entityManager,$id): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No User found for id '.$id
            );
        }
        return $this->render('admin/delete_user.html.twig', [
            'user' => $user,
        ]);
    } 

    #[Route('/user/delete/{id}', name: 'user_delete')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {   

        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No User found for id '.$id
            );
        }

        try {
            $entityManager->remove($user);

            $entityManager->flush();
            $this->addFlash('success', 'The user was deleted.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'The user could not be deleted.');
        }

           return $this->redirectToRoute('all_user');

    } 
    
    #[Route('/pass_user_admin/{id}', name: 'pass_user_admin')]
    public function pass_user_admin(EntityManagerInterface $entityManager, int $id): Response
    {   

        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No User found for id '.$id
            );
        }

        try {

            $user->setRoles(['ROLE_USER','ROLE_ADMIN']);
            $entityManager->persist($user);

            $entityManager->flush();
            $this->addFlash('success', 'The user was pass to admin');
        } catch (\Exception $e) {
            $this->addFlash('error', 'The user could not be set role admin');
        }

           return $this->redirectToRoute('all_user');

    }  


    #[Route('/pass_user_admin_verification/{id}', name: 'pass_user_admin_verification')]
    public function pass_user_admin_verification(EntityManagerInterface $entityManager, int $id): Response
    {   
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No User found for id '.$id
            );
        }

        return $this->render('admin/pass_user_admin_verification.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user,
        ]);

    } 

    #[Route('/user/create', name: 'user_create')]
    public function create(EntityManagerInterface $entityManager,Request $request,UserPasswordHasherInterface $userPasswordHasher): Response
    {   

        $form = $this->createForm(CreateUserFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = new User();

            

            $newEmail = $form->get('new_mail')->getData();
            
            // check if we have a email 
            if(!$newEmail){
              throw $this->createNotFoundException(
                    'Need a email for the user'
                );
            }

            if($newEmail){
                $user->setEmail($newEmail);

            }

            // check if we have a password submit 
            if(!$form->get('plainPassword')->getData()){
                 throw $this->createNotFoundException(
                    'Need a password for the user'
                );
            }

            if($form->get('plainPassword')->getData()){
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }
            

            $user->setVerified(true);
            

            // here we save the user fresh created 
            $entityManager->persist($user);
            $entityManager->flush();
            
            
            $this->addFlash('success', 'User created successfully.');

            return $this->redirectToRoute('all_user'); 
        }

        return $this->render('admin/create_user.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form->createView(),
        ]);

    } 
    
    #[Route('/user/edit/{id}', name: 'user_edit')]
    public function editUserAsAdmin($id,Request $request,EntityManagerInterface $entityManager,UserPasswordHasherInterface $userPasswordHasher): Response
    {   

        $form = $this->createForm(EditUserFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $entityManager->getRepository(User::class)->find($id);

            if (!$user) {
                throw $this->createNotFoundException(
                    'No User found for id '.$id
                );
            }

            $newEmail = $form->get('new_mail')->getData();
            // if we have a new email we set it 
            if($newEmail){
                $user->setEmail($newEmail);

            }
            // if have a new password we set it also need to hash the passowrd before of course
            if($form->get('plainPassword')->getData()){
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }

            // if verify is true we set the account to verify 
            if($form->get('verify')->getData()){
                $user->setVerified(true);
            }

            // here we save the modification we just made 
            $entityManager->persist($user);
            $entityManager->flush();
            
            
            $this->addFlash('success', 'User updated successfully.');

            return $this->redirectToRoute('all_user'); 
        }

        return $this->render('admin/edit_user.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form->createView(),
        ]);
    } 

    #[Route('/change_email', name: 'app_user_email')]
    public function edit(Request $request, TokenGeneratorInterface $tokenGenerator, MailerInterface $mailer): Response
    {   // create form 
        $form = $this->createForm(ChangeEmailFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $form->getData();

            $newEmail = $form->get('new_mail')->getData();

            $token = $tokenGenerator->generateToken();
            $session = $request->getSession();

            $session->set('email_change', [
                'new_email' => $newEmail,
                'token' => $token,
            ]);

            $confirmLink = $this->generateUrl('app_confirm_email_change', [
                'token' => $token,
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            $email = (new Email())
                ->from('no-reply@example.com')
                ->to($newEmail)
                ->subject('Email Change Confirmation')
                ->html('<p>Please confirm your email change by clicking <a href="' . $confirmLink  . '">this link</a>.</p>');

            $mailer->send($email);

            $this->addFlash('success', 'A confirmation email has been sent .');

            return $this->redirectToRoute('app_home'); 
        }

        return $this->render('change_email/request.html.twig', [
            'controller_name' => 'UserController',
            'requestForm' => $form->createView(),
        ]);
    }


    #[Route('/confirm_email_change/{token}', name: 'app_confirm_email_change')]
    public function confirmEmailChange(Security $security,Request $request,string $token,EntityManagerInterface $entityManager): Response
    {   
        $session = $request->getSession();

        $emailChangeData = $session->get('email_change');

        if (!$emailChangeData || $emailChangeData['token'] !== $token) {
            throw $this->createNotFoundException('Invalid token.');
        }

        $newEmail = $emailChangeData['new_email'];
        $user = $security->getUser();
        if(!$user){
            throw $this->createNotFoundException('Need to be login');

        }
        $user->setEmail($newEmail);

        $entityManager->persist($user);
        $entityManager->flush();

        $session->remove('email_change');

        return $this->render('change_email/success.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    
}
