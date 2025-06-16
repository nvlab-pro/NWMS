<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDate;
use Orchid\Filters\Types\WhereMaxMin;
use Orchid\Filters\Types\WhereDateStartEnd;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class rwOffer extends Model implements AuditableContract
{
    protected $primaryKey = 'of_id';

    use AsSource, Filterable, Attachable, HasFactory, SoftDeletes, Auditable;

    protected static $recordEvents = [
        'created', // включаем создание в аудит
        'updated',
        'deleted',
    ];

    // Добавляем кастомные поля в аудит
    public function transformAudit(array $data): array
    {
        $data['object_id'] = $this->getKey(); // или $this->of_id если нужно явно

        return $data;
    }

    // Другие разрешённые для массового присвоения атрибуты
    protected $fillable = [
        'of_ext_id',
        'of_domain_id',
        'of_shop_id',
        'of_status',
        'of_name',
        'of_article',
        'of_sku',
        'of_price',
        'of_estimated_price',
        'of_datamarix',
        'of_img',
        'of_dimension_x',
        'of_dimension_y',
        'of_dimension_z',
        'of_weight',
        'of_datamatrix',
        'of_comment',
    ];

    protected $allowedFilters = [
        'of_id'            => Where::class,
        'of_shop_id'       => Where::class,
        'of_ext_id'        => Where::class,
        'of_status'        => Where::class,
        'of_article'        => Like::class,
        'of_sku'            => Like::class,
        'of_name'           => Like::class,
        'of_price'          => Where::class,
        'of_estimated_price'    => Where::class,
    ];

    protected $importDescriptions = [
        'of_id'             => [
            'name'              => 'of_id',
            'description'       => 'ID товара в системе. Необязательное, но если указано, будет использоваться для обновления товара.',
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
            'description' => 'SKU товара. Необязательное. Может использоваться для обновления товара, если товар с таким SKU уже существует и не задано поле of_id.',
            'type'        => 'ключ, приоритет 3',
            'defaultValue'      => '-',
        ],
        'of_article'        => [
            'name'        => 'of_article',
            'description' => 'Артикул товара. Необязательное. Может использоваться для обновления товара, если товар с таким артикулом уже существует и не заданы поля of_id или of_sku.',
            'type'        => 'ключ, приоритет 4',
            'defaultValue'      => '-',
        ],
        'of_name'           => [
            'name'        => 'of_name',
            'description' => 'Название товара.',
            'type'        => 'поле обязательно',
            'defaultValue'      => '-',
        ],
        'of_status'         => [
            'name'        => 'of_status',
            'description' => 'Статус товара. 1 - активный, 2 - отключен, 3 - удален.',
            'type'        => 'необязательно (активный по умолчанию)',
            'defaultValue'      => '1',
        ],
        'of_barcode'          => [
            'name'        => 'of_barcode',
            'description' => 'Штрих-код товара. Если штрих-кодов несколько, то укажите их через запятую.',
            'type'        => 'необязательно',
            'defaultValue'      => '-',
        ],
        'of_price'          => [
            'name'        => 'of_price',
            'description' => 'Стоимость товара.',
            'type'        => 'необязательно',
            'defaultValue'      => '0',
        ],
        'of_estimated_price' => [
            'name'        => 'of_estimated_price',
            'description' => 'Оценочная стоимость товара.',
            'type'        => 'необязательно',
            'defaultValue'      => '0',
        ],
        'of_datamatrix'      => [
            'name'        => 'of_datamatrix',
            'description' => 'Использование Datamatrix. 1 - да, 0 - нет.',
            'type'        => 'необязательно',
            'defaultValue'      => '0',
        ],
        'of_img'            => [
            'name'        => 'of_img',
            'description' => 'URL изображения товара.',
            'type'        => 'необязательно',
            'defaultValue'      => '0',
        ],
        'of_comment'        => [
            'name'        => 'of_comment',
            'description' => 'Комментарий к товару.',
            'type'        => 'необязательно',
            'defaultValue'      => '-',
        ],
    ];

    /**
     * @var array
     */
    protected $allowedSorts = [
        'id',
    ];

    public static function getImportDescriptions(): array
    {
        return (new self())->importDescriptions;
    }

    public static function perPage(): int
    {
        return 50;
    }
    public function getStatus() {
        return $this->hasOne(rwLibStatus::class, 'ls_id', 'of_status');
    }

    public function getShop() {
        return $this->hasOne(rwShop::class, 'sh_id', 'of_shop_id');
    }

    public function barcodes()
    {
        return $this->hasMany(rwBarcode::class, 'br_offer_id', 'of_id');
    }
}
