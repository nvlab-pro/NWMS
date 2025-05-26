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

    public static function getImportDescriptions(): array
    {
        return (new self())->importDescriptions;
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
