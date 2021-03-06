<?php

class PopularController extends JO_Action {
	
	public function indexAction() {
		
		$request = $this->getRequest();
		
		$page = (int)$request->getRequest('page');
		if($page < 1) { $page = 1; }
		
		$data = array(
			'start' => ( JO_Registry::get('config_front_limit') * $page ) - JO_Registry::get('config_front_limit'),
			'limit' => JO_Registry::get('config_front_limit'),
			'order' => 'pins.views',
			'sort' => 'DESC',
//			'filter_marker' => $request->getRequest('marker'),
			'filter_like_repin_comment' => true,
                        'filter_categoria_id' => $request->getRequest('category_id')
		);
		
//		if((int)JO_Session::get('user[user_id]')) {
//			$data['following_users_from_user_id'] = JO_Session::get('user[user_id]');
//		}
		
		
		$this->view->pins = '';
		$pins = Model_Pins::getPins($data);
		
		if($pins) {
			$banners = Model_Banners::getBanners(
				new JO_Db_Expr("`controller` = '".$request->getController()."' AND position BETWEEN '".(int)$data['start']."' AND '".(int)$data['limit']."'")
			);
			$pp = JO_Registry::get('config_front_limit');
			foreach($pins AS $row => $pin) {
				///banners
				$key = $row + (($pp*$page)-$pp);
				if(isset($banners[$key])) {
					$this->view->pins .= Helper_Banners::returnHtml($banners[$key]);	
				}
				//pins
				$this->view->pins .= Helper_Pin::returnHtml($pin);
			}
			//JO_Registry::set('marker', Model_Pins::getMaxPin($data));
		}
		
		if($request->isXmlHttpRequest()) {
			echo $this->view->pins;
			$this->noViewRenderer(true);
		} else {
			$this->view->children = array(
	        	'header_part' 	=> 'layout/header_part',
	        	'footer_part' 	=> 'layout/footer_part'
	        );
		}
		
	}
	
	public function pageAction(){
		$this->forward('popular', 'index');
	}
	
	public function viewAction(){
		$this->forward('popular', 'index');
	}
	
}

?>