<?php
/**
 * Order administration interface, based on ModelAdmin
 * @package shop
 * @subpackage cms
 */
class OrdersAdmin extends ModelAdmin{

	private static $url_segment = 'orders';
	private static $menu_title = 'Orders';
	private static $menu_priority = 1;
	private static $menu_icon = 'shop/images/icons/order-admin.png';

	private static $managed_models = array(
		'Order' => array(
			'title' => 'Orders'
		)
	);

	public function getEditForm($id = null, $fields = null) {
		$form = parent::getEditForm($id, $fields);
		$gridFieldName = 'Order';
		$gridField = $form->Fields()->fieldByName($gridFieldName);
		if ($gridField) {
			$gridField->getConfig()->removeComponentsByType('GridFieldAddNewButton');
		}
		return $form;
	}

	public function getList() {
		$context = $this->getSearchContext();
		$params = $this->request->requestVar('q');
		//TODO update params DateTo, to include the day, ie 23:59:59

		$list = $context->getResults($params)
			->exclude("Status",Order::config()->hidden_status); //exclude hidden statuses

		$this->extend('updateList', $list);

		return $list;
	}

        public function getExportFields() {
                $fields = array(
                        'Reference' => 'Reference',
                        'FormattedDate' => 'Placed',
                        'FirstName' => 'First Name',
                        'Surname' => 'Surname',
                        'OrderType' => 'Order Type',
                        'OrderShippingType' => 'Shipping Type',
                        'MemberTypeName' => 'Member Type',
                        'QuarterlyBottlesAmount' => 'Quarterly Bottles',
                        'FullShippingAddress' => 'Shipping Address',
                        'ProductsBought' => 'Products Purchased',
                        'LatestEmail' => 'Customer Email',
                        'Total' => 'Total',
                        'Status' => 'Status',
                );
                return $fields;
        }
}

