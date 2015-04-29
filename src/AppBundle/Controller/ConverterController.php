<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ConverterController extends Controller
{
    public function getStatusAction($uid)
    {
        return $this->render('AppBundle:Converter:status.html.twig', ['uid' => $uid]);
    }
}
