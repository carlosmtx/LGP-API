<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function loginAction(Request $request){

        $username = $request->request->get('username');
        $password = $request->request->get('password');

        $targetName = $this->container->getParameter('api_user');
        $targetPass = $this->container->getParameter('api_pass');


        if($targetName === $username && $password === $targetPass){
            $request->getSession()->set('loggedin',true);
        }else {
            return new Response('Loggin Error');
        }

        return new JsonResponse([]);
    }

    public function logoutAction(Request $request){
        $request->getSession()->set('loggedin',false);
    }
}