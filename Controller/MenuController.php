<?php

namespace SKCMS\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \SKCMS\CoreBundle\Entity\MenuElement;
use SKCMS\CoreBundle\Form\MenuElementType;

class MenuController extends Controller
{
    public function deleteAction($id)
    {
        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SKCMSCoreBundle:MenuElement');
        
        $menu = $repo->find($id);
        
        if (null === $menu)
        {
            return $this->createNotFoundException('This menu doesn\'t exists');
        }
        $name = $menu->getName();
        $em->remove($menu);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add(
                'success',
                'Menu '.$name.' Deleted :)'
                );
        $url = $this->generateUrl('sk_admin_menulist');
        return $this->redirect($url);
        
    }
    
    public function listAction()
    {
        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SKCMSCoreBundle:MenuElement');
        
//        $repo->setDefaultLocale($locale);
        $entities = $repo->findRootMenu($this->getRequest()->getLocale());
//        die('sfsdf');
        return $this->render('SKCMSAdminBundle:Page:menu-list.html.twig',['entities'=>$entities]);
    }
    
    public function editAction($id,$_locale)
    {
        $locale = $_locale;
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SKCMSCoreBundle:MenuElement');
        if (null !== $id)
        {
            $repo->setDefaultLocale($locale);
            $menu = $repo->findMenu($id);
        }
        else
        {
            $menu = new MenuElement();
        }
        
        $entitiesParams = $this->container->getParameter('skcms_admin.entities');
        $entities = [];
        foreach ($entitiesParams as $entityName => $entityParams)
        {
           
            if (array_key_exists( 'skcmsMenuList',$entityParams) && $entityParams['skcmsMenuList'])
            {
                $repo = $em->getRepository($entityParams['bundle'].'Bundle:'.$entityName); 
                $subEntities = $repo->findAll();
                $entities[$entityName] = $subEntities;
            }
            
            
        }
        
        
        
        $form = $this->createForm(new MenuElementType($entities),$menu);
        
        
        
        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') 
        {
            $menuArray = json_decode($request->request->get('jsonmenu'));
            $children = $this->menuArrayToEntities($menuArray,$em,$menu,$locale);
            $menu->setName($request->request->get('name'));
            $menu->setTextId($request->request->get('textId'));
            $menu->setChildren($children);
            $em->persist($menu);
            
            $em->flush();
            
            if ($request->request->get('stay_here') !== null)
            {
                $url = $this->generateUrl('sk_admin_menuedit',['_locale'=>$locale,'id'=>$menu->getId()]);
            }
            elseif ($request->request->get('add_new') !== null)
            {
                $url = $this->generateUrl('sk_admin_menuedit',['_locale'=>$this->container->getParameter('locale')]);
            }
            else
            {
                $url = $this->generateUrl('sk_admin_menulist');
            }
            
            return $this->redirect($url);
            
            
        }
        
        $currentMenuArray = $this->menuEntitiesToArray($menu);
        
        $siteInfo = $this->container->getParameter('skcms_admin.siteinfo');
        
        return $this->render('SKCMSAdminBundle:Page:menu-edit.html.twig',['entity'=>$menu,'menu'=>$menu,'form'=>$form->createView(),'currentMenuArray'=>$currentMenuArray,'siteinfo'=>$siteInfo,'entities'=>$entities]);
    }
    
    private function menuArrayToEntities($menuArray, \Doctrine\ORM\EntityManager $em,  MenuElement $parent = null,$locale)
    {

        $collection = new \Doctrine\Common\Collections\ArrayCollection();
        $repo = $em->getRepository('SKCMSCoreBundle:MenuElement');
        $repo->setDefaultLocale($locale);
        foreach ($menuArray as $menuElement)
        {
//            //dump($menuElement);
//            die();
            $element = null;
            if (isset($menuElement->elementId))
            {
                $element = $repo->find($menuElement->elementId);
                $element->setTranslatableLocale($locale);
                $em->refresh($element);
            }
            if (null === $element)
            {
                $element = new MenuElement();
            }
            $element->setName($menuElement->name);
            $element->setEntityId($menuElement->targetId);
            $element->setEntityClass($menuElement->entityClass);
            $element->setPosition($menuElement->position);
            if (isset($menuElement->children) && count($menuElement->children))
            {
                $element->setChildren($this->menuArrayToEntities($menuElement->children,$em,$element,$locale));
            }
            $collection->add($element);
            if (null !== $parent)
            {
                $element->setParent($parent);
            }
            $em->persist($element);
            
        }
        
        return $collection;
        
    }
    
    private function translateChildren($menu,$locale,$em)
    {
//        if ($locale == 'en') // JE sens que ce truc va rester des plombes comme ça, pas vrai ? 20/2/2015
//        {
//            $locale = 'en_US';
//        }
        
        foreach ($menu->getChildren() as $child)
        {
            $child->setTranslatableLocale($locale);
            $em->refresh($child);
            //dump($locale);
            //dump($child);
            $child = $this->translateChildren($child,$locale,$em);
        }
        return $menu;
    }
    
    private function deletePreviousChildren($em,MenuElement $menu)
    {
        foreach ($menu->getChildren() as $children)
        {
            $this->deletePreviousChildren($em, $children);
            $em->remove($children);
        }
    }
    
    private function menuEntitiesToArray(MenuElement $menu)
    {
        $toReturn = [];
        foreach ($menu->getChildren() as $children)
        {
            $toReturn[$children->getPosition()]['name']=$children->getName();
            $toReturn[$children->getPosition()]['targetId']=$children->getEntityId();
            $toReturn[$children->getPosition()]['elementId']=$children->getId();
            $toReturn[$children->getPosition()]['entityClass']=$children->getEntityClass();
            $toReturn[$children->getPosition()]['position']=$children->getPosition();
            if (count($children->getChildren()))
            {
                $toReturn[$children->getPosition()]['children']=$this->menuEntitiesToArray($children);
            }
        }
        
        return $toReturn;
    }
}
