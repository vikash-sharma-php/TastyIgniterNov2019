<?php

namespace Igniter\Cart\Components;

use Admin\Models\Addresses_model;
use Admin\Models\Customers_model;
use Admin\Traits\ValidatesForm;
use ApplicationException;
use Auth;
use Cart;
use Exception;
use Igniter\Cart\Classes\OrderManager;
use Igniter\Cart\Models\Orders_model;
use Igniter\Local\Classes\CoveredArea;
use Illuminate\Http\RedirectResponse;
use Location;
use Main\Traits\HasPageOptions;
use Redirect;
use System\Classes\BaseComponent;

class Checkout extends BaseComponent
{
    use ValidatesForm;
    use HasPageOptions;

    /**
     * @var \Igniter\Cart\Classes\OrderManager
     */
    protected $orderManager;

    protected $order;

    public function initialize()
    {
        $this->orderManager = OrderManager::instance();
    }

    public function defineProperties()
    {
        return [
            'orderDateFormat' => [
                'label' => 'Date format to display order date on the checkout success page',
                'type' => 'text',
            ],
            'orderTimeFormat' => [
                'label' => 'Time format to display order time on the checkout success page',
                'type' => 'text',
            ],
            'showCountryField' => [
                'label' => 'Whether to display the country form field',
                'type' => 'switch',
                'default' => FALSE,
            ],
            'agreeTermsPage' => [
                'label' => 'lang:igniter.cart::default.checkout.label_checkout_terms',
                'type' => 'select',
                'options' => [static::class, 'getPagesOptions'],
                'comment' => 'lang:igniter.cart::default.checkout.help_checkout_terms',
            ],
            'menusPage' => [
                'label' => 'lang:igniter.cart::default.checkout.label_checkout_terms',
                'type' => 'select',
                'default' => 'local/menus',
                'options' => [static::class, 'getPageOptions'],
                'comment' => 'Page to redirect to when checkout can not be performed.',
            ],
            'redirectPage' => [
                'label' => 'Page to redirect to when checkout fails',
                'type' => 'select',
                'options' => [static::class, 'getPageOptions'],
                'default' => 'checkout/checkout',
            ],
            'ordersPage' => [
                'label' => 'Account orders page',
                'type' => 'select',
                'options' => [static::class, 'getPageOptions'],
                'default' => 'account/orders',
            ],
            'successPage' => [
                'label' => 'Page to redirect to when checkout is successful',
                'type' => 'select',
                'options' => [static::class, 'getPageOptions'],
                'default' => 'checkout/success',
            ],
            'successParamCode' => [
                'label' => 'The parameter name used for the order hash code',
                'type' => 'text',
                'default' => 'hash',
            ],
        ];
    }

    public function onRun()
    {
        if (!$this->isCheckoutSuccessPage()) {
            if ($redirect = $this->isOrderMarkedAsProcessed())
                return $redirect;

            if ($redirect = $this->validateCart(FALSE))
                return $redirect;
        }
        else {
            $this->orderManager->clearOrder();
        }

        $this->prepareVars();
    }

    protected function prepareVars()
    {
        $this->page['orderDateFormat'] = $this->property('orderDateFormat', setting('date_format'));
        $this->page['orderTimeFormat'] = $this->property('orderTimeFormat', setting('time_format'));
        $this->page['showCountryField'] = (bool)$this->property('showCountryField', 1);
        $this->page['agreeTermsPage'] = $this->property('agreeTermsPage');
        $this->page['redirectPage'] = $this->property('redirectPage');
        $this->page['menusPage'] = $this->property('menusPage');
        $this->page['ordersPage'] = $this->property('ordersPage');
        $this->page['successPage'] = $this->property('successPage');

        $this->page['confirmCheckoutEventHandler'] = $this->getEventHandler('onConfirm');

        $this->page['order'] = $this->getOrder();
        $this->page['paymentGateways'] = $this->getPaymentGateways();
    }

    /**
     * @return Orders_model
     */
    public function getOrder()
    {
        if (!is_null($this->order))
            return $this->order;

        if (!$this->isCheckoutSuccessPage()) {
            $order = $this->orderManager->getOrder();

            if (!$order->isPaymentProcessed())
                $this->orderManager->applyRequiredAttributes($order);
        }
        else {
            $order = $this->orderManager->getOrderByHash($this->hashCode());
        }

        return $this->order = $order;
    }

    public function getPaymentGateways()
    {
        $order = $this->getOrder();

        return $order->order_total > 0
            ? $this->orderManager->getPaymentGateways() : null;
    }

    /**
     * @return Customers_model|\Igniter\Flame\Auth\Models\User
     */
    public function customer()
    {
        if (!Auth::check()) {
            return null;
        }

        return Auth::getUser();
    }

    public function hashCode()
    {
        $routeParameter = $this->property('successParamCode');

        if ($code = $this->param($routeParameter)) {
            return $code;
        }

        return get('success');
    }

    public function onConfirm()
    {
        if ($redirect = $this->isOrderMarkedAsProcessed())
            return $redirect;

        try {
            $data = post();
            $data['cancelPage'] = $this->property('redirectPage');
            $data['successPage'] = $this->property('successPage');

            $this->validateCart();

            $this->validate($data, $this->createRules());

            if ($address = $this->getAddressFromRequest($data))
                $this->validateAddress($address);

            $order = $this->getOrder();
            $this->orderManager->saveOrder($order, $data);

            if (($redirect = $this->orderManager->processPayment($order, $data)) === FALSE)
                return;

            if ($redirect instanceof RedirectResponse)
                return $redirect;

            if ($redirect = $this->isOrderMarkedAsProcessed())
                return $redirect;
        }
        catch (Exception $ex) {
            flash()->warning($ex->getMessage())->important();

            return Redirect::back()->withInput();
        }
    }

