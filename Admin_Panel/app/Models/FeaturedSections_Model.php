<?php
namespace App\Models;

use CodeIgniter\Model;

class FeaturedSections_Model extends Model
{
    protected $table = 'tbl_featured_sections';
    protected $allowedFields = ['language_id', 'title', 'short_description', 'news_type', 'videos_type', 'filter_type', 'category_ids', 'subcategory_ids', 'news_ids', 'style_app','style_web', 'row_order', 'created_at', 'status', 'is_based_on_user_choice'];
}