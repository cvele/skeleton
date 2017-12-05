<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\MappedSuperclass */
class TenantAwareEntity extends Entity
{
    /**
     * Many Objects have One Tenant.
     * @ORM\ManyToOne(targetEntity="Tenant", cascade={"persist"})
     * @ORM\JoinColumn(name="tenant_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * @var Tenant
     */
    private $tenant;

    /**
     * @return Tenant
     */
    public function getTenant()
    {
        return $this->tenant;
    }

    /**
     * @param Tenant $tenant
     * @return self
     */
    public function setTenant(Tenant $tenant)
    {
        $this->tenant = $tenant;
        return $this;
    }
}
