<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;

class UserController extends SerializerController
{
    
    public function getAction($id, $format) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find(intval($id));
        return $this->formatResponse($user, $format);
    }
    
    public function deleteAction($id, $format) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find(intval($id));
        $em->remove($user);
        $em->flush();
        return $this->formatResponse("ok", $format);
    }
    
    public function modifyAction($id, $format) {
        try{
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AppBundle:User')->find(intval($id));
            $params = array();
            $content = $this->get("request")->getContent();
            if (!empty($content)) {
                $params = json_decode($content, true);
                $user->setConfirmationToken($params['confirmationToken']);
                $user->setCredentialsExpireAt($params['credentialsExpireAt']);
                $user->setCredentialsExpired($params['credentialsExpired']);
                $user->setEmail($params['email']);
                $user->setEmailCanonical($params['emailCanonical']);
                $user->setEnabled($params['enabled']);
                $user->setExpired($params['expired']);
                $user->setExpiresAt($params['expiresAt']);
                $user->setLastLogin($params['lastLogin']);
                $user->setLocked($params['locked']);
                $user->setPassword($params['password']);
                $user->setPasswordRequestedAt($params['passwordRequestedAt']);
                $user->setRoles($params['roles']);
                $user->setSalt($params['salt']);
                $user->setUsername($params['username']);
                $user->setUsernameCanonical($params['usernameCanonical']);            
            }
            $em->flush();
            return $this->formatResponse("ok", $format);
        } catch (Exception $ex) {
            return $this->formatResponse("error", $format);
        }
    }
    
    public function getAllAction($format) {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:User')->findAll();
        return $this->formatResponse($users, $format);
    }
    
    public function createAction($format) {
        try {
            $em = $this->getDoctrine()->getManager();
            $user = new User();
            $params = array();
            $content = $this->get("request")->getContent();
            if (!empty($content)) {
                $params = json_decode($content, true);
                $user->setConfirmationToken($params['confirmationToken']);
                $user->setCredentialsExpireAt($params['credentialsExpireAt']);
                $user->setCredentialsExpired($params['credentialsExpired']);
                $user->setEmail($params['email']);
                $user->setEmailCanonical($params['emailCanonical']);
                $user->setEnabled($params['enabled']);
                $user->setExpired($params['expired']);
                $user->setExpiresAt($params['expiresAt']);
                $user->setLastLogin($params['lastLogin']);
                $user->setLocked($params['locked']);
                $user->setPassword($params['password']);
                $user->setPasswordRequestedAt($params['passwordRequestedAt']);
                $user->setRoles($params['roles']);
                $user->setSalt($params['salt']);
                $user->setUsername($params['username']);
                $user->setUsernameCanonical($params['usernameCanonical']);
            }
            $em->persist($user);
            $em->flush();
            return $this->formatResponse("ok", $format);
        } catch (Exception $ex) {
            return $this->formatResponse("error", $format);
        }
    }
    
    public function addGroupAction($id, $groupId, $format) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find(intval($id));
        $group = $em->getRepository('AppBundle:Group')->find(intval($groupId));
        if (!empty($user) && !empty($group)) {
            $user->addGroup($group);
            $group->addUser($user);
        }
        $em->flush();
        return $this->formatResponse("ok", $format);
        
    }
}
