<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentBlock extends Model
{
    protected $fillable = [
        'page', 'type', 'heading', 'subheading', 'body',
        'image_url', 'link_label', 'link_url', 'sort_order', 'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_visible' => 'boolean',
        ];
    }

    /** Editable content pages (slug => label). */
    public const PAGES = [
        'welcome' => 'ໜ້າຫຼັກ (Landing)',
        'news' => 'ຂ່າວສານ ແລະ ປະກາດ',
        'community' => 'ຄອມມູນິຕີ',
        'about' => 'ກ່ຽວກັບພວກເຮົາ',
        'terms' => 'ເງື່ອນໄຂການນຳໃຊ້',
        'privacy' => 'ນະໂຍບາຍຄວາມເປັນສ່ວນຕົວ',
        'rules' => 'ກົດລະບຽບການນຳໃຊ້ ແລະ ຄອມມູນິຕີ',
        'withdrawal-terms' => 'ເງື່ອນໄຂການຖອນເງິນ',
        'faq' => 'ຄຳຖາມທີ່ພົບເລື້ອຍ (FAQ)',
        'contact' => 'ຕິດຕໍ່ພວກເຮົາ',
    ];

    /** Block types available to editable pages. */
    public const TYPES = [
        'hero' => 'ສ່ວນຫົວ (Hero)',
        'feature' => 'ກາດຟີເຈີ',
        'cta' => 'ປຸ່ມເຊີນຊວນ (CTA)',
        'richtext' => 'ເນື້ອຫາ (Rich text)',
        'faq' => 'ຄຳຖາມ-ຄຳຕອບ',
        'news' => 'ຂ່າວ / ປະກາດ',
        'community' => 'ຊ່ອງທາງຄອມມູນິຕີ',
        'contact' => 'ຊ່ອງທາງຕິດຕໍ່',
        'image' => 'ຮູບພາບ',
    ];

    public function scopeForPage($query, string $page)
    {
        return $query->where('page', $page);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }
}
