<?php

namespace WPStaging\Iterators;

class RecursiveFilterExclude extends \RecursiveFilterIterator {

   public $excludeFolders = array();

   public function __construct( \RecursiveIterator $iterator, array $excludeFolders ) {
      parent::__construct( $iterator );

      // Set exclude filter
      $this->excludeFolders = $excludeFolders;
   }

   public function accept() {
      
      $path = $this->getInnerIterator()->getSubPathname();
      //$file = $this->getFilename();
      //$file2 = $this->getBaseName();
      
      
      if ($this->isDir() && in_array( $path, $this->excludeFolders )){
         return false;
      }
      return true;
            
   }

   public function getChildren() {
      return new self( $this->getInnerIterator()->getChildren(), $this->excludeFolders );
   }

}
