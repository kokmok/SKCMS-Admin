<?php

namespace SKCMS\AdminBundle\Controller;

use SKCMS\BlogBundle\Entity\BlogCategory;
use SKCMS\BlogBundle\Entity\BlogPost;
use SKCMS\BlogBundle\Form\BlogCategoryType;
use SKCMS\BlogBundle\Form\BlogPostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \SKCMS\CoreBundle\Entity\PageTemplate;
use SKCMS\CoreBundle\Form\PageTemplateType;

class BlogCategoryController extends Controller
{
    public function editAction($id)
    {
        $user = $this->getUser();
        
                
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SKCMSBlogBundle:BlogCategory');
       
        if ($id!==null)
        {
            $entity = $repo->find($id);
        }
        else
        {
            $entity = new BlogCategory();
        }
        
        $form = $this->createForm(new BlogCategoryType(),$entity);
        
        
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
              $url = $this->generateUrl('sk_admin_blogcategorylist');
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
        
        
        return $this->render('SKCMSAdminBundle:Page:blogcategory-edit.html.twig',['entity'=>$entity,'form'=>$form->createView()]);
    }
    
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SKCMSBlogBundle:BlogCategory');
        
        $entities = $repo->findAll();
        
        return $this->render('SKCMSAdminBundle:Page:blogcategory-list.html.twig',['entities'=>$entities]);
    }
    
}
