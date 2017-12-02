<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity()
 * @UniqueEntity(fields="email", message="Email already taken")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @var string
     */
    private $password;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     * @var string
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     * @var bool
     */
    private $isActive;

    /** construct **/
    public function __construct()
    {
        $this->isActive = true;
    }

    public function getUsername()
    {
        /** We are faking username field, required by interface **/
        return $this->email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return self
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
        return $this;
    }

    /** This is a stub method, required by interface. We don't use it with bcrypt **/
    public function getSalt()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return self
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $password
     */
    public function setPlainPassword(string $password)
    {
        $this->plainPassword = $password;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /** We should erase credentials every time password is updated **/
    public function eraseCredentials()
    {
        $this->plainPassword = null;
        $this->password = null;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     * @param string $serialized
     * @return array
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
        ) = unserialize($serialized);
    }
}
