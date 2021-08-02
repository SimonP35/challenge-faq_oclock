<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function findByTag($tag)
    {
        return $this->createQueryBuilder('q')
            ->innerJoin('q.tags', 't')
            ->andWhere('t = :tag')
            ->andWhere('q.isBlocked = false')
            ->setParameter('tag', $tag)
            ->getQuery()
            ->getResult();
    }

    public function updateAllOutdated()
    {
        $em = $this->getEntityManager();

        $query = $em->createQuery(
            'UPDATE App\Entity\Question q
            SET q.active=false
            WHERE
            (DATE_DIFF(CURRENT_DATE(), q.updatedAt) > 7
            OR (q.updatedAt IS NULL AND DATE_DIFF(CURRENT_DATE(), q.createdAt) > 7))
            AND q.active=true'
        );

        // Affichage de la requête SQL générée
        // echo $query->getSQL();

        // Retourne le nombre de lignes impactées par la requête
        // 0 si aucune lignes impactées
        return $query->getResult();
    }

    //? Correction cours :
    
    // /**
    //  * Deactivates outdated questions
    //  * 
    //  * -- Contraintes pour désactiver une question :
    //  * -- # La mise à jour de la question est dépassée
    //  * -- Date de mise à jour > 7j
    //  * -- # Si question jamais mise à jour, on se rabat sur la date de création
    //  * -- OU (Date de mise à jour vaut NULL ET Date de création > 7j)
    //  * -- # Et la question doit être active
    //  * SELECT *
    //  * FROM `question`
    //  * WHERE
    //  * (DATEDIFF(NOW(), updated_at) > 7 OR (updated_at IS NULL AND DATEDIFF(NOW(), created_at) > 7))
    //  * AND active=1
    //  */
    // public function updateAllOutdated()
    // {
    //     $em = $this->getEntityManager();

    //     // Fonctions natives Doctrine DQL
    //     // @link https://www.doctrine-project.org/projects/doctrine-orm/en/2.9/reference/dql-doctrine-query-language.html#dql-functions

    //     $query = $em->createQuery(
    //         'UPDATE App\Entity\Question q
    //         SET q.active=false
    //         WHERE
    //         (DATE_DIFF(CURRENT_DATE(), q.updatedAt) > 7
    //         OR (q.updatedAt IS NULL AND DATE_DIFF(CURRENT_DATE(), q.createdAt) > 7))
    //         AND q.active=true'
    //     );

    //     // Affichage de la requête SQL générée
    //     //echo $query->getSQL();

    //     // Retourne le nombre de lignes impactées par la requête
    //     // 0 si aucune lignes impactées
    //     return $query->getResult();
    // }

    
//    /**
//     * @return Question[] Returns an array of Question objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Question
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
