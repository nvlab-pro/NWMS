<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereBetween;
use Orchid\Filters\Types\WhereIn;
use Orchid\Screen\AsSource;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class rwOrder extends Model implements AuditableContract
{
    protected $primaryKey = 'o_id';

    use AsSource, Filterable, Attachable, HasFactory, SoftDeletes, Auditable;

    // Другие разрешённые для массового присвоения атрибуты
    protected $fillable = [
        'o_status_id',
        'o_domain_id',
        'o_parcel_id',
        'o_type_id',
        'o_client_id',
        'o_customer_type',
        'o_company_id',
        'o_ext_id',
        'o_shop_id',
        'o_user_id',
        'o_wh_id',
        'o_date',
        'o_date_send',
        'o_source_id',
        'o_count',
        'o_sum',
        'o_operation_user_id',
    ];

    protected $allowedFilters = [
        'o_id'       => Where::class,
        'o_status_id'       => Where::class,
        'o_parcel_id'       => Where::class,
        'o_type_id'         => Where::class,
        'o_client_id'       => Where::class,
        'o_customer_type'   => Where::class,
        'o_ext_id'          => Where::class,
        'o_shop_id'         => Where::class,
        'o_wh_id'           => Where::class,
        'o_date'            => WhereBetween::class,
        'o_date_send'       => WhereBetween::class,
    ];

    /**
     * @var array
     */
    protected $allowedSorts = [
        'o_status_id',
    ];

    protected $importDescriptions = [
        'title_1'              => [
            'name'              => 'Данные о заказе',
            'field'             => 'title',
        ],
        'order_ext_id'             => [
            'name'              => 'order_ext_id',
            'description'       => 'ID заказа в системе клиента. Необязательное поле, но если указано, то заказ будет создан с этим ext_id. Используется для загрузки нескольких заказов одним файлом.',
            'type'              => 'необязательно',
            'defaultValue'      => '-',

        ],
        'order_offer_type'             => [
            'name'              => 'order_offer_type',
            'description'       => 'Тип товара: 1 - товар, 2 - короб ',
            'type'              => 'необязательно',
            'defaultValue'      => '1',

        ],
        'order_date_send'      => [
            'name'              => 'order_date_send',
            'description'       => 'Дата отправки заказа. Формат: YYYY-MM-DD',
            'type'              => 'необязательно',
            'defaultValue'      => '-',

        ],
        'order_customer_type'      => [
            'name'              => 'order_customer_type',
            'description'       => 'Тип клиента: 0 - физическое лицо, 1 - юридическое лицо',
            'type'              => 'необязательно',
            'defaultValue'      => '-',

        ],
        'title_2'              => [
            'name'              => 'Юридическое лицо',
            'field'             => 'title',
        ],
        'order_company_id'      => [
            'name'              => 'order_company_id',
            'description'       => 'ID юридического лица (можно взять из соотвествующего раздела в системе)',
            'type'              => 'необязательно',
            'defaultValue'      => '-',
        ],
        'title_3'              => [
            'name'              => 'Физическое лицо',
            'field'             => 'title',
        ],
        'oc_first_name'      => [
            'name'              => 'oc_first_name',
            'description'       => 'Имя клиента',
            'type'              => 'необязательно',
            'defaultValue'      => '-',
        ],
        'oc_middle_name'      => [
            'name'              => 'oc_middle_name',
            'description'       => 'Отчество клиента',
            'type'              => 'необязательно',
            'defaultValue'      => '-',
        ],
        'oc_last_name'      => [
            'name'              => 'oc_last_name',
            'description'       => 'Фамилия клиента',
            'type'              => 'необязательно',
            'defaultValue'      => '-',
        ],
        'oc_phone'      => [
            'name'              => 'oc_phone',
            'description'       => 'Телефонный номер клиента',
            'type'              => 'необязательно',
            'defaultValue'      => '-',
        ],
        'oc_email'      => [
            'name'              => 'oc_email',
            'description'       => 'E-Mail клиента',
            'type'              => 'необязательно',
            'defaultValue'      => '-',
        ],
        'oc_city_id'      => [
            'name'              => 'oc_city_id',
            'description'       => 'ID города клиента',
            'type'              => 'необязательно',
            'defaultValue'      => '-',
        ],
        'oc_postcode'      => [
            'name'              => 'oc_postcode',
            'description'       => 'Индекс клиента',
            'type'              => 'необязательно',
            'defaultValue'      => '-',
        ],
        'oc_full_address'      => [
            'name'              => 'oc_full_address',
            'description'       => 'Полный адрес клиента',
            'type'              => 'необязательно',
            'defaultValue'      => '-',
        ],
        'title_4'              => [
            'name'              => 'Данные о способе доставки',
            'field'             => 'title',
        ],
        'ds_id'      => [
            'name'              => 'ds_id',
            'description'       => 'ID службы доставки',
            'type'              => 'необязательно',
            'defaultValue'      => '-',
        ],
        'ds_pp_id'      => [
            'name'              => 'ds_pp_id',
            'description'       => 'ID пункта самовывоза',
            'type'              => 'необязательно',
            'defaultValue'      => '-',
        ],
        'title_5'              => [
            'name'              => 'Данные о товаре',
            'field'             => 'title',
        ],
        'of_id'             => [
            'name'              => 'of_id',
            'description'       => 'ID товара в системе. Необязательное, но если указано, будет использоваться для поиска товара в первую очередь.',
            'type'              => 'ключ, приоритет 1',
            'defaultValue'      => '-',

        ],
        'of_ext_id'            => [
            'name'        => 'of_ext_id',
            'description' => 'Внешний ID товара (ID товара в системе клиента). Необязательное. Может использоваться для добавления в документ товара, если товар с таким внешним ID уже существует и не задано поле of_id.',
            'type'        => 'ключ, приоритет 2',
            'defaultValue'      => '-',
        ],
        'of_sku'            => [
            'name'        => 'of_sku',
            'description' => 'SKU товара. Необязательное. Может использоваться для обновления товара, если товар с таким SKU уже существует и не задано поле of_id или of_ext_id.',
            'type'        => 'ключ, приоритет 3',
            'defaultValue'      => '-',
        ],
        'of_article'        => [
            'name'        => 'of_article',
            'description' => 'Артикул товара. Необязательное. Может использоваться для обновления товара, если товар с таким артикулом уже существует и не заданы поля of_id, of_ext_id или of_sku.',
            'type'        => 'ключ, приоритет 4',
            'defaultValue'      => '-',
        ],
        'oo_qty'           => [
            'name'        => 'oo_qty',
            'description' => 'Количество отгружаемого товара',
            'type'        => 'поле обязательно',
            'defaultValue'      => '-',
        ],
        'oo_oc_price'           => [
            'name'        => 'oo_oc_price',
            'description' => 'Оценочная стоимость товара',
            'type'        => 'необязательно',
            'defaultValue'      => '0',
        ],
        'oo_price'           => [
            'name'        => 'oo_price',
            'description' => 'Стоимость товара',
            'type'        => 'необязательно',
            'defaultValue'      => '0',
        ],
//        'oo_expiration_date' => [
//            'name'        => 'oo_expiration_date',
//            'description' => 'Срок годности товара в формате (YYYY-MM-DD).',
//            'type'        => 'необязательно',
//            'defaultValue'      => '-',
//        ],
//        'oo_batch'           => [
//            'name'        => 'oo_batch',
//            'description' => 'Батч или партия - это группа товаров, произведённых или поступивших одновременно и имеющих одинаковые характеристики (дата производства, срок годности, поставщик и т.п.).',
//            'type'        => 'необязательно',
//            'defaultValue'      => '-',
//        ],
    ];


    protected $printImportDescriptions = [


        'title_0'              => [
            'name'              => 'Данные о заказе',
            'field'             => 'title',
        ],
        'order_id'              => [
            'name'              => '{order_id}',
            'field'             => 'o_id',
            'table'             => 'rw_orders',
            'description'       => 'Номер заказа в программе',
            'type'              => 'Число',
            'example'           => '239751',
        ],
        'order_ext_id'          => [
            'name'              => '{order_ext_id}',
            'field'             => 'o_ext_id',
            'table'             => 'rw_orders',
            'description'       => 'Номер заказа в программе клиента (внешний ID)',
            'type'              => 'Строка',
            'example'           => 'EXT1234',
        ],
        'order_date'            => [
            'name'              => '{order_date}',
            'field'             => 'o_date',
            'table'             => 'rw_orders',
            'description'       => 'Дата создания заказа в формате DD.MM.YYYY',
            'type'              => 'Дата',
            'example'           => '12.04.2025',
        ],
        'order_date_send'       => [
            'name'              => '{order_date_send}',
            'field'             => 'o_date_send',
            'table'             => 'rw_orders',
            'description'       => 'Дата отправки заказа в формате DD.MM.YYYY',
            'type'              => 'Дата',
            'example'           => '14.04.2025',
        ],

        'title_1'              => [
            'name'              => 'Данные о складе',
            'field'             => 'title',
        ],
        'executor_name'         => [
            'name'              => '{executor_name}',
            'field'             => '',
            'table'             => '',
            'description'       => 'Название компании хранителя',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'executor_inn'         => [
            'name'              => '{executor_inn}',
            'field'             => '',
            'table'             => '',
            'description'       => 'ИНН компании хранителя',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'executor_phone'         => [
            'name'              => '{executor_phone}',
            'field'             => '',
            'table'             => '',
            'description'       => 'Телефонный номер хранителя',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'executor_email'         => [
            'name'              => '{executor_email}',
            'field'             => '',
            'table'             => '',
            'description'       => 'E-mail хранителя',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'executor_address'         => [
            'name'              => '{executor_address}',
            'field'             => '',
            'table'             => '',
            'description'       => 'Название компании хранителя',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'executor_bank_account'         => [
            'name'              => '{executor_bank_account}',
            'field'             => '',
            'table'             => '',
            'description'       => 'Расчетный счет хранителя',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'executor_bank_ks'         => [
            'name'              => '{executor_bank_ks}',
            'field'             => '',
            'table'             => '',
            'description'       => 'Корреспондентский счет хранителя',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'executor_bank_name'         => [
            'name'              => '{executor_bank_name}',
            'field'             => '',
            'table'             => '',
            'description'       => 'Название банка хранителя',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'executor_bank_bic'         => [
            'name'              => '{executor_bank_bic}',
            'field'             => '',
            'table'             => '',
            'description'       => 'БИК банка хранителя',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'executor_contact_person'         => [
            'name'              => '{executor_contact_person}',
            'field'             => '',
            'table'             => '',
            'description'       => 'Контактное лицо хранителя',
            'type'              => 'строка',
            'example'           => '-',
        ],

        'title_2'              => [
            'name'              => 'Данные о клиенте',
            'field'             => 'title',
        ],
        'doc_id'         => [
            'name'              => '{doc_id}',
            'field'             => '',
            'table'             => '',
            'description'       => 'Номер договора продавца',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'dog_date'         => [
            'name'              => '{dog_date}',
            'field'             => '',
            'table'             => '',
            'description'       => 'Дата договора продавца',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'customer_name'         => [
            'name'              => '{customer_name}',
            'field'             => '',
            'table'             => '',
            'description'       => 'Название компании продавца',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'customer_inn'         => [
            'name'              => '{customer_inn}',
            'field'             => '',
            'table'             => '',
            'description'       => 'ИНН компании продавца',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'customer_phone'         => [
            'name'              => '{customer_phone}',
            'field'             => '',
            'table'             => '',
            'description'       => 'Телефон компании продавца',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'customer_email'         => [
            'name'              => '{customer_email}',
            'field'             => '',
            'table'             => '',
            'description'       => 'Адрес компании продавца',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'customer_address'         => [
            'name'              => '{customer_address}',
            'field'             => '',
            'table'             => '',
            'description'       => 'Адрес компании продавца',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'customer_bank_account'         => [
            'name'              => '{customer_bank_account}',
            'field'             => '',
            'table'             => '',
            'description'       => 'Расчетный счет продавца',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'customer_bank_ks'         => [
            'name'              => '{customer_bank_ks}',
            'field'             => '',
            'table'             => '',
            'description'       => 'Корреспондентский счет продавца',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'customer_bank_name'         => [
            'name'              => '{customer_bank_name}',
            'field'             => '',
            'table'             => '',
            'description'       => 'Название банка продавца',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'customer_bank_bic'         => [
            'name'              => '{customer_bank_bic}',
            'field'             => '',
            'table'             => '',
            'description'       => 'БИК банка продавца',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'customer_contact_person'         => [
            'name'              => '{customer_contact_person}',
            'field'             => '',
            'table'             => '',
            'description'       => 'Контактное лицо продавца',
            'type'              => 'строка',
            'example'           => '-',
        ],

        'title_3'              => [
            'name'              => 'Данные о покупателе',
            'field'             => 'title',
        ],
        'buyer_name'         => [
            'name'              => '{buyer_name}',
            'field'             => '',
            'table'             => '',
            'description'       => 'ФИО или название компании покупателя',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'buyer_inn'         => [
            'name'              => '{buyer_inn}',
            'field'             => '',
            'table'             => '',
            'description'       => 'ИНН компании (если покупатель юридическое лицо)',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'buyer_phone'         => [
            'name'              => '{buyer_phone}',
            'field'             => '',
            'table'             => '',
            'description'       => 'Телефонный номер покупателя',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'buyer_email'         => [
            'name'              => '{buyer_email}',
            'field'             => '',
            'table'             => '',
            'description'       => 'E-mail покупателя',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'buyer_city'         => [
            'name'              => '{buyer_city}',
            'field'             => '',
            'table'             => '',
            'description'       => 'Город покупателя',
            'type'              => 'строка',
            'example'           => '-',
        ],
        'buyer_address'         => [
            'name'              => '{buyer_address}',
            'field'             => '',
            'table'             => '',
            'description'       => 'Адрес покупателя',
            'type'              => 'строка',
            'example'           => '-',
        ],

        'title_4'              => [
            'name'              => 'Данные о службе доставки',
            'field'             => 'title',
        ],
        'ds_name'              => [
            'name'              => '{ds_name}',
            'field'             => 'ds_name',
            'table'             => 'rw_orders',
            'description'       => 'Названрие службы доставки',
            'type'              => 'строка',
            'example'           => '-',
        ],

        'title_5'              => [
            'name'              => 'Список товаров (в разрезе артикула)',
            'field'             => 'title',
        ],
        'offer_num'              => [
            'name'              => '[offer_num]',
            'field'             => 'num',
            'table'             => '',
            'description'       => 'Порядковый номер товара в документе.',
            'type'              => 'список',
            'example'           => '-',
        ],
        'offer_id'              => [
            'name'              => '[offer_id]',
            'field'             => 'oo_id',
            'table'             => 'rw_order_offers',
            'description'       => 'ID товара. Выводится несколько строк (список ID товаров, на каждой строке по товару).',
            'type'              => 'список',
            'example'           => '-',
        ],
        'of_name'               => [
            'name'              => '[offer_name]',
            'field'             => 'of_name',
            'table'             => 'rw_offers',
            'description'       => 'Название товара. Выводится несколько строк (список названий товаров, на каждой строке по товару).',
            'type'              => 'список',
            'example'           => '-',
        ],
        'offer_article'         => [
            'name'              => '[offer_article]',
            'field'             => 'of_article',
            'table'             => 'rw_offers',
            'description'       => 'Артикул. Выводится несколько строк (список артикулов товаров, на каждой строке по артикулу).',
            'type'              => 'список',
            'example'           => '-',
        ],
        'offer_sku'             => [
            'name'              => '[offer_sku]',
            'field'             => 'of_sku',
            'table'             => 'rw_offers',
            'description'       => 'SKU товара. Выводится несколько строк (список SKU товаров, на каждой строке по SKU).',
            'type'              => 'список',
            'example'           => '-',
        ],
        'offer_qty'             => [
            'name'              => '[offer_qty]',
            'field'             => 'oo_qty',
            'table'             => 'rw_order_offers',
            'description'       => 'Количество отправленного товара. Выводится несколько строк.',
            'type'              => 'число',
            'example'           => '-',
        ],
        'offer_oc_price'        => [
            'name'              => '[offer_oc_price]',
            'field'             => 'oo_oc_price',
            'table'             => 'rw_order_offers',
            'description'       => 'Оценочная стоимость товара. Выводится несколько строк (список оценочной стоимости товаров, на каждой строке по стоимости).',
            'type'              => 'число',
            'example'           => '-',
        ],
        'offer_price'           => [
            'name'              => '[offer_price]',
            'field'             => 'oo_price',
            'table'             => 'rw_order_offers',
            'description'       => 'Стоимость товара. Выводится несколько строк (список стоимости товаров, на каждой строке по стоимости).',
            'type'              => 'число',
            'example'           => '-',
        ],
        'offer_dimension_x'           => [
            'name'              => '[offer_dimension_x]',
            'field'             => 'of_dimension_x',
            'table'             => 'rw_offers',
            'description'       => 'Ширина товара. Выводится несколько строк.',
            'type'              => 'число',
            'example'           => '-',
        ],
        'offer_dimension_y'     => [
            'name'              => '[offer_dimension_y]',
            'field'             => 'of_dimension_y',
            'table'             => 'rw_offers',
            'description'       => 'Длина товара. Выводится несколько строк.',
            'type'              => 'число',
            'example'           => '-',
        ],
        'offer_dimension_z'     => [
            'name'              => '[offer_dimension_z]',
            'field'             => 'of_dimension_z',
            'table'             => 'rw_offers',
            'description'       => 'Глубина товара. Выводится несколько строк.',
            'type'              => 'число',
            'example'           => '-',
        ],
        'offer_weight'          => [
            'name'              => '[offer_weight]',
            'field'             => 'of_weight',
            'table'             => 'rw_offers',
            'description'       => 'Вес товара. Выводится несколько строк.',
            'type'              => 'число',
            'example'           => '-',
        ],
        'offer_sum'          => [
            'name'              => '[offer_sum]',
            'field'             => 'of_weight',
            'table'             => 'rw_offers',
            'description'       => 'Сумма товара (цена * количество). Выводится несколько строк.',
            'type'              => 'число',
            'example'           => '-',
        ],


        'title_6'              => [
            'name'              => 'Список товаров (в разрезе партии)',
            'field'             => 'title',
        ],
        'offer_part_num'              => [
            'name'              => '[offer_part_num]',
            'field'             => 'num',
            'table'             => '',
            'description'       => 'Порядковый номер товара в документе.',
            'type'              => 'список',
            'example'           => '-',
        ],
        'offer_part_id'              => [
            'name'              => '[offer_part_id]',
            'field'             => 'oo_id',
            'table'             => 'rw_order_offers',
            'description'       => 'ID товара. Выводится несколько строк (список ID товаров, на каждой строке по товару).',
            'type'              => 'список',
            'example'           => '-',
        ],
        'of_part_name'               => [
            'name'              => '[offer_part_name]',
            'field'             => 'of_name',
            'table'             => 'rw_offers',
            'description'       => 'Название товара. Выводится несколько строк (список названий товаров, на каждой строке по товару).',
            'type'              => 'список',
            'example'           => '-',
        ],
        'offer_part_article'         => [
            'name'              => '[offer_part_article]',
            'field'             => 'of_article',
            'table'             => 'rw_offers',
            'description'       => 'Артикул. Выводится несколько строк (список артикулов товаров, на каждой строке по артикулу).',
            'type'              => 'список',
            'example'           => '-',
        ],
        'offer_part_sku'             => [
            'name'              => '[offer_part_sku]',
            'field'             => 'of_sku',
            'table'             => 'rw_offers',
            'description'       => 'SKU товара. Выводится несколько строк (список SKU товаров, на каждой строке по SKU).',
            'type'              => 'список',
            'example'           => '-',
        ],
        'offer_part_qty'             => [
            'name'              => '[offer_part_qty]',
            'field'             => 'oo_qty',
            'table'             => 'rw_order_offers',
            'description'       => 'Количество отправленного товара. Выводится несколько строк.',
            'type'              => 'число',
            'example'           => '-',
        ],
        'offer_part_oc_price'        => [
            'name'              => '[offer_part_oc_price]',
            'field'             => 'oo_oc_price',
            'table'             => 'rw_order_offers',
            'description'       => 'Оценочная стоимость товара. Выводится несколько строк (список оценочной стоимости товаров, на каждой строке по стоимости).',
            'type'              => 'число',
            'example'           => '-',
        ],
        'offer_part_price'           => [
            'name'              => '[offer_part_price]',
            'field'             => 'oo_price',
            'table'             => 'rw_order_offers',
            'description'       => 'Стоимость товара. Выводится несколько строк (список стоимости товаров, на каждой строке по стоимости).',
            'type'              => 'число',
            'example'           => '-',
        ],
        'offer_part_expiration_date' => [
            'name'              => '[offer_part_expiration_date]',
            'field'             => 'oo_expiration_date',
            'table'             => 'rw_order_offers',
            'description'       => 'Срок годности товара. Выводится несколько строк. Формат DD.MM.YYYY',
            'type'              => 'дата',
            'example'           => '-',
        ],
        'offer_part_batch'           => [
            'name'              => '[offer_part_batch]',
            'field'             => 'oo_batch',
            'table'             => 'rw_order_offers',
            'description'       => 'Партия товара. Выводится несколько строк.',
            'type'              => 'список',
            'example'           => '-',
        ],
        'offer_part_dimension_x'           => [
            'name'              => '[offer_part_dimension_x]',
            'field'             => 'of_dimension_x',
            'table'             => 'rw_offers',
            'description'       => 'Ширина товара. Выводится несколько строк.',
            'type'              => 'число',
            'example'           => '-',
        ],
        'offer_part_dimension_y'     => [
            'name'              => '[offer_part_dimension_y]',
            'field'             => 'of_dimension_y',
            'table'             => 'rw_offers',
            'description'       => 'Длина товара. Выводится несколько строк.',
            'type'              => 'число',
            'example'           => '-',
        ],
        'offer_part_dimension_z'     => [
            'name'              => '[offer_part_dimension_z]',
            'field'             => 'of_dimension_z',
            'table'             => 'rw_offers',
            'description'       => 'Глубина товара. Выводится несколько строк.',
            'type'              => 'число',
            'example'           => '-',
        ],
        'offer_part_weight'          => [
            'name'              => '[offer_part_weight]',
            'field'             => 'of_weight',
            'table'             => 'rw_offers',
            'description'       => 'Вес товара. Выводится несколько строк.',
            'type'              => 'число',
            'example'           => '-',
        ],
        'offer_part_sum'          => [
            'name'              => '[offer_part_sum]',
            'field'             => 'of_weight',
            'table'             => 'rw_offers',
            'description'       => 'Сумма товара (цена * количество). Выводится несколько строк.',
            'type'              => 'число',
            'example'           => '-',
        ],

        'title_7'              => [
            'name'              => 'Итоговые стоимости',
            'field'             => 'title',
        ],
        'total_qty'              => [
            'name'              => '{total_qty}',
            'field'             => '',
            'table'             => '',
            'description'       => 'Итоговое количество товара в заказе.',
            'type'              => 'число',
            'example'           => '-',
        ],
        'total_sum'              => [
            'name'              => '{total_sum}',
            'field'             => '',
            'table'             => '',
            'description'       => 'Итоговая стоимость товара в заказе.',
            'type'              => 'число',
            'example'           => '-',
        ],
    ];

    public static function getImportDescriptions(): array
    {
        return (new self())->importDescriptions;
    }

    public static function getPrintImportDescriptions(): array
    {
        return (new self())->printImportDescriptions;
    }

    public function getContact()
    {
        return $this->hasOne(rwOrderContact::class, 'oc_order_id', 'o_id');
    }

    public function getCompany() {
        return $this->hasOne(rwCompany::class, 'co_id', 'o_company_id');
    }

    public function getShop() {
        return $this->hasOne(rwShop::class, 'sh_id', 'o_shop_id');
    }

    public function getStatus() {
        return $this->hasOne(rwOrderStatus::class, 'os_id', 'o_status_id');
    }

    public function getType() {
        return $this->hasOne(rwOrderType::class, 'ot_id', 'o_type_id');
    }

    public function getWarehouse() {
        return $this->hasOne(rwWarehouse::class, 'wh_id', 'o_wh_id');
    }

    public function getPlace() {
        return $this->hasOne(rwPlace::class, 'pl_id', 'o_order_place');
    }
    public function offers()
    {
        return $this->hasMany(rwOrderOffer::class, 'oo_order_id', 'o_id');
    }
    public function getOperationUser()
    {
        return $this->hasOne(User::class, 'id', 'o_operation_user_id');
    }
}
