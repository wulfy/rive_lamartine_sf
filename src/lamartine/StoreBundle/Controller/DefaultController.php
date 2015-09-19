<?php

namespace lamartine\StoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use lamartine\StoreBundle\Entity\Note;
use lamartine\StoreBundle\Entity\Users;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

	protected  $submitText;

	public function __construct()
    {
    	$this->submitText = "Add";
    }

    public function indexAction($name)
    {
        return $this->render('lamartineStoreBundle:Default:index.html.twig', array('name' => $name));
    }

    public function addUserAction(Request $request)
    {
    	$users = new Users();
    	$message = null;
    	$roles = $this->getParameter('roles');

    	$form = $this->createFormBuilder($users)
    			->add('username', 'text',array('attr' => array('placeholder' => "username")))
    			->add('password', 'password',array('attr' => array('placeholder' => "password")))
    			->add('email', 'email',array('attr' => array('placeholder' => "email")))
    			->add('roles', 'choice', [
									            'choices' => $roles,
									            'multiple' => true,
									            'expanded' => true
									        ])
                ->add($this->submitText, 'submit')
                ->getForm();

        //if($this->handleStoreRequest($form,$request))
         //	$message = "User successfully added";


        return $this->render('lamartineStoreBundle:Default:new.html.twig', array('form' => $form->createView(),'message' => $message));
    }

    public function addNoteAction(Request $request)
    {
    	$note = new Note();
    	$message = null;
    	$form = $this->createFormBuilder($note)
    			->add('date', 'date')
                ->add('img', 'text' , array('required' => false , 'attr' => array('placeholder' => "URL de l'image")))
                ->add('text', 'textarea', array('attr' => array('placeholder' => 'Texte html')))
                ->add($this->submitText, 'submit')
                ->getForm();

         if($this->handleStoreRequest($form,$request))
         	$message = "Note successfully added";


        return $this->render('lamartineStoreBundle:Default:new.html.twig', array('form' => $form->createView(),'message' => $message));
    }


    protected function handleStoreRequest($form,Request $request)
    {
    	$form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->get('doctrine')->getManager('notes');
            $note = $form->getData();
            $em->persist($note);
            $em->flush();
            
            return true;
        }

        return false;
    }


    
}
