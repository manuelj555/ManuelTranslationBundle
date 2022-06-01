<?php

namespace ManuelAguirre\Bundle\TranslationBundle\Entity;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use ManuelAguirre\Bundle\TranslationBundle\TranslationRepository as RepositoryInterface;

/**
 * TranslationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TranslationRepository extends ServiceEntityRepository implements RepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Translation::class);
    }

    public function getAllQueryBuilder($search = null, $domain = null, $inactives = false)
    {
        $query = $this->createQueryBuilder('translation')
            ->orderBy('translation.domain,translation.code, translation.active');

        if ($inactives) {
            $query->andWhere('translation.active = false');
        }

        if (null !== $search) {
            $part = $query->expr()->orX()
                ->add('translation.code LIKE :search')
                ->add('translation.values LIKE :search');

            $query->andWhere($part)
                ->setParameter('search', "%$search%");
        }

        if (null !== $domain) {
            $query->andWhere('translation.domain IN (:domain)')
                ->setParameter('domain', $domain);
        }

        return $query;
    }

    public function getAll(
        $search = null,
        $domain = null,
        $onlyConflicted = null,
        $onlyChanged = null
    ) {
        return $this->getAllQueryBuilder($search, $domain, $onlyConflicted, $onlyChanged)
            ->getQuery()
            ->getArrayResult();
    }

    public function getAllEntities($search = null, $domain = null)
    {
        return $this->getAllQueryBuilder($search, $domain)
            ->getQuery()
            ->getResult();
    }

    public function getActiveTranslations(): array
    {
        return $this->createQueryBuilder('translation')
            ->select('translation.code,
            translation.domain,
            translation.values,
            translation.hash
            ')
            ->orderBy('translation.code')
            ->andWhere('translation.active = true')
            ->getQuery()
            ->getArrayResult();
    }

    public function codeExists($code)
    {
        return $this->createQueryBuilder('t')
                ->select('COUNT(t.code)')
                ->where('t.code = :code')
                ->setParameter('code', $code)
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }

    public function saveTranslation(Translation $entity, $flush = true)
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getExistentDomains()
    {
        $result = $this->createQueryBuilder('t')
            ->select('t.domain')
            ->addGroupBy('t.domain')
            ->getQuery()
            ->getScalarResult();

        $domains = array();

        foreach ($result as $item) {
            $domains[$item['domain']] = $item['domain'];
        }

        return $domains;
    }

    public function inactiveByDomainAndCodes($domain, $codes)
    {
        return $this->createQueryBuilder('t')
            ->update('ManuelTranslationBundle:Translation', 't')
            ->set('t.active', 'false')
            ->where('t.domain = :domain')
            ->andWhere('t.code IN (:codes)')
            ->andWhere('t.active = true')
            ->setParameter('domain', $domain)
            ->setParameter('codes', (array)$codes)
            ->getQuery()
            ->execute();
    }

    public function getOneArrayByCodeAndDomain($code, $domain)
    {
        return $this->createQueryBuilder('t')
            ->select('t')
            ->where('t.domain = :domain')
            ->andWhere('t.code = :code')
            ->setParameter('domain', $domain)
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }
}
