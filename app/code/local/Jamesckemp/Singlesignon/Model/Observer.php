<?php

class Jamesckemp_Singlesignon_Model_Observer {

    public function RegisterWp($observer) {

		$event = $observer->getEvent();  //Fetches the current event
		$customer = $event->getCustomer();
        
		// ----- WordPress User Info
		$email = $customer->getEmail();
		$newID = $customer->getId();
		$wpLogin = 'WP_user_'.$newID;
		$displayname = $customer->getFirstname().' '.$customer->getLastname();
		$password = Mage::app()->getRequest()->getPost('password');
		
		$user_id = username_exists( $wpLogin );
		
		if(!$user_id){
														
			$args = array(
				'user_pass' => $password,
				'user_login' => $wpLogin,
				'user_email' => $email,
				'display_name' => $displayname,
				'nickname' => $displayname,
				'user_nicename' => $displayname,
				'first_name' => $customer->getFirstname(),
				'last_name' => $customer->getLastname()			
			);
			
			$newUserId = wp_insert_user( $args );
			
		}
		// End WP add_user
		
		if (!$customer->isConfirmationRequired()) {
								
			// Login to WordPress
			$user = get_userdatabylogin($wpLogin);
			$user_id = $newUserId;
			wp_set_current_user($user_id, $wpLogin);
			wp_set_auth_cookie($user_id);
			do_action('wp_login', $wpLogin);
			// End WP login
			
		}
    }
	
	public function CustomerLogin($observer) {
		
				$event = $observer->getEvent();  //Fetches the current event
				$customer = $event->getCustomer();
						
				// ----- WordPress User Info
				$email = $customer->getEmail();
				$newID = $customer->getId();
				$wpLogin = 'WP_user_'.$newID;
				$displayname = $customer->getFirstname().' '.$customer->getLastname();
				$password = Mage::app()->getRequest()->getPost('password');
				$passwordconfirm = Mage::app()->getRequest()->getParam('confirmation');
		
				$user_id = username_exists( $wpLogin );
		
				if(!$user_id){
																
					$args = array(
						'user_pass' => $password,
						'user_login' => $wpLogin,
						'user_email' => $email,
						'display_name' => $displayname,
						'nickname' => $displayname,
						'user_nicename' => $displayname,
						'first_name' => $customer->getFirstname(),
						'last_name' => $customer->getLastname()			
					);
					
					$newUserId = wp_insert_user( $args );
					
					// Login to WordPress
					$user_id = $newUserId;
					wp_set_current_user($user_id, $email);
					wp_set_auth_cookie($user_id);
					do_action('wp_login', $email);
					// End WP login    
					
				} else {
					
					if($password && $password == $passwordconfirm) {

						// Update WordPress Password
						$user = get_userdatabylogin($wpLogin);
						$user_id = $user->ID;
						wp_update_user( array ('ID' => $user_id, 'password' => $password) ) ;
						// End Update WordPress Password
						
					} else {
						
						// Login to WordPress
						$user = get_userdatabylogin($wpLogin);
						$user_id = $user->ID;
						wp_set_current_user($user_id, $wpLogin);
						wp_set_auth_cookie($user_id);
						do_action('wp_login', $wpLogin);
						// End WP login 
					
					}
				
				}
		
    }
	
	public function CustomerLogout($observer) {
		wp_logout();
	}
	
	public function resetPassword($observer) {
		
		// Reset Password through My Account Area
		
		//echo Mage::app()->getRequest()->getActionName();
		//echo Mage::app()->getRequest()->getControllerModule();
		
		$customer = Mage::getSingleton('customer/session')->getCustomer();
			
		$newID = $customer->getId();
		$wpLogin = 'WP_user_'.$newID;
		
		if(Mage::app()->getRequest()->getActionName() == 'editPost'
			&& Mage::app()->getRequest()->getControllerModule() == 'Mage_Customer')
		{
			
			$password = Mage::app()->getRequest()->getParam('password');
			$email = Mage::app()->getRequest()->getParam('email');
			$firstname = Mage::app()->getRequest()->getParam('firstname');
			$lastname = Mage::app()->getRequest()->getParam('lastname');
			$displayname = $firstname.' '.$lastname;
			
			$new_user_info = array();
			
			if($firstname) { $new_user_info['first_name'] = $firstname;  }
			if($lastname) { $new_user_info['last_name'] = $lastname;  }
			if($password) { $new_user_info['user_pass'] = $password;  }
			if($email) { $new_user_info['user_email'] = $email; }
			if($displayname) { 
				$new_user_info['display_name'] = $displayname;
				$new_user_info['nickname'] = $displayname;
				$new_user_info['user_nicename'] = $displayname;  
			}
			
			// Need to get some sort of global user element, made at signup for WP username
			
				
			// Update WordPress Password
			$user = get_userdatabylogin($wpLogin);
			$user_id = $user->ID;
			$new_user_info['ID'] = $user_id;
			
			wp_update_user( $new_user_info ) ;
			// End Update WordPress Password
			
			// Login to WordPress
			wp_set_current_user($user_id, $wpLogin);
			wp_set_auth_cookie($user_id);
			do_action('wp_login', $wpLogin);
			// End WP login
			
		}
		
		// End Reset Password through My Account Area
		
		// Reset Password through Forgot my password
		
		if(Mage::app()->getRequest()->getActionName() == 'resetpasswordpost'
			&& Mage::app()->getRequest()->getControllerModule() == 'Mage_Customer')
		{
			$password = Mage::app()->getRequest()->getParam('password');
			$passwordconfirm = Mage::app()->getRequest()->getParam('confirmation');
			$id = Mage::app()->getRequest()->getParam('id');
			
			$customer = Mage::getModel('customer/customer')->load($id)->getData();
			$email = $customer['email'];
			
			if($password && $password == $passwordconfirm) {
			
				// Update WordPress Password
				$user = get_userdatabylogin($wpLogin);
				$user_id = $user->ID;
				wp_update_user( array ('ID' => $user_id, 'user_pass' => $password) ) ;
				// End Update WordPress Password
			
			}
			
		}
		
		// End Reset Password through Forgot my password
		
		
		
	}

}
