<?php

/**
 * Created by PhpStorm.
 * User: store
 * Date: 02.06.2017 г.
 * Time: 14:12
 */
class SoftUni_VentsyslavVassilev_Softuni_VentsyslavVassilev_VentsyslavVassilevController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title($this->__('Submission'));
        $submissionID = $this->getRequest()->getParam('ID');
        $model = Mage::getModel('softuni_ventsyslavvassilev/ventsyslavvassilev');

        if ($submissionID) {
            $model->load($submissionID);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('softuni_ventsyslavvassilev')->__('This submission no longer exists.')
                );
                $this->_redirectReferer();
                return;
            }
        }

        $this->_title(
            $model->getId() ?
                $model->getName() :
                $this->__('New Submission')
        );
        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        // 4. Register model to use later in blocks
        Mage::register('softuni_ventsyslavvassilev_ventsyslavvassilev', $model);

        $this->loadLayout();
        $this->renderLayout();
    }


    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            $submissionId = $this->getRequest()->getParam('ID');
            $model = Mage::getModel('softuni_ventsyslavvassilev/ventsyslavvassilev')->load($submissionId);
            if (!$model->getId() && $submissionId) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('softuni_ventsyslavvassilev')->__('This submission no longer exists.')
                );
                $this->_redirect('*/*/index');
                return;
            }
            // init model and set data
            $model->setData($data);
            // try to save it
            try {
                // save the data
                $model->save();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('softuni_ventsyslavvassilev')->__('The submission has been saved.')
                );
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('ID' => $model->getId()));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/index');
                return;
            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('ID' => $this->getRequest()->getParam('ID')));
                return;
            }
        }
        $this->_redirect('*/*/index');
    }


    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($submissionId = $this->getRequest()->getParam('ID')) {
            try {
                // init model and delete
                $model = Mage::getModel('softuni_ventsyslavvassilev/ventsyslavvassilev');
                $model->load($submissionId);
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('softuni_ventsyslavvassilev')->__('The submission has been deleted.')
                );
                // go to grid
                $this->_redirect('*/*/index');
                return;
            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('ID' => $submissionId));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('softuni_ventsyslavvassilev')->__('Unable to find a submission to delete.')
        );
        // go to grid
        $this->_redirect('*/*/index');
    }

    protected function __isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/softuni_ventsyslavvassilev');

    }

}