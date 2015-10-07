<?php

namespace SKCMS\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class AbstractEditController extends Controller
{
    
    const REPOSITORY = '';
    const ENTITY = '';
    const FORM = '';
    const PATH = '';
    const TEMPLATE = '';
    
    private $locale;
    
    
    public function editAction($entityClass,$repository,$formClass,$locale,$id = null,$sort = null,$formParams = [])
    {
        $user = $this->getUser();
        $request = $this->get('request');
        
        $em = $this->getDoctrine()->getManager();
        
        $repo = $em->getRepository($repository);
       
        if ($id!==null)
        {
            $entity = $repo->findForAdmin($id,$locale);

        }
        else
        {
            $entity = new $entityClass;
            $entity->setUserCreate($user);
            
        }
        
        $entity->setUserUpdate($user);
        $entity->setUpdateDate(new \DateTime());
        $entity->setTranslatableLocale($locale);
        

        
        if (isset($formParams) && count($formParams))
        {
            if (isset($formParams['user']) && $formParams['user'] == 'current')
            {
                $form = $this->createForm(new $formClass($user),$entity);
            }
            if (isset($formParams['method']))
            {
                $class = new $formParams['class'];
                $formParams = call_user_func( array( $class, $formParams['method'] ) );
//                die('t'.$formParams);
                $form = $this->createForm(new $formClass($formParams),$entity);
            }
        }
        else
        {
            $form = $this->createForm(new $formClass,$entity);
        }
        
        
        
        if ($request->getMethod() == 'POST') 
        {
          
            $form->bind($request);
          
            if ($form->isValid()) 
            {
              
                
                if (isset($sort) && $sort!="none" && $entity->getId() === null)
                {
                    
                    $newPosition = $repo->findLastSortableIndex($sort)+1;
//                    call_user_method('set'.ucfirst($entityParams['sort']), $this,[$newPosition]);
                    call_user_func([$entity,'set'.ucfirst($sort)],$newPosition);
                }
               
              $em->persist($entity);
              $em->flush();
              
              
              
              $this->get('session')->getFlashBag()->add(
                'success',
                'Entity Edited :)'
                );
              
                if ($request->request->get('stay_here') !== null)
                {
                    $url = $this->generateUrl($request->get('_route'),array_merge($request->get('_route_params'),['id'=>$entity->getId()]));
                }
                elseif ($request->request->get('add_new') !== null)
                {
                    $params = $request->get('_route_params');
                    $url = $this->generateUrl('sk_admin_edit',['entity'=>$entityParams['name'],'_locale'=>$this->container->getParameter('locale')]);
                }
                else
                {
                    $url = $this->generateUrl('sk_admin_list',['entity'=>$entityParams['name']]);
                }
              
              return $this->redirect($url);
            }
            else
            {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, '.$entityParams['beautyName'].' not edited, sorry, try again :/'
                );
            }
            
        }
        
        $siteInfo = $this->container->getParameter('skcms_admin.siteinfo');
        
        
        return $this->render('SKCMSAdminBundle:Page:edit.html.twig',['entityParams'=>$entityParams,'entity'=>$entity,'form'=>$form->createView(),'siteinfo'=>$siteInfo]);
    }
    
    public function indexAction($entity,$id,$_locale)
    {
        $locale = $_locale;
        $request = $this->get('request');
        
        $user = $this->getUser();
        $entitiesParams = $this->container->getParameter('skcms_admin.entities');
        
        $entityParams = $entitiesParams[$entity];
        
        
        $repository = $entityParams['class'];
        $entityClass = $entityParams['class'];
        $formClass = $entityParams['form'];
        $formParams = $entityParams['formParams'];
        
       
        $em = $this->getDoctrine()->getManager();
       
    }
    
    public function deleteAction($entity,$id)
    {
        $user = $this->getUser();
        $entitiesParams = $this->container->getParameter('skcms_admin.entities');
        
        $entityParams = $entitiesParams[$entity];
        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($entityParams['class']);
        $entity = $repo->find($id);
        
        
        $em->remove($entity);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add(
                    'success',
                    $entityParams['beautyName'].' deleted'
                );
        
        $url = $this->generateUrl('sk_admin_list',['entity'=>$entityParams['name']]);
        return $this->redirect($url);
        
    }
    
    public function pullDownAction($entity,$id,$_locale)
    {
        $locale = $_locale;
        
        $entitiesParams = $this->container->getParameter('skcms_admin.entities');
        $entityParams = $entitiesParams[$entity];
        $em = $this->getDoctrine()->getManager();
        
        $repo = $em->getRepository($entityParams['class']);
       
        $entity = $repo->find($id,$locale);
        
        $position = call_user_method('get'.ucfirst($entityParams['sort']), $entity);
        
        $nextEntity = $repo->findNextEntity($entityParams['sort'],$position);
        
        $nextPotition = call_user_method('get'.ucfirst($entityParams['sort']), $nextEntity);
        call_user_method('set'.ucfirst($entityParams['sort']), $entity,$nextPotition);
        call_user_method('set'.ucfirst($entityParams['sort']), $nextEntity,$position);
        
        
        
        $em->persist($entity);
        $em->persist($nextEntity);
        
        $em->flush();
        
        return new \Symfony\Component\HttpFoundation\JsonResponse(['status'=>1]);
    }
    
    public function pushUpAction($entity,$id,$_locale)
    {
        $locale = $_locale;
        
        $entitiesParams = $this->container->getParameter('skcms_admin.entities');
        
        $entityParams = $entitiesParams[$entity];
        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($entityParams['class']);
       
        $entity = $repo->find($id,$locale);
        
        $position = call_user_method('get'.ucfirst($entityParams['sort']), $entity);
        
        $prevEntity = $repo->findPrevEntity($entityParams['sort'],$position);
        

        
        $prevPotition = call_user_method('get'.ucfirst($entityParams['sort']), $prevEntity);
        
//        
        call_user_method('set'.ucfirst($entityParams['sort']), $entity,$prevPotition);
        call_user_method('set'.ucfirst($entityParams['sort']), $prevEntity,$position);
//        call_user_method
        
        
        
        $em->persist($entity);
        $em->persist($prevEntity);
        
        $em->flush();
        
        return new \Symfony\Component\HttpFoundation\JsonResponse(['status'=>1]);
        
    }
}
