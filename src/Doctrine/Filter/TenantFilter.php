<?php

namespace App\Doctrine\Filter;

use App\Entity\Tenant;
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

        $tenant = $this->getParameter(Tenant::class, null);
        if (!$tenant instanceof Tenant) {
            return ''; //returning empty string so that the query will not be modified
        }

        $query = sprintf('%s.%s = %s', $targetTableAlias, $class->getTenantFieldName(), $tenant->getId());

        return $query;
    }
}
