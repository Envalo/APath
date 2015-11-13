<?php

class DataSources
{
    public static function getEngine()
    {
        return new Envalo_APath_Engine();
    }
    public static function getNode()
    {
        return new Envalo_APath_Node();
    }
    public static function getCondition($operator, $argument)
    {
        return new Envalo_APath_Condition($operator, $argument);
    }
    public static function getOrderData()
    {
        return array(
            'order' => array(
                'created_at' => '2015-10-28 14:46:00',
                'currency' => 'USD',
                'subtotal' => 130.00,
                'shipping' => 15.00,
                'tax' => 5,
                'total' => 150.00,
                'items' => array(
                    array('sku' => 'SKU1', 'warehouse_id' => 3, 'price' => 10.00, 'qty' => 3),
                    array('sku' => 'SKU2', 'warehouse_id' => 3, 'price' => 5.00, 'qty' => 2),
                    array('sku' => 'SKU3', 'warehouse_id' => 4, 'price' => 10.00, 'qty' => 5),
                    array('sku' => 'SKU4', 'warehouse_id' => 5, 'price' => 20.00, 'qty' => 2, 'options' =>array(
                        array('label' => 'Color', 'value' => 'Red'),
                        array('label' => 'Size', 'value' => 'Small'),
                    )),
                ),
                'bill_to' => array(
                    'name' => 'Mary Anyperson',
                    'address' => '123 Imagination Drive',
                    'city' => 'Faketown',
                    'region' => 'OH',
                    'postcode' => '44999-9999'
                ),
                'ship_to' => array(
                    'name' => 'Frank Realhuman',
                    'address' => 'PO BOX 999999',
                    'city' => 'Existsville',
                    'region' => 'GA',
                    'postcode' => '31999-9999'
                )
            )
        );
    }

}