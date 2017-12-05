<?php

namespace App\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Represents an Tenant.
 *
 * @ORM\Entity
 * @ORM\Table(name="tenants")
 * @UniqueEntity(fields="name", message="Organization name already taken")
 */
class Tenant extends Entity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     * @Assert\NotBlank()
     * @var string
     */
    private $name;

    /**
     * One Tenant has Many Users.
     * @ORM\OneToMany(targetEntity="User", mappedBy="tenant")
     * @var ArrayCollection
     */
    private $users;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return static
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param UserInterface $user
     * @return self
     */
    public function addUser(UserInterface $user)
    {
        $this->users[] = $user;
        return $this;
    }

    /**
     * @param  UserInterface $user
     * @return self
     */
    public function removeUser(UserInterface $user) {
        $this->users->removeElement($user);
        return $this;
    }
}
