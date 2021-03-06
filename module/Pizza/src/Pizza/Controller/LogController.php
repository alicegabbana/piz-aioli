<?php

namespace Pizza\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Pizza\Service\ControllerServiceInterface;
use Pizza\Form\LogForm;
use Pizza\Form\PostFieldset;

class LogController extends AbstractActionController {

    protected $service;

    public function __construct(ControllerServiceInterface $service) {
        $this->service = $service->getService();
    }

    public function connectAction() {
        if ($this->getRequest()->isPost()) {
            $dataForm = $this->getRequest()->getPost();

            $request = $this->service->getRepository('Pizza\Entity\TbUsers')->findOneBy(array('email' => $dataForm['email'], 'password' => $dataForm['password']));
            if ($request) {
                $_SESSION['userId'] = $request->getUserId();
                $_SESSION['email'] = $request->getEmail();
                $_SESSION['role'] = $request->getRole();
                return $this->redirect()->toRoute('index');
            }
        }
        
        $connectuser = new \Pizza\Entity\TbUsers();
        $form = new LogForm($this->service, $connectuser);
        $viewData['form'] = $form;
        return new ViewModel($viewData);
        
        
    }

    public function adduserAction() {

        // On récupère l'objet Request
        $requestpost = $this->getRequest();
        $connectuser = new \Pizza\Entity\TbUsers();
        $form = new LogForm($this->service, $connectuser);
        $viewData['form'] = $form;

        // On vérifie si le formulaire a été posté
        if ($requestpost->isPost()) {
            $newuser = new \Pizza\Entity\TbUsers();

            // Et on passe l'InputFilter de Category au formulaire
            $PostFieldset = new PostFieldset($this->service);
            $logform->setInputFilter($PostFieldset->getInputFilter());
            $logform->setData($requestpost->getPost());
            // Si le formulaire est valide
            if ($logform->isValid()) {
                $data = $logform->getData();
                $newuser->exchangeArray($data);
                $ville = $this->service->getRepository('\Pizza\Entity\TbVilles')->find($data['ville']);
                $newuser->setVille($ville);
                $this->service->persist($newuser);
                $this->service->flush();
                
                $_SESSION['userId']= $newuser->getUserId();
                $_SESSION['email'] = $newuser->getEmail();
                $_SESSION['role'] = $newuser->getRole();
                
                return $this->redirect()->toRoute('userdetail');
            }
        }

        
        return new ViewModel($viewData);
    }
    
    public function edituserAction() {
        
        $userToEdit = $this->service->getRepository('Pizza\Entity\TbUsers')->findOneBy(array('userId' => $_SESSION['userId']));
        
        $logform = new LogForm($this->service, $userToEdit);

        // On récupère l'objet Request
        $requestpost = $this->getRequest();


        // On vérifie si le formulaire a été posté
        if ($requestpost->isPost()) {

            // Et on passe l'InputFilter de Category au formulaire
            $PostFieldset = new PostFieldset($this->service);
            $logform->setInputFilter($PostFieldset->getInputFilter());
            $logform->setData($requestpost->getPost());
            // Si le formulaire est valide
            if ($logform->isValid()) {
                $data = $logform->getData();
                $userToEdit->exchangeArray($data);
                $ville = $this->service->getRepository('\Pizza\Entity\TbVilles')->find($data['ville']);
                $userToEdit->setVille($ville);
                
                $this->service->persist($userToEdit);
                $this->service->flush();

                $_SESSION['email'] = $userToEdit->getEmail();
                
                return $this->redirect()->toRoute('userdetail');
            }
        }

        return new ViewModel(
                array(
            'form' => $logform
                )
        );
    }

}
