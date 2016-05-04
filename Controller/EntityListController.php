<?php

namespace SKCMS\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \SKCMS\CoreBundle\Entity\PageTemplate;
use SKCMS\CoreBundle\Form\PageTemplateType;

class EntityListController extends Controller
{
    public function editAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) 
        {
            throw new \Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException('Only Super Admin have access to this');
        }
        
        $user = $this->getUser();
        
                
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SKCMSCoreBundle:EntityList');
       
        if ($id!==null)
        {
            $entity = $repo->find($id);
        }
        else
        {
            $entity = new \SKCMS\CoreBundle\Entity\EntityList();
        }
        
        $form = $this->createForm(new \SKCMS\CoreBundle\Form\EntityListType($this->container->getParameter('skcms_admin.entities'),$this->container->getParameter('skcms_admin.modules')),$entity);
        
        
        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') 
        {
          
            $form->bind($request);
          
            if ($form->isValid()) 
            {
              $em->persist($entity);
              $em->flush();
              $this->get('session')->getFlashBag()->add(
                'success',
                'list Edited :)'
                );
              $url = $this->generateUrl('sk_admin_entitylistlist');
              return $this->redirect($url);
            }
            else
            {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, list not edited, sorry, try again :/'
                );
            }
            
        }
        $siteInfo = $this->container->getParameter('skcms_admin.siteinfo');
        
        return $this->render('SKCMSAdminBundle:Page:entitylist-edit.html.twig',['entity'=>$entity,'form'=>$form->createView(),'siteinfo'=>$siteInfo]);
    }
    
    public function listAction()
    {
        
        if (!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) 
        {
            throw new \Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException('Only Super Admin have access to this');
        }
        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SKCMSCoreBundle:EntityList');
        
        $entities = $repo->findAll();
        
        return $this->render('SKCMSAdminBundle:Page:entitylist-list.html.twig',['entities'=>$entities]);
    }
    
}
