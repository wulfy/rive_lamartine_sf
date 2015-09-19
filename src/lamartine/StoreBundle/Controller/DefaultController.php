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

    protected function getRoles()
    {
    	$roles = $this->getParameter('roles');
    	$roles_choice_array = [];
    	foreach ($roles as $role) {
    		$roles_choice_array[$role] = $role;
    	}

    	return $roles_choice_array;
    }

    public function addUserAction(Request $request)
    {
    	$users = new Users();
    	$message = null;
    	$roles = $this->getRoles();

    	
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

        $form->handleRequest($request);
        if($form->isValid())
        {
        	$data = $form->getData();
        	$encoder = $this->container->get('security.password_encoder');
			$encoded = $encoder->encodePassword($data, $data->getPassword());
			$data->setPassword($encoded);

			if($this->storeData($data))
         		$message = "User successfully added";
        }

        


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

        $form->handleRequest($request);
        if($form->isValid())
        {
        	$data = $form->getData();

			if($this->storeData($data))
         		$message = "Note successfully added";
        }


        return $this->render('lamartineStoreBundle:Default:new.html.twig', array('form' => $form->createView(),'message' => $message));
    }


    protected function storeData($data)
    {
            $em = $this->get('doctrine')->getManager('notes');
            $em->persist($data);
            $em->flush();
            
            return true;
    }


    
}
