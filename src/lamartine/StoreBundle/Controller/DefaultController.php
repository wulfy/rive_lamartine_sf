<?php

namespace lamartine\StoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('lamartineStoreBundle:Default:index.html.twig', array('name' => $name));
    }
}
