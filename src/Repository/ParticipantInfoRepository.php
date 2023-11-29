<?php

namespace App\Repository;

use App\Entity\ParticipantInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ParticipantInfo>
 *
 * @method ParticipantInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParticipantInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParticipantInfo[]    findAll()
 * @method ParticipantInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipantInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParticipantInfo::class);
    }

//    /**
//     * @return ParticipantInfo[] Returns an array of ParticipantInfo objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ParticipantInfo
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
