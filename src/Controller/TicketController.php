<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\TicketRepository;


class TicketController extends AbstractController
{
    /**
     * @Route("/ticket/new", name="newTicketPath")
     */
    public function new(EntityManagerInterface $manager)
    {
        if(isset($_POST['ticketMessage'])){
             $ticket = new Ticket();

             $ticket->setUsername($_POST['username']);

             $ticket->setContent($_POST['ticketMessage']);

             $ticket->setFoneNumber($_POST['ticketPhoneNumber']);

             $ticket->setMail($_POST['ticketMail']);

             $manager->persist($ticket);

             $manager->flush();
        }

        return new JsonResponse(['ticket' => 'ok']);

     }




    /**
     * @Route("/ticket/show/all/pending", name="showAllPendingTicketsPath")
     */
    public function showPending(){        

        $repo = $this->getDoctrine()->getRepository(Ticket::class);

        $tickets = $repo->findBy(
            ['status' => 'pending']
        );
      

        return $this->render('ticket/showallPending.html.twig', ['tickets' => $tickets]);


    }

    
    /**
     * @Route("/ticket/show/all/done", name="showAllDoneTicketsPath")
     */


    public function showDone(){        

        $repo = $this->getDoctrine()->getRepository(Ticket::class);

        $tickets = $repo->findBy(
            ['status' => 'done']
        );
      

        return $this->render('ticket/showallDone.html.twig', ['tickets' => $tickets]);


    }



    /**
     * @Route("/ticket/setToDone/{ticketId}", name="setTicketToDonePath")
     */


    public function setTicketToDone($ticketId, EntityManagerInterface $manager){     

        $repo = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $repo->find($ticketId);
        $ticket->setStatus('done');

        $manager->persist($ticket);
        $manager->flush();


       return $this->redirectToRoute('showAllPendingTicketsPath');

    }

    
    /**
     * @Route("/ticket/setToPending/{ticketId}", name="setTicketToPendingPath")
     */


    public function setTicketToPending($ticketId, EntityManagerInterface $manager){     

        $repo = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $repo->find($ticketId);
        $ticket->setStatus('pending');

        $manager->persist($ticket);
        $manager->flush();


       return $this->redirectToRoute('showAllDoneTicketsPath');

    }

    



    /**
     * @Route("/ticket/delete/{ticketId}/{ticketStatus}", name="deleteTicketPath")
     */

    function deleteTicketPath(EntityManagerInterface $manager, $ticketId, $ticketStatus){

        $ticket = $this->getDoctrine()->getRepository(Ticket::class)->find($ticketId);

          
        $manager->remove($ticket);

        $manager->flush();

        if($ticketStatus == 'done'){

            return $this->redirectToRoute('showAllDoneTicketsPath');
        } else{
            
            return $this->redirectToRoute('showAllPendingTicketsPath');


        }
    


    }






}
