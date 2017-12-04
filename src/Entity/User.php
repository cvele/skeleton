<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity()
 * @UniqueEntity(fields="email", message="Email already taken")
 */
class User implements UserInterface, \Serializable
{
    /**
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;

    /**
     * Adds fromArray method, for easy hydration
     */
    use Traits\EntityHydrationTrait;

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
     * @ORM\Column(type="string", length=128, nullable=true)
     * @var string
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @var string
     */
    private $emailCanonical;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @var string
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @var string
     */
    private $lastname;

    /**
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     * @var bool
     */
    private $active = true;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $confirmationToken;

    /**
     * @ORM\Column(name="is_confirmed", type="boolean", nullable=false)
     * @var bool
     */
    private $confirmed = false;

    /**
     *
     * @ORM\OneToMany(targetEntity="Address", mappedBy="user")
     * @var ArrayCollection
     */
    private $addresses;

    /** construct **/
    public function __construct()
    {
         $this->addresses = new ArrayCollection();
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

    /**
     * @return string
     */
    public function getEmailCanonical()
    {
        return $this->emailCanonical;
    }

    /**
     * @param string $emailCanonical
     * @return self
     */
    public function setEmailCanonical(string $emailCanonical)
    {
        $this->emailCanonical = $emailCanonical;
        return $this;
    }

    public function getSalt()
    {
        return null;
    }

    /**
     * @param string|null $salt
     * @return self
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return self
     */
    public function setPassword(string $password = null)
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
     * @return string
     */
    public function getFirstname()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $firstname
     * @return self
     */
    public function setFirstname(string $firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     * @return self;
     */
    public function setLastname(string $lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * @param string $confirmationToken
     */
    public function setConfirmationToken(string $confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;
        return $this;
    }

    /**
     * @param bool $active
     * @return self
     */
    public function setActive(bool $active = true)
    {
        $this->isActive = $active;
        return $this;
    }

    /**
     * @return bool
     */
    public function getActive() : bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $confirmed
     * @return self
     */
    public function setConfirmed(bool $confirmed = true)
    {
        $this->confirmed = $confirmed;
        return $this;
    }

    /**
     * @return bool
     */
    public function getConfirmed() : bool
    {
        return $this->confirmed;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /** We should erase credentials every time **/
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->email,
            $this->password,
        ]);
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

    /**
     * @return ArrayCollection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * @param Address $address
     * @return self
     */
    public function addAddress(Address $address)
    {
        $address->setUser($this);
        $this->addresses[] = $address;
        return $this;
    }

    /**
     * @param  Address $address
     * @return self
     */
    public function removeAddress(Address $address) {
        $this->addresses->removeElement($address);
        return $this;
    }
}