    protected function validateCart($throwException = TRUE)
    {
        $failed = FALSE;
        try {
            if (!Cart::count())
                throw new ApplicationException(lang('igniter.cart::default.checkout.alert_no_menu_to_order'));

            if (!setting('guest_order') AND !$this->customer())
                throw new ApplicationException(lang('igniter.cart::default.checkout.alert_customer_not_logged'));

            if (!$location = Location::current())
                throw new ApplicationException(lang('igniter.cart::default.alert_location_required'));

            if (!$location->hasFutureOrder() AND Location::isClosed())
                throw new ApplicationException(lang('igniter.cart::default.alert_location_closed'));

            if (!Location::checkOrderType($orderType = Location::orderType()))
                throw new ApplicationException(lang('igniter.local::default.alert_'.$orderType.'_unavailable'));

            $orderDateTime = Location::orderDateTime();
            if (!$orderDateTime OR !Location::checkOrderTime($orderDateTime))
                throw new ApplicationException(lang('igniter.cart::default.checkout.alert_no_delivery_time'));

            if (Location::orderTypeIsDelivery() AND Location::requiresUserPosition() AND !Location::userPosition()->hasCoordinates())
                throw new ApplicationException(lang('igniter.cart::default.alert_no_search_query'));
        }
        catch (Exception $ex) {
            if ($throwException)
                throw $ex;

            flash()->warning($ex->getMessage())->now();
            $failed = TRUE;
        }

        if (!$failed AND Location::orderTypeIsDelivery() AND !Location::checkMinimumOrder(Cart::total()))
            $failed = TRUE;

        if ($failed)
            return Redirect::to(restaurant_url($this->property('menusPage')));

        return FALSE;
    }

    protected function validateAddress($address)
    {
        if (!Location::requiresUserPosition())
            return;

        $address['country'] = app('country')->getCountryNameById($address['country_id']);
        $address = implode(' ', array_only($address, ['address_1', 'address_2', 'city', 'state', 'postcode', 'country']));

        $collection = app('geocoder')->geocode($address);
        if (!$collection OR $collection->isEmpty())
            throw new ApplicationException(lang('igniter.cart::default.alert_invalid_search_query'));

        if (!$area = Location::current()->searchDeliveryArea($collection->first()->getCoordinates()))
            throw new ApplicationException(lang('igniter.cart::default.checkout.error_covered_area'));

        if (!Location::isCurrentAreaId($area->area_id)) {
            Location::setCoveredArea(new CoveredArea($area));
            throw new ApplicationException(lang('igniter.cart::default.checkout.alert_delivery_area_changed'));
        }
    }

    protected function createRules()
    {
        $namedRules = [
            ['first_name', 'lang:igniter.cart::default.checkout.label_first_name', 'required|min:2|max:32|regex:/^(?!\s*$)[A-Z-a-z]+$/i'],
            ['last_name', 'lang:igniter.cart::default.checkout.label_last_name', 'required|min:2|max:32|regex:/^(?!\s*$)[A-Z-a-z]+$/i'],
            ['email', 'lang:igniter.cart::default.checkout.label_email', 'sometimes|required|email|max:96|unique:customers'],
            ['telephone', 'lang:igniter.cart::default.checkout.label_telephone', 'regex:/^[0-9]+$/|digits_between:10,20'],
            ['comment', 'lang:igniter.cart::default.checkout.label_comment', 'max:500'],
            ['payment', 'lang:igniter.cart::default.checkout.label_payment_method', 'sometimes|required|alpha_dash'],
            ['terms_condition', 'lang:button_agree_terms', 'sometimes|integer'],
        ];

        if (Location::orderTypeIsDelivery()) {
            $namedRules[] = ['address_id', 'lang:igniter.cart::default.checkout.label_address', 'sometimes|required|integer'];
            $namedRules[] = ['address.address_1', 'lang:igniter.cart::default.checkout.label_address_1', 'requiredIf:address_id,0|min:3|max:128'];
            $namedRules[] = ['address.city', 'lang:igniter.cart::default.checkout.label_city', 'requiredIf:address_id,0|min:2|max:128'];
            $namedRules[] = ['address.state', 'lang:igniter.cart::default.checkout.label_state', 'max:128'];
            $namedRules[] = ['address.postcode', 'lang:igniter.cart::default.checkout.label_postcode', 'requiredIf:address_id,0|min:2|max:10'];
            $namedRules[] = ['address.country_id', 'lang:igniter.cart::default.checkout.label_country', 'sometimes|requiredIf:address_id,0|integer'];
        }

        return $namedRules;
    }

    protected function isCheckoutSuccessPage()
    {
        return $this->page->getBaseFileName() == $this->property('successPage');
    }

    protected function isOrderMarkedAsProcessed()
    {
        $order = $this->getOrder();
        if (!$order->isPaymentProcessed())
            return FALSE;

        $successPage = $this->property('successPage');

        return Redirect::to($order->getUrl($successPage));
    }

    protected function getAddressFromRequest(&$data)
    {
        $addressId = array_get($data, 'address_id');
        if (empty($addressId)) {
            if (isset($data['address']))
                $data['address']['country_id'] = $data['address']['country_id'] ?? setting('country_id');

            return array_get($data, 'address', []);
        }

        $data['address'] = ($model = Addresses_model::find($addressId))
            ? $model->toArray() : null;

        return $data['address'];
    }
}