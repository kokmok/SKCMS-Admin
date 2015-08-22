<?php

namespace SKCMS\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EshopController extends Controller
{
    public function editAction($orderId,  \Symfony\Component\HttpFoundation\Request $request)
    {
        $eshopParams = $this->getModuleConfig();
        if (!$eshopParams['enabled'])
        {
            return new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }
        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SKCMSShopBundle:Order');
        $entity = $repo->find($orderId);
        
        
        $form = $this->createForm(new \SKCMS\AdminBundle\Form\OrderType(),$entity);
        
                
        if ($request->getMethod() == 'POST') 
        {
          
            $form->bind($request);
          
            if ($form->isValid()) 
            {
              $em->persist($entity);
              $em->flush();
              $this->get('session')->getFlashBag()->add(
                'success',
                'Commande éditée'
                );
              $url = $this->generateUrl($request->get('_route'),$request->get('_route_params'));
              return $this->redirect($url);
            }
            else
            {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Oops, Il y a eu un problème. Veuillez réessayer'
                );
            }
            
        }
        
        $siteInfo = $this->container->getParameter('skcms_admin.siteinfo');
        
        
        return $this->render('SKCMSAdminBundle:Page:order-edit.html.twig',['order'=>$entity,'form'=>$form->createView(),'siteinfo'=>$siteInfo]);
//        return $this->render('SKCMSAdminBundle:Page:user-edit.html.twig',['entityParams'=>$userParams['userEntity'],'entity'=>$entity,'form'=>$form->createView()]);
    }
    
    public function listAction()
    {
        $eshopParams = $this->getModuleConfig();
        if ($eshopParams['enabled'] == false)
        {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SKCMSShopBundle:Order');
        
        $entities = $repo->findBy([],['date'=>'DESC']);
        
        
        
        
        
        return $this->render('SKCMSAdminBundle:Page:order-list.html.twig',['entities'=>$entities]);
    }
    
    private function getModuleConfig()
    {
        $modulesParams = $this->container->getParameter('skcms_admin.modules');
        return $modulesParams['eshop'];
    }
}
