<?php

// url/?ait-payment&payment-type=paypal&payment-id=0

// were about to submit a transaction
if(isset($_REQUEST['ait-payment'])){
	$themeOptions = aitOptions()->getOptionsByType('theme');
	$currency = $themeOptions['payments']['currency'];

	// prepared for future extending of payments classes
	if(isset($_REQUEST['payment-type']) && !empty($_REQUEST['payment-type'])){
		// payment-type -> string containing type of payment e.g. "paypal"
		switch($_REQUEST['payment-type']){
			case "paypal":
				// start a transaction request only when we have an id of the transaction
				$packages = new ThemePackages();
				$package = $packages->getPackageBySlug($_REQUEST['payment-data-package']);
				
				if(!empty($package)){
					$options = $package->getOptions();

					if(!empty($options['price'])){
						if(class_exists("AitPaypal")){
							$paypal = AitPaypal::getInstance();
							$data = array(
								'user' => $_REQUEST['payment-data-user'],
								'package' => $_REQUEST['payment-data-package'],
								'operation' => $_REQUEST['payment-data-operation'],
							);

							$paypal->requestPayment($data, $package->getName(), $package->getDesc(), floatval($options['price']), 0, $currency);
						} else {
							// redirect back
							$redirect = home_url().'/?ait-notification=user-registration-error';
							wp_safe_redirect( $redirect );
							exit();
						}
					}
				} else {
					// redirect back
					$redirect = home_url().'/?ait-notification=user-registration-error';
					wp_safe_redirect( $redirect );
					exit();
				}
			break;
			case "paypalRecurring":
				// start a transaction request only when we have an id of the transaction
				$packages = new ThemePackages();
				$package = $packages->getPackageBySlug($_REQUEST['payment-data-package']);
				
				if(!empty($package)){
					$options = $package->getOptions();

					if(!empty($options['price'])){
						if(class_exists("AitPaypalSubscriptions")){
							$paypal = AitPaypalSubscriptions::getInstance();
							$data = array(
								'user' => $_REQUEST['payment-data-user'],
								'package' => $_REQUEST['payment-data-package'],
								'operation' => $_REQUEST['payment-data-operation'],
							);

							$paypal->createAgreement($data, array(
								'name' => $package->getName(),
								'description' => $package->getDesc(),
								'interval' => $options['expirationLimit'],
								'amount' => floatval($options['price']),
								'currency' => $currency,
								'trialTime' => $options['trialTime'],
							));
						} else {
							// redirect back
							$redirect = home_url().'/?ait-notification=user-registration-error';
							wp_safe_redirect( $redirect );
							exit();
						}
					}
				} else {
					// redirect back
					$redirect = home_url().'/?ait-notification=user-registration-error';
					wp_safe_redirect( $redirect );
					exit();
				}
			break;
			case "stripe":
				// start a transaction request only when we have an id of the transaction
				$packages = new ThemePackages();
				$package = $packages->getPackageBySlug($_REQUEST['payment-data-package']);
				
				if(!empty($package)){
					$options = $package->getOptions();

					//if(isset($_REQUEST['payment-price']) && $_REQUEST['payment-price'] !== ""){
					if(!empty($options['price'])){
						if(class_exists("AitStripe")){
							$stripe = AitStripe::getInstance();
							$data = array(
								'user' => $_REQUEST['payment-data-user'],
								'package' => $_REQUEST['payment-data-package'],
								'operation' => $_REQUEST['payment-data-operation'],
							);

							$packages = new ThemePackages();
							$package = $packages->getPackageBySlug($_REQUEST['payment-data-package']);

							$stripe->requestPayment($data, $package->getName(), $package->getDesc(), floatval($options['price']), $currency);
						} else {
							// redirect back
							$redirect = home_url().'/?ait-notification=user-registration-error';
							wp_safe_redirect( $redirect );
							exit();
						}
					}
				} else {
					// redirect back
					$redirect = home_url().'/?ait-notification=user-registration-error';
					wp_safe_redirect( $redirect );
					exit();
				}
			break;
			default:
				// bank transfer
			break;
		}

	}

}

if(class_exists('AitPaypal')){
	add_action('ait-paypal-payment-completed','aitPaypalPaymentSuccess');
	function aitPaypalPaymentSuccess($payment) {
		$data = $payment->data;
		$user = new Wp_User($data['user']);
		if($data['operation'] === 'register'){
			$user->set_role( $data['package'] );

			$redirect = home_url('/').'?ait-notification=user-registration-success';
			wp_safe_redirect( $redirect );
			exit();
		}
		if($data['operation'] === 'renew'){
			aitSetPackageUserRenewed($data['user'], $data['package']);
		}
		if($data['operation'] === 'upgrade'){
			aitSetPackageUserRenewed($data['user'], $data['package']);
		}
	}
}

