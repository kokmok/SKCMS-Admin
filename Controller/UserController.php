<?php

namespace SKCMS\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    public function editAction($id)
    {
        $userParams = $this->getUserConfig();
        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($userParams['userEntity']['bundle'].'Bundle:'.$userParams['userEntity']['name']);
       
        if ($id!==null)
        {
            $entity = $repo->find($id);
        }
        else
        {
            $entity = new $userParams['userEntity']['class'];
        }
        $user = $this->getUser();
        
        $form = $this->createForm(new $userParams['userEntity']['form']($user),$entity);
        
        if ($id !== null)
        {
            $form->remove('plainPassword');
        }
        
        
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
                'User Edited :)'
                );
              $url = $this->generateUrl('sk_admin_userlist');
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
        
        $siteInfo = $this->container->getParameter('skcms_admin.siteinfo');
        
        
        return $this->render('SKCMSAdminBundle:Page:user-edit.html.twig',['entityParams'=>$userParams['userEntity'],'entity'=>$entity,'form'=>$form->createView(),'siteinfo'=>$siteInfo]);
//        return $this->render('SKCMSAdminBundle:Page:user-edit.html.twig',['entityParams'=>$userParams['userEntity'],'entity'=>$entity,'form'=>$form->createView()]);
    }
    
    public function listAction()
    {
        $userParams = $this->getUserConfig();
        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($userParams['userEntity']['bundle'].'Bundle:'.$userParams['userEntity']['name']);
        
        $entities = $repo->findBy([],['username'=>'ASC']);
        
        $entityParams = $userParams['userEntity'];
        
        
        
        return $this->render('SKCMSAdminBundle:Page:user-list.html.twig',['entityParams'=>$entityParams,'entities'=>$entities]);
    }
    
    private function getUserConfig()
    {
        $modulesParams = $this->container->getParameter('skcms_admin.modules');
        return $modulesParams['user'];
    }
}
