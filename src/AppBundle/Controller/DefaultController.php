<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Utils\Notes;
use Symfony\Component\HttpFoundation\RedirectResponse;
use lamartine\StoreBundle\Entity\Note;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class DefaultController extends Controller
{

    /**
     * @Route("/app", name="homepage")
     */
    public function indexAction()
    {

    	$notes1 = new Notes();
    	$notes1->date = "20-07-15";
    	$notes1->title = "Mise en place des plaques";
    	$notes1->img = "http://www.ppcaboutique.fr/images/porte/21812.jpg";
    	$notes1->text = "La pose des plaques portes d'entrée + boite aux lettres + configuration de l'interphone à défilement sont prévus en fin de semaine.<br/><br/>
        <b>ATTENTION</b> Seules les personnes qui ont envoyé <a href=\"#\">ce formulaire</a> par mail auront leur plaques + nom sur l'interphone.";

    	$notes2 = new Notes();
    	$notes2->date = "20-07-15";
    	$notes2->title = "Cartons!";
    	$notes2->img = "http://www.voyage-yukon.net/blog/wp-content/uploads/2012/04/cartons.jpg";
    	$notes2->text = "Pour rappel , il est interdit d'entreposer des cartons ou tout autre déchet dans le local à poubelle. Les poubelles sont réservées aux déchets ménagers et les cartons qui peuvent entrer dans les container recyclable. Les autres déchets doivent être jetés à la déchetterie de Sathonay camp (à 7min de fontaines).<br/>
        <img src='http://icon-park.com/imagefiles/location_map_pin_red6.png' class='mini'/>Itinéraire : <a href='#'> cliquez ici </a>";

        $notes = [];
        $notes[] = $notes1;
        $notes[] = $notes2;


        $notes = $this->getNotes();
        
        //die(var_dump($notes));
    	return $this->render('default/index.html.twig', array(
			'notes' => $notes,
		));
        //return $this->render('default/index.html.twig');
    }

    protected function getNotes()
    {

        $notes = $this->get('doctrine')
                         ->getManager('notes')
                         ->getRepository('lamartineStoreBundle:Note')
                         ->findAll();

        return $notes;
    }

    /**
     * @Route("/app/sendmail", name="mailer")
     * @Method({"POST"})
     */
    public function sendMailAction(Request $request)
    {
        $nomprenom = $request->request->get('username');
        $email = $request->request->get('usermail');
        $sujet = $request->request->get('subject');
        $texte = "hello" ;//$request->request->get('message');
       // die(mb_convert_encoding($message, 'utf-8', 'utf-8'));

        //$message = null;

        $a = \Swift_Message::newInstance()
        ->setSubject('Contact site :'.$sujet)
        ->setFrom($email)
        ->setTo('ludovic.lasry@gmail.com')
        ->setBody($this->renderView('default/email.txt.twig', array('nomprenom' => $nomprenom,'email' => $email, 'sujet' => $sujet, 'message' => $texte)),'text/html');
        //->setBody($this->renderView('HelloBundle:Hello:email.txt.twig', array('name' => $name)
        $this->get('mailer')->send($a);

        return $this->render('default/confirmation.twig', array('nomprenom' => $nomprenom,'email' => $email, 'sujet' => $sujet, 'message' => $texte));
    }

    /**
     * @Route("/app/newNote", name="notes")
     *
     */
    public function newNotes(Request $request)
    {

        // crée une tâche et lui donne quelques données par défaut pour cet exemple
        $note = new Note();
        $note->setDate(new \DateTime('today'));
        $submitText = "Add note";

        $form = $this->createFormBuilder($note)
            ->add('title', 'text')
            ->add('date', 'date')
            ->add('img', 'text')
            ->add('text', 'text')
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->get('doctrine')->getManager('notes');
            $note = $form->getData();
            $em->persist($note);
            $em->flush();
            
        }

        return $this->redirect($this->generateUrl('manage_notes'));
    }

    /**
     * @Route("/app/manage/notes", name="manage_notes")
     * @Route("/app/manage/notes/edit", name="edit_notes")
     */
    public function manageNotesAction(Request $request)
    {

        // crée une tâche et lui donne quelques données par défaut pour cet exemple
        $id = $request->query->get('id');
        $submitText = "Save";
        if(is_null($id))
        {
           $note = new Note();
           $note->setDate(new \DateTime('today'));

        }else
        {
            $em = $this->get('doctrine')->getManager('notes');
            $note = $em->getRepository('lamartineStoreBundle:Note')->find($id);
            $submitText = "update";
        }

       

        $form = $this->createFormBuilder($note)
            ->add('title', 'text',array('attr' => array('placeholder' => "Titre de l affiche")))
                ->add('date', 'date')
                ->add('img', 'text' , array('required' => false , 'attr' => array('placeholder' => "URL de l'image")))
                ->add('text', 'textarea', array('attr' => array('placeholder' => 'Texte html')))
                ->add($submitText, 'submit')
                ->getForm();

        $form->handleRequest($request);

                if ($form->isValid()) {
                    $em = $this->get('doctrine')->getManager('notes');
                    $note = $form->getData();
                    $em->persist($note);
                    $em->flush();
                    //return $this->redirect($this->generateUrl('task_success'));
                }

        $notes = $this->getNotes();

        return $this->render('admin/index.html.twig', array(
            'form' => $form->createView(),
            'notes' => $notes,
            'message' => null,
        ));

    }

    /**
     * @Route("/app/manage/notes/delete", name="delete_note")
     * @Method({"GET"})
     */
    public function deleteNoteAction(Request $request)
    {
        $id = $request->query->get('id');
        
       
        if($id >0)
        {
             $em = $this->get('doctrine')->getManager('notes');
             $note = $em->getRepository('lamartineStoreBundle:Note')->find($id);
             $em->remove($note);
             $em->flush();
         }

         return $this->redirect($this->generateUrl('manage_notes'));
    }

}
