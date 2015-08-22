<?php
namespace SKCMS\AdminBundle\Listener;


use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
/**
 * Description of OneToManyDeleteListener
 *
 * @author jona
 */
class OneToManyDeleteListener 
{
    private $doctrine;
    
    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
    }
    
    public function PostUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $em = $args->getObjectManager();

        $classMetaData = $em->getClassMetadata(get_class($entity));
        
        foreach ($classMetaData->associationMappings as $associationMappingKey => $associationMappingDatas)
        {
        if ($associationMappingDatas['type'] == \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY)
            {
                
                $repo = $em->getRepository($associationMappingDatas['targetEntity']);
                $associated = $repo->findBy([$associationMappingDatas['mappedBy']=>$entity]);
                
                
                $newAssociated = call_user_method('get'.ucfirst($associationMappingKey),$entity);
                
                foreach ($associated as $associatedEntity)
                {
                    if (!$newAssociated->contains($associatedEntity))
                    {

                        $em->remove($associatedEntity);
                    }
                    
                }
                
                $em->flush();

//                
            }
        }
//        dump($classMetaData);
//        die();
    }
}
