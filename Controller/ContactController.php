<?php

namespace SKCMS\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ContactController extends Controller
{
    public function messageViewAction($id)
    {
        $contactParams = $this->getContactConfig();
        $user = $this->getUser();
        if (!$contactParams['enabled'])
        {
            return new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }
                
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($contactParams['messageEntity']['class']);
        
        $entity = $repo->find($id);
        
        
        if (null === $entity)
        {
            return new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('This message does not exists');
        }
        if ($entity->getStatus() ==\SKCMS\ContactBundle\Entity\ContactMessage::STATUS_NEW)
        {
            $entity->setStatusViewed();
        }
        
        
        $response = new \SKCMS\ContactBundle\Entity\ContactMessageResponse();
        $response->setContactMessageId($id);
        $response->setUser($user);
        $responseForm = $this->createForm(new \SKCMS\ContactBundle\Form\ContactMessageResponseType(),$response);
        $request = $this->getRequest();
        $session = $this->container->get('session');
        
        if ($request->getMethod() == 'POST') 
        {
          
            $responseForm->bind($request);
          
            if ($responseForm->isValid()) 
            {
                $entity->setStatusAnswered();
                $em->persist($response);
                $em->persist($entity);
                $em->flush();
                
                $message = \Swift_Message::newInstance()
                    ->setSubject('Une réponse à votre message')
                    ->setTo($entity->getEmail())
                    ->setFrom($this->container->getParameter('mailer_user'))
                    ->setBody($this->renderView('SKCMSContactBundle:Mail:response.html.twig', array('body' => $response->getMessage())),'text/html')
                ;
                $this->get('mailer')->send($message);
                
                
                $session->getFlashBag()->add(
                  'success',
                  'message_sent'
                  );
                
                $url =$this->generateURl('sk_admin_messageview',['id'=>$id]);
                return $this->redirect($url);
            
            }
            else
            {
                $session->getFlashBag()->add(
                'success',
                'message_not_sent'
                );
            }
        }
        
        $responseRepo = $em->getRepository('SKCMSContactBundle:ContactMessageResponse');
        $responses = $responseRepo->findByContactMessageId($id,['date'=>'DESC']);
        
        $em->persist($entity);
        $em->flush();
        
        return $this->render('SKCMSAdminBundle:Page:contact-message-view.html.twig',['form'=>$responseForm->createView(),'message'=>$entity,'responses'=>$responses]);
        
    }
    
    public function messagesAction($page)
    {
        
        $contactParams = $this->getContactConfig();
        
        if (!$contactParams['enabled'])
        {
            return new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }
        
        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($contactParams['messageEntity']['class']);
        
        $entities = $repo->findBy([],['date'=>'DESC']);
        
        $entityParams = $contactParams['messageEntity'];
        
        
        
        return $this->render('SKCMSAdminBundle:Page:contact-messages.html.twig',['entityParams'=>$entityParams,'entities'=>$entities]);
    }
    
    private function getContactConfig()
    {
        $modulesParams = $this->container->getParameter('skcms_admin.modules');
        return $modulesParams['contact'];
    }
}
