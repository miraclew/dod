<?php
/**
 * 元宝(钻石)
 *  
 * @property int $id
 * @property string $name
 * @property string $image
 * @property string $voice
 * @property string $short_description
 * @property string $description
 * @property int $quantity
 * @property float $price
 * @property float $original_price
 * @property int $add_points
 * @property int $show_order
 * @property int $discount
 * @property string $product_id
 * @property boolean $is_on_sale
 * @property datetime $created
 * @property datetime $modified
 */
class GoldStore extends Model {
    public static $useTable = 'gold_store';
    public static $useDbConfig = 'store';
    
    
}
