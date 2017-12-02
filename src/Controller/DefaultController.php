<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        die('Homepage ahoy!');
    }
}
