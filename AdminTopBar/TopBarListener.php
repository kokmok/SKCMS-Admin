<?php

namespace SKCMS\AdminBundle\AdminTopBar;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpFoundation\Response;
/**
 * Description of TopBarListener
 *
 * @author Jona
 */
class TopBarListener {
    
    private $viewer;
    private $container;
    private $controller;
    private $route;
    private $routeParams;
    private $editPath;
    
    public function __construct(TopBarViewer $viewer,  \Symfony\Component\DependencyInjection\Container $container )
    {
        $this->viewer = $viewer;
        $this->container  = $container;
    }

    public function processTopBar(FilterResponseEvent $event)
    {
//        if (!$event->isMasterRequest()) 
//        {
//            return;
//        }
        
        
        
        if (!$this->container->get('security.context')->getToken() || !$this->container->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            return;
        }
        if (null !== $this->container->get('request')->get('_route'))
        {
            $response = $this->viewer->displayAdminTopBar($event->getResponse(),$this->editPath);
            $event->setResponse($response);
        }
    }
    
    public function controllerCall(FilterControllerEvent $event)
    {
        
        
        
        if (!$this->container->get('security.context')->getToken() || !$this->container->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            return;
        }
        $this->controller = $event->getController();
        
        $this->route = $this->container->get('request')->get('_route');
        $this->routeParams = $this->container->get('request')->get('_route_params');
        
        
        if (null !== $this->route)
        {
//            //dump($this->route);
//            die();
            if (key_exists('slug', $this->routeParams))
            {
                $slug =$this->routeParams['slug'];

                $translationRepo = $this->container->get('doctrine')->getManager()->getRepository('SKCMS\CoreBundle\Entity\Translation\EntityTranslation');
                $entity = $translationRepo->findObjectBySlug($slug,$this->container->get('request')->getLocale());
                
                if (null == $entity)
                {
                    throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
                }
                $entityType = substr($entity->getObjectClass(),strrpos($entity->getObjectClass(),'\\')+1);

                $this->editPath = $this->container->get('router')->generate('sk_admin_edit',['entity'=>$entityType,'id'=>$entity->getForeignKey()]);

            }
        }
        
        
    }
    
}
