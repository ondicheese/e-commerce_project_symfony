<?php
namespace App\Services;

use App\Repository\CarrierRepository;
use App\Repository\ProductRepository;
use App\Repository\SettingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $session;

    public function __construct(private RequestStack $requestStack, private ProductRepository $productRepo, private CarrierRepository $carrierRepo, private SettingRepository $settingRepo)
    {
        $this->session = $requestStack->getSession();
    }

    public function get(string $key)
    {
        return $this->session->get($key, []);
    }

    public function addToCart(string $productId, int $count = 1)
    {
        $cart = $this->get('cart');

        if (!empty($cart[$productId])) {
            $cart[$productId] += $count;
        } else {
            $cart[$productId] = $count;
        }

        $this->update('cart', $cart);
    }

    public function update(string $key, array $cart)
    {
        return $this->session->set($key, $cart);
    }

    public function removeFromCart(string $productId, int $count = 1)
    {
        $cart = $this->get('cart');

        if (isset($cart[$productId])) {
            if ($cart[$productId] <= $count) {
                unset($cart[$productId]);
            } else {
                $cart[$productId] -= $count;
            }
        }

        $this->update('cart', $cart);
    }

    public function emptyCart()
    {
        $this->update('cart', []);
    }

    public function getCartDetails()
    {
        $cart = $this->get('cart');
        $result = [
            'items' => [],
            'sub_total' => 0,
            'cart_count' => 0,
            'carrier' => "",
            'tax' => "",
            'sub_total_ht' => ""
        ];
        $sub_total = 0;
        $tax_rate = 0;

        if ($setting = $this->settingRepo->findOneBy(['website_name'=>'Mongoni'])) {
            $tax_rate = $setting->getTaxeRate() / 100;
        }

        foreach ($cart as $productId => $quantity) {
            $product = $this->productRepo->find($productId);
            if ($product) {
                $current_sub_total = $product->getSalePrice() * $quantity / 100;
                $sub_total += $current_sub_total;
                $result['items'][] = [
                    'product' => [
                        'id' => $product->getId(),
                        'name' => $product->getName(),
                        'slug' => $product->getSlug(),
                        'description' => $product->getDescription(),
                        'imageUrls' => $product->getImageUrls(),
                        'salePrice' => $product->getSalePrice(),
                        'regularPrice' => $product->getRegularPrice()
                    ],
                    'quantity' => $quantity,
                    'sub_total_ht' => round($current_sub_total / (1 + $tax_rate), 2),
                    'tax' => round($current_sub_total / (1 + $tax_rate) * $tax_rate, 2),
                    'sub_total' => $current_sub_total,
                ];
                $result['sub_total'] = $sub_total;
                $result['sub_total_ht'] = round($sub_total / (1 + $tax_rate), 2);
                $result['tax'] = round($tax_rate * $result['sub_total_ht'], 2);
                $result['cart_count'] += $quantity;
            } else {
                unset($cart[$productId]);
                $this->update('cart', $cart);
            }

            $carrier = $this->get('carrier');
            if (!$carrier) {
                $carrier = $this->carrierRepo->findAll()[0];
                $carrier = [
                    'id' => $carrier->getId(),
                    'name' => $carrier->getName(),
                    'price' => ($carrier->getPrice() / 100),
                    'description' => $carrier->getDescription()
                ];
                
                $this->update('carrier', $carrier);
            }
            
            $result['carrier'] = $carrier;
            $result['sub_total_with_carrier'] = ($result['sub_total'] + $carrier['price']);
        }

        return $result;
    }
}