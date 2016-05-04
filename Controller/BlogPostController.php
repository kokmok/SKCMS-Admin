<?php

namespace SKCMS\AdminBundle\Controller;

use SKCMS\BlogBundle\Entity\BlogPost;
use SKCMS\BlogBundle\Form\BlogPostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \SKCMS\CoreBundle\Entity\PageTemplate;
use SKCMS\CoreBundle\Form\PageTemplateType;

class BlogPostController extends Controller
{
    public function editAction($id,$_locale)
    {
        $locale = $_locale;
        $user = $this->getUser();
        
                
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SKCMSBlogBundle:BlogPost');
       
        if ($id!==null)
        {
            $entity = $repo->find($id);
        }
        else
        {
            $entity = new BlogPost();
        }
        $entity->setTranslatableLocale($locale);
        
        $form = $this->createForm(new BlogPostType(),$entity);
        
        
        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') 
        {
          
            $form->handleRequest($request);
          
            if ($form->isValid()) 
            {

              $em->persist($entity);
              $em->flush();
              $this->get('session')->getFlashBag()->add(
                'success',
                'article bien édité :)'
                );

                if ($request->request->get('stay_here') !== null)
                {
                    $url = $this->generateUrl($request->get('_route'),['_locale'=>$locale,'id'=>$entity->getId()]);
                }
                elseif ($request->request->get('add_new') !== null)
                {
                    $url = $this->generateUrl($request->get('_route'),['_locale'=>$locale]);
                }
                else
                {
                    $url = $this->generateUrl('sk_admin_blogpostlist');
                }
//              $url = $this->generateUrl('sk_admin_blogpostlist');
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
        
        
        return $this->render('SKCMSAdminBundle:Page:blogpost-edit.html.twig',['entity'=>$entity,'form'=>$form->createView()]);
    }
    
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SKCMSBlogBundle:BlogPost');
        
        $entities = $repo->findAll();
        
        return $this->render('SKCMSAdminBundle:Page:blogpost-list.html.twig',['entities'=>$entities]);
    }
    
}
