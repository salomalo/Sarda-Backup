<?php

/**
 * @author William Sergio Minossi
 * @copyright 2018
 */

	 submit_button();
	 echo '</form>';
     echo '</div>'; //containerleft
     $when_installed = get_option('bill_installed');
     $now = time();
     $delta = $now - $when_installed;
     
     
     if ($delta > (3600 * 24 * 7))
     {
        echo '<div id="containerright">';
        echo '<ul>';
        echo '<h2>Help & Support</h2>';
        echo '<li><a href="http://stopbadbots.com/help">OnLine Guide</a></li>';
        echo '<li><a href="http://billminozzi.com/dove/">Support</a></li>';
        echo '<li><a href="http://siterightaway.net/troubleshooting/">Troubleshooting</a></li>';

        echo '</ul>';
        
        
       
        
        
        $stopbadbots_checkversion = trim(get_option('stopbadbots_checkversion', ''));
        
        
      if (empty($stopbadbots_checkversion))
      {
        
          echo '<ul>';
          $x = rand(1,5);
        
        
        
        
        
        //$x = 5;
        if($x < 3){
            
            
            echo '<h2>Like This Plugin?</h2>';
            _e( 'If you like this product, please write a few words about it. It will help other people find this useful plugin more quickly.<br><b>Thank you!</b>', 'cardealer' ); 
            ?>
            <br /><br />
            <a href="http://stopbadbots.com/share/" class="button button-medium button-primary"><?php _e( 'Rate or Share', 'cardealer' ); ?></a>
            <?php
            echo '</ul>';
        }
        
        elseif ($x == 5 )
        {
            echo '<h2>We Need Your Support</h2>';
            _e( 'It is hard to continue development and support for this plugin without contributions from users like you. If you enjoy it and find it useful, please consider making a donation. Your donation will help encourage and support the plugin\'s continued development and better user support.      
', 'cardealer' ); 
            ?>
            <br /><br />
            <a href="http://stopbadbots.com/donate/" class="button button-medium button-primary"><?php _e( 'Donate', 'cardealer' ); ?></a>
            <?php
            echo '</ul>';
        }
        
        else
        {
            echo '<h2>Please help us keep the plugin free & up-to-date</h2>';
            _e( 'If you use & enjoy Car Dealer Plugin, please rate it on WordPress.org. It only takes a second and helps us keep the plugin free and maintained. Thank you!', 'cardealer' ); 
            ?>
            <br /><br />
            <a href="https://wordpress.org/support/plugin/stopbadbots/reviews/#new-post" class="button button-medium button-primary"><?php _e( 'Rate', 'cardealer' ); ?></a>
            <?php
            echo '</ul>';
        } 
        
        
         
        if($x <> 5)
           echo '<ul>';      
       //        $banner_image = STOPBADBOTSIMAGES.'/keys_from_left.png';
        if($x == 1){
            echo '<h2>Go Pro:<br />Your bad bots table is always updated.</h2>';
            echo '<img src="'.STOPBADBOTSIMAGES.'/apple.jpg" width="250" />';
        }
        if($x == 2){
            echo '<h2>Become Pro:<br />Your bad bots table is always updated.</h2>';
            // Chave
            echo '<img src="'.STOPBADBOTSIMAGES.'/keys.jpg" width="250" />';
        }
        if($x == 3){
            echo '<h2>Power for block bad bots: <br />Your bad bots table is always updated.</h2>';
            // Leao
            echo '<img src="'.STOPBADBOTSIMAGES.'/lion.jpg" width="250" />';
        }
        if($x == 4){
            echo '<h2>Get Premium Performance: <br />Stop Bad Bots and save bandwidth.</h2>';
            // Corrida
            echo '<img src="'.STOPBADBOTSIMAGES.'/racing.jpg" width="250" />';
        }
        if($x <> 5){
            ?>
              <li>Weekly Automatic Updates</li>
              <li>You don't need update the plugin to update the  boat's table</li>
             <br />
            <a href="http://stopbadbots.com/premium/" class="button button-medium button-primary" ><?php _e( 'Learn More', 'cardealer' ); ?></a>
            <?php
            echo '</ul>';  

        }            

     } //free_

         

        echo '</div>'; //containerright 

      } // if delta...

	  echo $this->page->markup_bottom;

 //	}       



// }