<?php

namespace App\Repository;

use App\Entity\OvertimeRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OvertimeRequest>
 *
 * @method OvertimeRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method OvertimeRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method OvertimeRequest[]    findAll()
 * @method OvertimeRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OvertimeRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OvertimeRequest::class);
    }

    public function save(OvertimeRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(OvertimeRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return OvertimeRequest[] Returns an array of OvertimeRequest objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?OvertimeRequest
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
