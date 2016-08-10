<?php

namespace Store\Cart;

require_once __DIR__ . '/product.php';

/**
 * NOTE: items stored in cart cookie are in the JSON string form of:
 * { <product id>-<variant id>: <quantity> }
 * but any all PHP arrays of the cart object will be in the form of:
 * { <product id>-<variant id> => {
 *     quantity: <quantity>
 *     product: <product array>
 *     variant: <variant array>
 * }}
 */

/**
 * get_cart
 * Returns current parsed cart cookie
 *
 * @return Array list of products
 */
function get_cart () {
    $products = \Store\Product\do_open();

    if (!isset($_COOKIE['cart'])) {
        $cart = array();
    } else {
        $cart = json_decode($_COOKIE['cart'], true);
    }

    $f = [];
    foreach ($cart as $id => $quantity) {
        list($i, $v) = explode('-', $id, 2);

        if (!isset($products[$i])) continue;

        $key = array_search($v, array_column($products[$i]['variants'], 'id'));

        if ($key === null) continue;

        $f[$id] = array(
            'product' => $products[$i],
            'variant' => $products[$i]['variants'][$key],
            'quantity' => $quantity
        );
    }

    return $f;
}

/**
 * get_subtotal
 * Returns the current price of the cart without shipping or extras
 *
 * @return Float price of cart
 */
function get_subtotal () {
    $cart = get_cart();
    $price = 0;

    foreach ($cart as $item) {
        $price = $price + ($item['quantity'] * $item['variant']['price']);
    }

    return number_format((float) $price, 2);
}

/**
 * set_cart
 * Sets the cart cookie
 *
 * @param Array $c list of cart items
 *
 * @return Boolean true if cookie was set
 */
function set_cart (array $c) {
    $f = [];
    foreach ($c as $id => $item) {
        if (isset($item['quantity']) && (int) $item['quantity'] > 0) {
            $f[$id] = $item['quantity'];
        }
    }

    if (count($f) > 0) {
        return setcookie('cart', json_encode($f), time() + 315360000, '/', '', 0, 1);
    } else {
        return setcookie('cart', false, 1, '/', '', 0, 1);
    }
}

/**
 * do_parse
 * Parses (usually a POST) array of data for cart information
 * Used instead of cookies to avoid weird missmatches during checkout process
 *
 * @param Array $c list of inputs to parse
 *
 * @return Array list of cart items
 */
function do_parse (array $c) {
    $items = [];

    foreach($c as $name => $value) {
        preg_match('/product-([0-9]+\-[0-9]+)-id/', $name, $matches);

        if ($matches && isset($c["product-$matches[1]-quantity"])) {
            $items[$matches[1]] = array(
                'quantity' => $c["product-$matches[1]-quantity"]
            );
        }
    }

    set_cart($items);
    return get_cart();
}

/**
 * set_quantity
 * sets the quantity on product in cart
 *
 * @param Int $i product id
 * @param Int $v product variant id
 * @param Int $q quantity of product to set
 *
 * @return Boolean true if cookie was set
 *
 * @throws Exception on bad product or variant param given
 */
function set_quantity (int $i, int $v, int $q = 1) {
    $cart = get_cart();
    $products = \Store\Product\do_open();

    if (!isset($products[$i])) {
        throw new \Exception('Product id does not exist');
    }

    $key = array_search($v, array_column($products[$i]['variants'], 'id'));

    if ($key === null) {
        throw new \Exception('Variant id does not exist');
    }

    $id = $i . '-' . $v;

    if (!isset($cart[$id])) {
        $cart[$id] = array(
            'product' => $products[$i],
            'variant' => $products[$i]['variants'][$key]
        );
    }

    $cart[$id]['quantity'] = $q;

    return set_cart($cart);
}

/**
 * set_add
 * adds to quantity in cart
 *
 * @param Int $i product id
 * @param Int $v product variant id
 * @param Int $q quantity of product to add
 *
 * @return Boolean true if cookie was set
 *
 * @throws Exception on bad product or variant param given
 */
function set_add (int $i, int $v, int $q = 1) {
    $cart = get_cart();
    $id = $i . '-' . $v;

    if (isset($cart[$id])) {
        $q += $cart[$id]['quantity'];
    }

    return set_quantity($i, $v, $q);
}