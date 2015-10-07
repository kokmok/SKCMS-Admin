<?php

namespace SKCMS\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ListController extends Controller
{
    public function indexAction($entity,$page)
    {
        
        $this->getRequest()->getSession()->set('_locale', $this->getRequest()->getDefaultLocale());
//        //dump($this->getRequest()->getLocale());
        
        $entitiesParams = $this->container->getParameter('skcms_admin.entities');
        
//        die(print_r($entitiesParams,true));
        
        $entityParams = $entitiesParams[$entity];
        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($entityParams['class']);
//        $repo->setDefaultLocale($this->getRequest()->getLocale());
        
        $entities = $repo->findAll($this->getRequest()->getLocale());
        
//        //dump($this->getRequest()->getLocale());
//        die();
        
        
        return $this->render('SKCMSAdminBundle:Page:list.html.twig',['entityParams'=>$entityParams,'entities'=>$entities]);
    }
    
    public function messagesAction($page)
    {
        
        $modulesParams = $this->container->getParameter('skcms_admin.modules');
        
        $contactParams = $modulesParams['contact'];
        
        if (!$contactParams['enabled'])
        {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }
        
//        die(print_r($entitiesParams,true));
        
        
        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($contactParams['messageEntity']['class']);
        
        $entities = $repo->findBy([],['date'=>'DESC']);
        
        $entityParams = $contactParams['messageEntity'];
        
        
        
        return $this->render('SKCMSAdminBundle:Page:contact-messages.html.twig',['entityParams'=>$entityParams,'entities'=>$entities]);
    }
}
