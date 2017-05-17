<?php

class Manujith_HelloWorld_IndexController extends Mage_Core_Controller_Front_Action{

   /**
   * Index action
   *
   * @access public

   * @return void
   */
   public function indexAction() {
   		echo "hellow magento";
   }

   public function fooBarAction(){
   		echo "fooo";
   }
}
?>