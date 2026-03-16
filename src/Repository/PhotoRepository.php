<?php

namespace App\Repository;

use App\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Photo>
 */
class PhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    public function findByTag(string $tagSlug): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.tags', 't')
            ->where('t.slug = :tagSlug')
            ->setParameter('tagSlug', $tagSlug)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findLatest(int $limit = 10): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findPhotosFromLast24Hours(): array
    {
        $twentyFourHoursAgo = new \DateTime('-24 hours');
        
        return $this->createQueryBuilder('p')
            ->where('p.createdAt >= :date')
            ->setParameter('date', $twentyFourHoursAgo)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findRecentPhotos(): array
    {
        $photosFromLast24Hours = $this->findPhotosFromLast24Hours();
        
        // If we have at least 10 photos from last 24h, use them
        if (count($photosFromLast24Hours) >= 10) {
            return $photosFromLast24Hours;
        }
        
        // Otherwise, return the latest 10 photos
        return $this->findLatest(10);
    }

    public function findMostLiked(int $limit = 10): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.likedBy', 'u')
            ->groupBy('p.id')
            ->orderBy('COUNT(u.id)', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
