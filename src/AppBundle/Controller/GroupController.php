<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Group;

class GroupController extends SerializerController
{
    
    public function getAction($id, $format) {
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository('AppBundle:Group')->find(intval($id));
        return $this->formatResponse($group, $format);
    }
    
    public function deleteAction($id, $format) {
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository('AppBundle:Group')->find(intval($id));
        $em->remove($group);
        $em->flush();
        return $this->formatResponse("ok", $format);
    }
    
    public function modifyAction($id, $format) {
        try{
            $em = $this->getDoctrine()->getManager();
            $group = $em->getRepository('AppBundle:Group')->find(intval($id));
            $params = array();
            $content = $this->get("request")->getContent();
            if (!empty($content)) {
                $params = json_decode($content, true);
                $group->setName($params['name']);
                $group->setRoles($params['roles']);     
            }
            $em->flush();
            return $this->formatResponse("ok", $format);
        } catch (Exception $ex) {
            return $this->formatResponse("error", $format);
        }
    }
    
    public function getAllAction($format) {
        $em = $this->getDoctrine()->getManager();
        $groups = $em->getRepository('AppBundle:Group')->findAll();
        return $this->formatResponse($groups, $format);
    }
    
    public function createAction($format) {
        try {
            $em = $this->getDoctrine()->getManager();
            $group = new Group();
            $params = array();
            $content = $this->get("request")->getContent();
            if (!empty($content)) {
                $params = json_decode($content, true);
                $group->setName($params['name']);
                $group->setRoles($params['roles']);
            }
            $em->persist($group);
            $em->flush();
            return $this->formatResponse("ok", $format);
        } catch (Exception $ex) {
            return $this->formatResponse("error", $format);
        }
    }
    
    public function addUserAction($id, $userId, $format) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find(intval($userId));
        $group = $em->getRepository('AppBundle:Group')->find(intval($id));
        if (!empty($user) && !empty($group)) {
            $user->addGroup($group);
            $group->addUser($user);
        }
        $em->flush();
        return $this->formatResponse("ok", $format);
    }
}
