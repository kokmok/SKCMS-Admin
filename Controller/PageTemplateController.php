<?php

namespace SKCMS\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \SKCMS\CoreBundle\Entity\PageTemplate;
use SKCMS\CoreBundle\Form\PageTemplateType;

class PageTemplateController extends Controller
{
    public function editAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) 
        {
            throw new \Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException('Only Super Admin have access to this');
        }
        
        $user = $this->getUser();
        
                
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SKCMSCoreBundle:PageTemplate');
       
        if ($id!==null)
        {
            $entity = $repo->find($id);
        }
        else
        {
            $entity = new PageTemplate();
        }
        
        $form = $this->createForm(new PageTemplateType(),$entity);
        
        
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
                'template Edited :)'
                );
              $url = $this->generateUrl('sk_admin_pagetemplatelist');
              return $this->redirect($url);
            }
            else
            {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, User not edited, sorry, try again :/'
                );
            }
            
        }
        
        
        return $this->render('SKCMSAdminBundle:Page:pagetemplate-edit.html.twig',['entity'=>$entity,'form'=>$form->createView()]);
    }
    
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SKCMSCoreBundle:PageTemplate');
        
        $entities = $repo->findAll();
        
        return $this->render('SKCMSAdminBundle:Page:pagetemplate-list.html.twig',['entities'=>$entities]);
    }
    
}