if(class_exists('AitPaypalSubscriptions')){

	add_action('ait-paypal-subscriptions-payment-completed','aitPaypalPaymentSubscriptionsCompleted');
	function aitPaypalPaymentSubscriptionsCompleted($payment) {
		$data = $payment->data;
		$user = new Wp_User($data['user']);
		$defaultRole = get_option('default_role');
		$trialStatus = get_user_option( 'trial_status', $user->ID );

		if($data['operation'] === 'register'){
			$user->set_role( $data['package'] );

			$redirect = home_url('/').'?ait-notification=user-registration-success';
			wp_safe_redirect( $redirect );
			exit();
		}

		if($data['operation'] === 'renew'){
			$user->set_role($defaultRole);
			if($trialStatus == 'activated'){
				update_user_meta( $user->ID, 'trial_status', 'expired' );
				update_user_meta( $user->ID, 'package_status', 'activated' );
			} else {
				update_user_meta( $user->ID, 'trial_status', 'not set' );
				update_user_meta( $user->ID, 'package_status', 'activated' );
			}
			aitSetPackageUserRenewed($data['user'], $data['package']);
		}

		if($data['operation'] === 'upgrade'){		
			$user->set_role($defaultRole);
			if($trialStatus == 'activated'){
				update_user_meta( $user->ID, 'trial_status', 'expired' );
				update_user_meta( $user->ID, 'package_status', 'activated' );
			} else {
				update_user_meta( $user->ID, 'trial_status', 'not set' );
				update_user_meta( $user->ID, 'package_status', 'activated' );
			}
			aitSetPackageUserRenewed($data['user'], $data['package']);
		}
	}
}

if(class_exists('AitPaypalSubscriptions')){
	add_action('ait-paypal-subscriptions-profile-created','aitPaypalPaymentSubscriptionsCreated');
	function aitPaypalPaymentSubscriptionsCreated($payment) {
		$data = $payment->data;
		$user = new Wp_User($data['user']);
		$packages = new ThemePackages();
		$packageOptions = $packages->getPackageBySlug($payment->data['package'])->getOptions();
		$defaultRole = get_option('default_role');
		if($data['operation'] === 'register'){
			$user->set_role( $data['package'] );

			$redirect = home_url('/').'?ait-notification=user-registration-success';
			wp_safe_redirect( $redirect );
			exit();
		}
		if($data['operation'] === 'renew'){
			$user->set_role($defaultRole);
			if($packageOptions['trialTime'] != 0){
				update_user_meta( $user->ID, 'trial_status', 'activated' );
				update_user_meta( $user->ID, 'package_status', 'not initialized' );
			} else {
				update_user_meta( $user->ID, 'trial_status', 'not set' );
				update_user_meta( $user->ID, 'package_status', 'not initialized' );
			}
			aitSetPackageUserRenewed($data['user'], $data['package']);
		}
		if($data['operation'] === 'upgrade'){
			$user->set_role($defaultRole);
			
			if($packageOptions['trialTime'] != 0){
				update_user_meta( $user->ID, 'trial_status', 'activated' );
				update_user_meta( $user->ID, 'package_status', 'not initialized' );
			} else {
				update_user_meta( $user->ID, 'trial_status', 'not set' );
				update_user_meta( $user->ID, 'package_status', 'not initialized' );
			}
			
			aitSetPackageUserRenewed($data['user'], $data['package']);
		}
	}
}

if(class_exists('AitStripe')){
	add_action('ait-stripe-payment-success', 'aitStripePaymentSuccess');
	function aitStripePaymentSuccess($payment){
		$data = $payment->data;
		$user = new Wp_User($data['user']);
		if($data['operation'] === 'register'){
			$user->set_role( $data['package'] );

			$redirect = home_url('/').'?ait-notification=user-registration-success';
			wp_safe_redirect( $redirect );
			exit();
		}
		if($data['operation'] === 'renew'){
			aitSetPackageUserRenewed($data['user'], $data['package']);
		}
		if($data['operation'] === 'upgrade'){
			aitSetPackageUserRenewed($data['user'], $data['package']);
		}	
	}
}