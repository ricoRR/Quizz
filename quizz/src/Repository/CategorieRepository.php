<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Question;
use App\Entity\Reponse;

/**
 * @extends ServiceEntityRepository<Categorie>
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

//    /**
//     * @return Categorie[] Returns an array of Categorie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Categorie
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

        public function deleteCategorie($id): void {

            $entityManager = $this->getEntityManager();

            $query = $entityManager->createQuery(
                'SELECT a 
                FROM App\Entity\Question a
                WHERE a.id_categorie = :id'
            )->setParameter('id', $id);

            $questions = $query->getResult();
            

            foreach ($questions as $question) {
                $conn = $this->getEntityManager()->getConnection();
                
                $sql = '
                SELECT * FROM reponse p
                WHERE p.id_question = :id';

                $responses = $conn->executeQuery($sql, ['id' => $question->getId()]);
                
        
                foreach ($responses as $response) {
                    $entityManager->remove($response);
                }
                $entityManager->flush(); 
            }

            foreach ($questions as $question) {
                $entityManager->remove($question);
            }
            $entityManager->flush(); 

            $category = $entityManager->getRepository('App\Entity\Categorie')->find($id);
            var_dump($category);
            $entityManager->remove($category);
            $entityManager->flush();
            
         

        
       
        } 
        
        // public function getCategorie($id): array {

        //     $entityManager = $this->getEntityManager();

        //     $query = $entityManager->createQuery(
        //         'SELECT a 
        //         FROM App\Entity\Question a
        //         WHERE a.id_categorie = :id'
        //     )->setParameter('id', $id);

        //     $questions = $query->getResult();
            
        //     $all_responses = array();

        //     foreach ($questions as $question) {
        //         $conn = $this->getEntityManager()->getConnection();
                
        //         $sql = '
        //         SELECT * FROM reponse p
        //         WHERE p.id_question = :id';

        //         $responses = $conn->executeQuery($sql, ['id' => $question->getId()]);
                
        //         array_push($all_responses,$responses);
                
        //     }

             

        //     $category = $entityManager->getRepository('App\Entity\Categorie')->find($id);
            


        //     return ['category'=>$category,'questions'=>$questions,'reponses'=>$all_responses];
            
         

        
       
        // }


        public function findByCategorie($id): array
        {
            $entityManager = $this->getEntityManager();

            $category = $entityManager->getRepository(Categorie::class)->find($id);

            if (!$category) {
                throw $this->createNotFoundException('No category found for id ' . $id);
            }

            $questions = $entityManager->getRepository(Question::class)->findBy(['categorie' => $id]);

            foreach ($questions as $question) {
                $responses = $entityManager->getRepository(Reponse::class)->findBy(['question' => $question->getId()]);
                foreach ($responses as $response) {
                    $question->addResponse($response); 
                }
            }

            return ['category' => $category, 'questions' => $questions];
            }




}
