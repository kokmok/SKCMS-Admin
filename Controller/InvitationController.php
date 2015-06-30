<?php

namespace SKCMS\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InvitationController extends Controller
{
    
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $invitationRepo = $em->getRepository('SKCMSUserBundle:Invitation');
        $invitation = $invitationRepo->find($id);
        
        $em->remove($invitation);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add(
                'success',
                'Invitation '.$invitation->getEmail().' Deleted :)'
                );
        
        
        $url = $this->generateUrl('sk_admin_invitationlist');
        return $this->redirect($url);
       
    }
    
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        if (null === $id)
        {
            $invitation = new \SKCMS\UserBundle\Entity\Invitation();
        }
        else
        {
            $invitationRepo = $em->getRepository('SKCMSUserBundle:Invitation');
            $invitation = $invitationRepo->find($id);
        }
        
        
        $editor = $this->getUser();
        
        $invitation->setUser($editor);
        if(null === $invitation)
        {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Invitation not found');
        }
        
        
        $form = $this->createForm(new \SKCMS\UserBundle\Form\InvitationType(),$invitation);
        $form   ->remove('code')
                ->remove('user')
                ->remove('sent');
        
        
        $request = $this->get('request');
        // On vérifie qu'elle est de type POST
        if ($request->getMethod() == 'POST') 
        {
          
            $form->bind($request);
          
            if ($form->isValid()) 
            {   
              $em->persist($invitation);
              $em->flush();
              $this->get('session')->getFlashBag()->add(
                'success',
                'Invitation '.$invitation->getEmail().' Edited :)'
                );
              
              $url = $this->generateUrl('sk_admin_invitationlist');
              return $this->redirect($url);
            }
            else
            {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, Invitation '.$invitation->getEmail().' not edited, sorry, try again :/'
                );
            }
            
        }
        
        
        
        
        $params = array
                (
                    'form'=>$form->createView(),
                    'invitation'=>$invitation
                );
        
        return $this->render('SKCMSAdminBundle:Page:invitation-edit.html.twig',$params);
    }
    
    
    
    public function listAction()
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('SKCMSUserBundle:Invitation');
        
        $invitations = $repo->findAll();
        
        return $this->render('SKCMSAdminBundle:Page:invitation-list.html.twig',['entities'=>$invitations]);
    }
    
}
