<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class rwAcceptance extends Model implements AuditableContract
{
    protected $primaryKey = 'acc_id';

    use AsSource, Filterable, Attachable, HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'acc_domain_id',
        'acc_status',
        'acc_ext_id',
        'acc_user_id',
        'acc_wh_id',
        'acc_date',
        'acc_type',
        'acc_domain_id',
        'acc_shop_id',
        'acc_comment',
        'acc_count_expected',
        'acc_count_accepted',
        'acc_count_placed',
        'acc_transaction_id',
    ];

    protected $allowedFilters = [
        'acc_id' => Where::class,
        'acc_status' => Where::class,
        'acc_type' => Where::class,
        'acc_ext_id' => Where::class,
        'acc_date' => Like::class,
        'acc_wh_id' => Where::class,
        'acc_comment' => Like::class,
    ];

    protected $allowedSorts = [
        'acc_status',
        'acc_ext_id',
        'acc_wh_id',
        'acc_date',
        'acc_type',
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
        'ao_expected'           => [
            'name'        => 'ao_expected',
            'description' => 'Количество ожидаемого товара',
            'type'        => 'поле обязательно',
            'defaultValue'      => '-',
        ],
        'ao_barcode'           => [
            'name'        => 'ao_barcode',
            'description' => 'Штрих-код товара. Если поле будет пустым или его не будет, то программа возьмет штрих-код из товара, если таковой имеется.',
            'type'        => 'необязательно',
            'defaultValue'      => '-',
        ],
        'ao_price'         => [
            'name'        => 'oa_price',
            'description' => 'Закупочная стоимость товара.',
            'type'        => 'необязательно',
            'defaultValue'      => '-',
        ],
        'ao_batch'          => [
            'name'        => 'oa_batch',
            'description' => 'Батч или партия - это группа товаров, произведённых или поступивших одновременно и имеющих одинаковые характеристики (дата производства, срок годности, поставщик и т.п.).',
            'type'        => 'необязательно',
            'defaultValue'      => '-',
        ],
        'ao_expiration_date' => [
            'name'        => 'oa_expiration_date',
            'description' => 'Срок годности товара в формате (YYYY-MM-DD).',
            'type'        => 'необязательно',
            'defaultValue'      => '-',
        ],
    ];

    /**
     * @var array
     */
    public static function getImportDescriptions(): array
    {
        return (new self())->importDescriptions;
    }

    public static function perPage(): int
    {
        return 50;
    }

    public function getAccStatus() {
        return $this->hasOne(rwLibAcceptStatus::class, 'las_id', 'acc_status');
    }
    public function getAccType() {
        return $this->hasOne(rwLibAcceptType::class, 'lat_id', 'acc_type');
    }
    public function getWarehouse() {
        return $this->hasOne(rwWarehouse::class, 'wh_id', 'acc_wh_id');
    }

    public function getShop() {
        return $this->hasOne(rwShop::class, 'sh_id', 'acc_shop_id');
    }

    public function getUser() {
        return $this->hasOne(User::class, 'id', 'acc_user_id');
    }
    public function offers()
    {
        return $this->hasMany(rwAcceptanceOffer::class, 'ao_acceptance_id', 'acc_id');
    }
}
