<?php

namespace App\Controller;

use App\Form\Type\UserType;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends Controller
{
    /**
     * @param Request $request
     * @param UserManager $userManager
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request, UserManager $userManager)
    {
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
}
