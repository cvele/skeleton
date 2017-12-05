<?php

namespace App\Controller;

use App\Form\Type\UserType;
use App\Form\Type\ChangePasswordType;
use App\Service\CommandBus\Command\RegisterUserCommand;
use App\Service\CommandBus\Command\ChangePasswordCommand;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use League\Tactician\CommandBus;

class SecurityController extends Controller
{
    /** @var CommandBus **/
    private $commandBus;

    /**
     * @param CommandBus $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

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

        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @param Request $request
     * @param AuthorizationChecker $authChecker
     *
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request, AuthorizationChecker $authChecker)
    {
        if (true === $authChecker->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('homepage');
        }

        $user = new User;
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $command = new RegisterUserCommand($user);
            $this->commandBus->handle($command);
            return $this->render('registration/register_confirm.html.twig');
        }

        return $this->render(
            'registration/register.html.twig', ['form' => $form->createView()]
        );
    }

    /**
     * Change user password.
     *
     * @param Request $request
     * @return Response
     *
     * @Route("/change-password", name="change_password")
     */
    public function changePasswordAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $command = new ChangePasswordCommand($user->getEmail(), $user->getPlainPassword());
            $this->commandBus->handle($command);
            return $this->redirectToRoute('homepage');
        }
        return $this->render('security/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
