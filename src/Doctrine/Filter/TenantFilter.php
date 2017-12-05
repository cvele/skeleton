<?php

namespace App\Doctrine\Filter;

use App\Entity\TenantAwareEntity;
use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;

class TenantFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        $class = $targetEntity->newInstance();
        if (!$class instanceof TenantAwareEntity) {
            return ''; //returning empty string so that the query will not be modified
        }

        if (null === $this->getParameter('tenant', null)) {
            return ''; //returning empty string so that the query will not be modified
        }

        $query = sprintf('%s.%s = %s', $targetTableAlias, 'tenant_id', $this->getParameter('tenant_id'));

        return $query;
    }
}
