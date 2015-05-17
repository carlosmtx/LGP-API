<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DeletableController extends Controller
{
    public function indexAction()
    {
        return new JsonResponse("oj");
    }
}