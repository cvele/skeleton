<?php

namespace App\Service\Security;

use App\Service\CanonicalizerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class updating the canonical fields of the user.
 *
 * @author Vladimir Cvetic <vladimir@ferdinand.rs>
 */
class UserCanonicalFieldsUpdater
{
    /** @var CanonicalizerInterface **/
    private $emailCanonicalizer;

    /**
     * @param CanonicalizerInterface $emailCanonicalizer
     */
    public function __construct(CanonicalizerInterface $emailCanonicalizer)
    {
        $this->emailCanonicalizer = $emailCanonicalizer;
    }

    /**
     * @param  UserInterface $user
     * @return void
     */
    public function updateCanonicalFields(UserInterface $user)
    {
        $user->setEmailCanonical($this->canonicalizeEmail($user->getEmail()));
    }

    /**
     * Canonicalizes an email.
     *
     * @param string|null $email
     *
     * @return string|null
     */
    public function canonicalizeEmail($email)
    {
        return $this->emailCanonicalizer->canonicalize($email);
    }
}
