<?php
/**
 * Handles calculation of sales tax on Orders.
 *
 * If you would like to make your own tax calculator,
 * create a subclass of this and enable it by using
 * {@link Order::set_modifiers()} in your project
 * _config.php file.
 *
 * Sample configuration in your _config.php:
 *
 * <code>
 *        //rate , name, isexclusive
 *        FlatTaxModifier::set_tax(0.15, 'GST', false);
 * </code>
 *
 * @package shop
 * @subpackage modifiers
 */
class FlatTaxModifier extends TaxModifier
{

        public static $db = array(
                'TaxType' => "Enum('Exclusive,Inclusive')" //deprecated
        );

        private static $name = null;
        private static $rate = null;
        private static $exclusive = null;

        private static $includedmessage = "%.1f%% %s (inclusive)";
        private static $excludedmessage = "%.1f%% %s";

        public function populateDefaults() {
                parent::populateDefaults();
                $this->Type = ($this->config()->exclusive) ? 'Chargable' : 'Ignored';
        }


        /**
         * Get the tax amount to charge on the order.
         *
         */
        public function value($incoming) {
                $this->Rate = $this->config()->rate;
                if ($this->config()->exclusive)
                        return $incoming * $this->Rate;
                return $incoming - round($incoming / (1 + $this->Rate), Config::inst()->get('Order', 'rounding_precision')); //inclusive tax requires a different calculation
        }

       public function TableTitle(){
                $title =  parent::TableTitle();
                if($this->Rate) {
                        $title = $this->config()->name . " @ " .  number_format($this->Rate * 100, 2). "%";
                        if (!$this->config()->exclusive) $title .= ' (inclusive)';
                }
                return $title;
        }

}