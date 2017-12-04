<?php

namespace App\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents an Tenant.
 *
 * @ORM\Entity
 * @ORM\Table(name="tenants")
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
     * @ORM\Column(type="string", length=255)
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
    public function getName(): string
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
    public function getUsers(): ArrayCollection
    {
        return $this->users;
    }

    /**
     * @param ArrayCollection $users
     *
     * @return static
     */
    public function setUsers(ArrayCollection $users)
    {
        $this->users = $users;
        return $this;
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
        $user->add($this);
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
