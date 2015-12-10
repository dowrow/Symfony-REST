<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Reservation;

class ReservationController extends SerializerController
{
    
    public function getAction($id, $format) {
        $em = $this->getDoctrine()->getManager();
        $reservation = $em->getRepository('AppBundle:Reservation')->find(intval($id));
        return $this->formatResponse($reservation, $format);
    }
    
    public function deleteAction($id, $format) {
        $em = $this->getDoctrine()->getManager();
        $reservation = $em->getRepository('AppBundle:Reservation')->find(intval($id));
        $em->remove($reservation);
        $em->flush();
        return $this->formatResponse("ok", $format);
    }
    
    public function modifyAction($id, $format) {
        try {
            $em = $this->getDoctrine()->getManager();
            $reservation = $em->getRepository('AppBundle:Reservation')->find(intval($id));
            $params = array();
            $content = $this->get("request")->getContent();
            if (!empty($content)) {
                $params = json_decode($content, true);
                $reservation->setCourt($params['court']);
                $reservation->setDatetime(new \DateTime($params['datetime']));
                $reservation->setUser($params['user']);
            }
            $em->flush();
            return $this->formatResponse("ok", $format);
        } catch (Exception $ex) {
            return $this->formatResponse("error", $format);
        }
    }
    
    public function getAllAction($format) {
        $em = $this->getDoctrine()->getManager();
        $reservations = $em->getRepository('AppBundle:Reservation')->findAll();
        return $this->formatResponse($reservations, $format);
    }
    
    public function createAction($format) {
        try{
            $em = $this->getDoctrine()->getManager();
            $reservation = new Reservation();
            $params = array();
            $content = $this->get("request")->getContent();
            if (!empty($content)) {
                $params = json_decode($content, true);
                var_dump($params);
                $reservation->setCourt($params['court']);
                $reservation->setDatetime(new \DateTime($params['datetime']));
                $reservation->setUser($params['user']);
            }
            $em->persist($reservation);
            $em->flush();
            return $this->formatResponse("ok", $format);
        } catch (Exception $ex) {
            return $this->formatResponse("error", $format);
        }
    }
}
