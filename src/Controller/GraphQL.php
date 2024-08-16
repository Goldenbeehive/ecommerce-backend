<?php
namespace App\Controller;

use App\Database\DatabaseFactory;
use GraphQL\Type\Definition\InputObjectType;
use PDO;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;

use GraphQL\Error\DebugFlag;

class GraphQL
{
    private $dbFactory;

    public function __construct(PDO $db)
    {
        $this->dbFactory = new DatabaseFactory($db);
    }

    private function getCategoryType($name = 'Category')
    {
        return new ObjectType([
            'name' => $name,
            'fields' => [
                'id' => Type::int(),
                'name' => Type::string()
            ]
        ]);
    }

    private function getPriceType($name = 'Price')
    {
        return new ObjectType([
            'name' => $name,
            'fields' => [
                'id' => Type::int(),
                'product_id' => Type::string(),
                'amount' => Type::float(),
                'currency' => Type::string()
            ]
        ]);
    }

    private function getAttributeType($name = 'Attribute')
    {
        return new ObjectType([
            'name' => $name,
            'fields' => [
                'id' => Type::int(),
                'product_id' => Type::string(),
                'name' => Type::string(),
                'type' => Type::string(),
                'items' => Type::string()
            ]
        ]);
    }

    private function getGalleryType($name = 'GalleryItem')
    {
        return new ObjectType([
            'name' => $name,
            'fields' => [
                'id' => Type::int(),
                'product_id' => Type::string(),
                'image_url' => Type::string()
            ]
        ]);
    }
    private function getCartInputType($name = 'CartInput')
    {
        return new InputObjectType([
            'name' => $name,
            'fields' => [
                'product' => Type::nonNull($this->getProductInputType()),
                'selectedAttributes' => Type::string(),
                'quantity' => Type::nonNull(Type::int()),
            ]
        ]);
    }private function getOrderDetailsType()
    {
        return new ObjectType([
            'name' => 'OrderDetails',
            'fields' => [
                'id' => Type::int(),
                'order_id' => Type::int(),
                'product_id' => Type::string(),
                'quantity' => Type::int(),
                'selected_attributes' => Type::string(),
            ]
        ]);
    }
    private function getProductInputType()
    {
        return new InputObjectType([
            'name' => 'ProductInput',
            'fields' => [
                'id' => Type::nonNull(Type::string()),
                'name' => Type::string(),
                'inStock' => Type::boolean(),
                'description' => Type::string(),
                'brand' => Type::string(),
            ]
        ]);
    }
    private function getProductType($name = 'Product')
    {
        return new ObjectType([
            'name' => $name,
            'fields' => [
                'id' => Type::string(),
                'name' => Type::string(),
                'inStock' => Type::boolean(),
                'description' => Type::string(),
                'category_id' => Type::int(),
                'brand' => Type::string(),
                'prices' => [
                    'type' => Type::listOf($this->getPriceType("price_" . $name)),
                    'resolve' => function ($product) {
                        $productModel = $this->dbFactory->createModel('Product');
                        return $productModel->getPrices($product['id']);
                    }
                ],
                'attributes' => [
                    'type' => Type::listOf($this->getAttributeType("atrib_" . $name)),
                    'resolve' => function ($product) {
                        $productModel = $this->dbFactory->createModel('Product');
                        return $productModel->getAttributes($product['id']);
                    }
                ],
                'gallery' => [
                    'type' => Type::listOf($this->getGalleryType("gal_" . $name)),
                    'resolve' => function ($product) {
                        $productModel = $this->dbFactory->createModel('Product');
                        return $productModel->getGalleryOne($product['id']);
                    }
                ]
            ]
        ]);
    }

