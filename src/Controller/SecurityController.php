<?php

namespace App\Controller;

use App\Service\UserManager;
use App\Form\Type\UserType;
use App\Form\Type\ChangePasswordType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class SecurityController extends Controller
{
    /**
     * @param Request $request
     * @param AuthenticationUtils $authUtils
     * @param AuthorizationChecker $authChecker
     * @return Response
     *
     * @Route("/login", name="login")
     * @Route("/logout", name="logout")
     *
     * We are defining two routes for this method.
     * Logout is a stub route as it is catched by event, this method is never triggered on logout.
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils, AuthorizationChecker $authChecker)
    {
        if (true === $authChecker->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('homepage');
        }

        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }


    /**
     * @param Request $request
     * @param UserManager $userManager
     * @param AuthorizationChecker $authChecker
     *
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request, UserManager $userManager, AuthorizationChecker $authChecker)
    {
        if (true === $authChecker->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('homepage');
        }

        $user = $userManager->createUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->save($user);
            return $this->redirectToRoute('login');
        }

        return $this->render(
            'registration/register.html.twig', ['form' => $form->createView()]
        );
    }

    /**
     * Change user password.
     *
     * @param Request $request
     * @param UserManager $userManager
     * @return Response
     *
     * @Route("/change-password", name="change_password")
     */
    public function changePasswordAction(Request $request, UserManager $userManager)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->save($user);
            return $this->redirectToRoute('homepage');
        }
        return $this->render('security/change_password.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
