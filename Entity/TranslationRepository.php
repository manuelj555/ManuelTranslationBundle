<?php

namespace ManuelAguirre\Bundle\TranslationBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TranslationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TranslationRepository extends EntityRepository
{
    public function getAllQueryBuilder($search = null, $domain = null, $onlyNews = null,
        $onlyConflicted = null, $onlyChanged = null)
    {
        $query = $this->createQueryBuilder('translation')
            ->select('translation, values')
            ->leftJoin('translation.values', 'values')
            ->orderBy('translation.domain,translation.code');

        if (null !== $search) {
            $part = $query->expr()->orX()
                ->add('translation.code LIKE :search');
//                ->add('values.value LIKE :search');

            $query->andWhere($part)
                ->setParameter('search', "%$search%");
        }

        if (null !== $domain) {
            $query->andWhere('translation.domain IN (:domain)')
                ->setParameter('domain', $domain);
        }

        if (null != $onlyNews) {
            $query->andWhere('translation.new = true');
        }

        if (null != $onlyConflicted) {
            $query->andWhere('translation.conflicts = true');
        }

        if (null != $onlyChanged) {
            $query->andWhere('translation.localEditions > 0');
        }

        return $query;
    }

    public function getAll($search = null, $domain = null, $onlyNews = null,
        $onlyConflicted = null, $onlyChanged = null)
    {
        return $this->getAllQueryBuilder($search, $domain, $onlyNews, $onlyConflicted, $onlyChanged)
            ->getQuery()
            ->getArrayResult();
    }

    public function getAllChanged()
    {
        return $this->getAllQueryBuilder(null, null, null)
            ->andWhere('translation.localEditions > 0')
            ->getQuery()
            ->getArrayResult();
    }

    public function getAllChangedWithoutConflicts()
    {
        return $this->getAllQueryBuilder(null, null, null)
            ->andWhere('translation.localEditions > 0')
            ->andWhere('translation.conflicts = false')
            ->getQuery()
            ->getResult();
    }

    public function getAllWithoutConflicts()
    {
        return $this->getAllQueryBuilder(null, null, null)
            ->andWhere('translation.conflicts = false')
            ->getQuery()
            ->getResult();
    }

    public function getAllEntities($search = null, $domain = null, $onlyNews = null)
    {
        return $this->getAllQueryBuilder($search, $domain, $onlyNews)
            ->getQuery()
            ->getResult();
    }

    public function getTranslationsByLocale($locale)
    {
        return $this->createQueryBuilder('translation')
            ->select('translation.code, translation.domain, values.value')
            ->join('translation.values', 'values')
            ->orderBy('translation.code')
            ->where('values.locale = :locale')
            ->setParameter('locale', $locale)
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
        $entity->setTimestamp(time());
        $entity->setLocalEditions($entity->getLocalEditions() + 1);

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

    /**
     * Determina que el archivo de traducciones .doctrine sea aun más nuevo que la ultima edición en la base de datos.
     */
    public function isFresh($timestamp)
    {
        return $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->where('t.timestamp > :file_time')
            ->setParameter('file_time', $timestamp)
            ->getQuery()
            ->getSingleScalarResult() == 0;
    }

//    public function updateFromArray($data, $synchronizations)
//    {
//        return $this->createQueryBuilder('t')
//            ->update('ManuelTranslationBundle:Translation', 't')
//            ->set('t.timestamp', time())
//            ->set('t.localEditions', 0)
//            ->set('t.synchronizations', $synchronizations)
//            ->set('t.', time())
//            ;
//    }
}