    public function handle()
    {
        try {
            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'Product' => [
                        'type' => $this->getProductType("single_prod"),
                        'args' => [
                            'id' => ['type' => Type::nonNull(Type::string())],
                        ],
                        'resolve' => function ($root, $args) {
                            $ProductModel = $this->dbFactory->createModel('Product');
                            $Productitem = $ProductModel->getById($args['id']);
                            if ($Productitem === null) {
                                throw new RuntimeException('Product not found');
                            }
                            return $Productitem;
                        },
                    ],
                    'allCategories' => [
                        'type' => Type::listOf($this->getCategoryType()),
                        'args' => [

                        ],
                        'resolve' => function ($root, $args) {
                            $CategoryModel = $this->dbFactory->createModel('Category');
                            $Categoryitems = $CategoryModel->getAll();
                            if ($Categoryitems === null) {
                                throw new RuntimeException('Category not found');
                            }
                            return $Categoryitems;
                        },
                    ],
                    'allProducts' => [
                        'type' => Type::listOf($this->getProductType()),
                        'args' => [

                        ],
                        'resolve' => function ($root, $args) {
                            $ProductModel = $this->dbFactory->createModel('Product');
                            $Productitems = $ProductModel->getAll();
                            if ($Productitems === null) {
                                throw new RuntimeException('Products not found');
                            }
                            return $Productitems;
                        },
                    ],
                    'galleryOfProduct' => [
                        'type' => Type::listOf($this->getGalleryType('gallery_prod')),
                        'args' => [
                            'id' => ['type' => Type::nonNull(Type::string())],
                        ],
                        'resolve' => function ($root, $args) {
                            $GalleryModel = $this->dbFactory->createModel('Gallery');
                            $Galleryitems = $GalleryModel->getByProductId($args['id']);
                            if ($Galleryitems === null) {
                                throw new RuntimeException('Gallery item not found');
                            }
                            return $Galleryitems;
                        },
                    ],
                    'pricesOfProduct' => [
                        'type' => $this->getPriceType('price_prod'),
                        'args' => [
                            'id' => ['type' => Type::nonNull(Type::string())],
                        ],
                        'resolve' => function ($root, $args) {
                            $PriceModel = $this->dbFactory->createModel('Price');
                            $Priceitem = $PriceModel->getByProductId($args['id']);
                            if ($Priceitem === null) {
                                throw new RuntimeException('Price item not found');
                            }
                            return $Priceitem;
                        },
                    ],
                    'AttributesOfProduct' => [
                        'type' => Type::listOf($this->getAttributeType('Attrib_prod')),
                        'args' => [
                            'id' => ['type' => Type::nonNull(Type::string())],
                        ],
                        'resolve' => function ($root, $args) {
                            $AttribModel = $this->dbFactory->createModel('Attribute');
                            $AttribItem = $AttribModel->getByProductId($args['id']);
                            if ($AttribItem === null) {
                                throw new RuntimeException('Attrib item not found');
                            }
                            return $AttribItem;
                        },
                    ],

                ],
            ]);
            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'addToCart' => [
                        'type' => new ObjectType([
                            'name' => 'AddToCartResponse',
                            'fields' => [
                                'success' => Type::boolean(),
                                'message' => Type::string(),
                                'orderDetails' => Type::listOf($this->getOrderDetailsType()),
                            ]
                        ]),
                        'args' => [
                            'cart' => ['type' => Type::nonNull(Type::listOf(Type::nonNull($this->getCartInputType())))],
                        ],
                        'resolve' => function ($root, $args) {
                            $orderModel = $this->dbFactory->createModel('Order');
                            $orderDetailsModel = $this->dbFactory->createModel('OrderDetails');

                          
                            $totalAmount = 0;
                            $currency = '';
                            foreach ($args['cart'] as $item) {
                                $priceModel = $this->dbFactory->createModel('Price');
                                $price = $priceModel->getOneByProductId($item['product']['id']);
                                $totalAmount += $price['amount'] * $item['quantity'];
                                $currency = $price['currency'];
                            }

                            $orderId = $orderModel->create([
                                'total' => $totalAmount,
                                'currency' => $currency,
                            ]);

                            $orderDetails = [];
                            foreach ($args['cart'] as $item) {
                                $orderDetail = $orderDetailsModel->create([
                                    'order_id' => $orderId,
                                    'product_id' => $item['product']['id'],
                                    'quantity' => $item['quantity'],
                                    'selected_attributes' => json_encode($item['selectedAttributes']),
                                ]);
                                $orderDetails[] = $orderDetail;
                            }

                            return [
                                'success' => true,
                                'message' => 'Order created successfully',
                                'orderDetails' => $orderDetails,
                            ];
                        },
                    ],
                ],
            ]);

            $schema = new Schema(
                (new SchemaConfig())
                    ->setQuery($queryType)
                    ->setMutation($mutationType)
            );

            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }

            $input = json_decode($rawInput, true);
            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;

            $rootValue = ['prefix' => 'You said: '];
            $result = GraphQLBase::executeQuery($schema, $query, $rootValue, null, $variableValues)->toArray(DebugFlag::INCLUDE_DEBUG_MESSAGE);
            ;
            $output = $result;
        } catch (Throwable $e) {
            $output = [
                'errors' => [
                    [
                        'message' => $e->getMessage(),

                    ],
                ],
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($output);
    }
}
