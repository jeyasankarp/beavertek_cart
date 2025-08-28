<?php
namespace Opencart\Admin\Model\Tool;

class ProductImport extends \Opencart\System\Engine\Model {
    public function importCSV(string $file): string {
        $this->load->model('catalog/product');
        $this->load->model('catalog/category');
        $this->load->model('design/seo_url');
        $this->load->model('catalog/manufacturer');

        $handle = fopen($file, 'r');
        $header = array_map('trim', fgetcsv($handle, 1000, ','));
        $header = array_map(function($h) {
            return ucfirst(strtolower($h)); // "name" → "Name", "MODEL" → "Model"
        }, $header);
        $added = 0;
        $updated = 0;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            // Convert every value in the row to UTF-8 safely
         $row = array_map(function($value) {
            if ($value === null) return '';
            $enc = mb_detect_encoding($value, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
            if ($enc) {
                return mb_convert_encoding($value, 'UTF-8', $enc);
            }
            return $value;
        }, $row);
                
            $product_data = array_combine($header, $row);

            $model = $product_data['Model'] ?? '';
            $master_id = $product_data['master_id'] ?? '';
            $product_id = 0;

            if (!empty($master_id)) {
            $query = $this->db->query("SELECT product_id FROM `" . DB_PREFIX . "product` WHERE master_id = '" . (int)$master_id . "' LIMIT 1");
            if ($query->num_rows) {
                $product_id = (int)$query->row['product_id'];
            }
            }
           
            // If no product found and model is given, try by model
            if (!$product_id && !empty($model)) {
                $query = $this->db->query("SELECT product_id FROM `" . DB_PREFIX . "product` WHERE model = '" . $this->db->escape($model) . "' LIMIT 1");
                if ($query->num_rows) {
                    $product_id = (int)$query->row['product_id'];
                }
            }

            $category_ids = [];
            if (!empty($product_data['Category'])) {
                foreach (explode('|', $product_data['Category']) as $cat_path) {
                    $category_ids[] = $this->createCategoryPath($cat_path);
                }
            }

            $manufacturer_id = 0;
            if (!empty($product_data['Manufacturer'])) {
                $m_query = $this->db->query("SELECT manufacturer_id FROM `" . DB_PREFIX . "manufacturer` WHERE name = '" . $this->db->escape($product_data['Manufacturer']) . "' LIMIT 1");
                if ($m_query->num_rows) {
                    $manufacturer_id = (int)$m_query->row['manufacturer_id'];
                } else {
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "manufacturer` SET name = '" . $this->db->escape($product_data['Manufacturer']) . "', sort_order = 0");
                    $manufacturer_id = $this->db->getLastId();
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "manufacturer_to_store` SET manufacturer_id = '" . (int)$manufacturer_id . "', store_id = 0");
                }
            }

            $image = $product_data['Image'] ?? '';
            if ($image && str_starts_with($image, 'http')) {
                $image = $this->downloadImage($image);
            }

        $images = [];
        if (!empty($product_data['Image'])) {
            foreach (explode('|', $product_data['Image']) as $img) {
                $img = trim($img);
                if ($img) {
                    if (str_starts_with($img, 'http')) {
                        $img = $this->downloadImage($img); // existing function
                    }
                    $images[] = ['image' => $img, 'sort_order' => 0];
                }
            }
        }

            $data = [
                'model'              => $model,
                'sku'                => $product_data['SKU'] ?? '',
                'price'              => (float)($product_data['Price'] ?? 0),
                'quantity'           => (int)($product_data['Quantity'] ?? 1),
                'status'             => 1,
                'stock_status_id'    => 6,
                'date_available'     => date('Y-m-d'),
                'manufacturer_id'    => $manufacturer_id,
                'minimum'            => 1,
                'subtract'           => 1,
                'shipping'           => 1,
                'points'             => 0,
                'weight'             => 0,
                'weight_class_id'    => 1,
                'length'             => 0,
                'width'              => 0,
                'height'             => 0,
                'length_class_id'    => 1,
                'tax_class_id'       => 0,
                'sort_order'         => 0,
                'image'              => $image,
                'product_store'      => [0],
                'product_category'   => $category_ids,
                'location'         => $product_data['Location'] ?? '',
                 'master_id'          => $product_data['master_id'] ?? 0,   // or null
              
                'product_description' => [
                    (int)$this->config->get('config_language_id') => [
                        'name'             => $product_data['Name'] ?? '',
                        'description'      => '',
                        'meta_title'       => $product_data['Name'] ?? '',
                        'meta_description' => '',
                        'meta_keyword'     => '',
                        'tag'              => ''
                    ]
                ],
                'keyword'            => $this->generateSeoKeyword($product_data['Name'] ?? ''),
            ];


            $data['product_attribute'] = [];
            $data['product_image'] = $images; // gallery images
            foreach ($product_data as $key => $value) {
                if (stripos($key, 'Attribute:') === 0 && $value !== '') {
                    $name = trim(substr($key, 9));
                    $attr_query = $this->db->query("SELECT attribute_id FROM `" . DB_PREFIX . "attribute_description` WHERE name = '" . $this->db->escape($name) . "' LIMIT 1");
                    if ($attr_query->num_rows) {
                        $attribute_id = $attr_query->row['attribute_id'];
                    } else {
                        $this->db->query("INSERT INTO `" . DB_PREFIX . "attribute_group` SET sort_order = 1");
                        $group_id = $this->db->getLastId();
                        $this->db->query("INSERT INTO `" . DB_PREFIX . "attribute_group_description` SET attribute_group_id = '" . $group_id . "', language_id = '" . (int)$this->config->get('config_language_id') . "', name = 'Imported'");
                        $this->db->query("INSERT INTO `" . DB_PREFIX . "attribute` SET attribute_group_id = '" . $group_id . "', sort_order = 0");
                        $attribute_id = $this->db->getLastId();
                        $this->db->query("INSERT INTO `" . DB_PREFIX . "attribute_description` SET attribute_id = '" . $attribute_id . "', language_id = '" . (int)$this->config->get('config_language_id') . "', name = '" . $this->db->escape($name) . "'");
                    }

                    $data['product_attribute'][] = [
                        'attribute_id' => $attribute_id,
                        'product_attribute_description' => [
                            (int)$this->config->get('config_language_id') => ['text' => $value]
                        ]
                    ];
                }
            }

            $data['product_option'] = [];
            
            foreach ($product_data as $key => $value) {
                if (stripos($key, 'Option:') === 0 && $value !== '') {
                    $option_name = trim(substr($key, 7));
                    $values = array_map('trim', explode('|', $value));
                    $opt_query = $this->db->query("SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name = '" . $this->db->escape($option_name) . "' LIMIT 1");
                    if ($opt_query->num_rows) {
                        $option_id = $opt_query->row['option_id'];
                    } else {
                        $this->db->query("INSERT INTO `" . DB_PREFIX . "option` SET type = 'select', sort_order = 0");
                        $option_id = $this->db->getLastId();
                        $this->db->query("INSERT INTO `" . DB_PREFIX . "option_description` SET option_id = '" . $option_id . "', language_id = '" . (int)$this->config->get('config_language_id') . "', name = '" . $this->db->escape($option_name) . "'");
                    }

                    $option_values = [];
                    foreach ($values as $val) {
                        $val_query = $this->db->query("SELECT option_value_id FROM `" . DB_PREFIX . "option_value_description` WHERE name = '" . $this->db->escape($val) . "' AND option_id = '" . $option_id . "'");
                        if ($val_query->num_rows) {
                            $value_id = $val_query->row['option_value_id'];
                        } else {
                            $this->db->query("INSERT INTO `" . DB_PREFIX . "option_value` SET option_id = '" . $option_id . "', image = '', sort_order = 0");
                            $value_id = $this->db->getLastId();
                            $this->db->query("INSERT INTO `" . DB_PREFIX . "option_value_description` SET option_value_id = '" . $value_id . "', language_id = '" . (int)$this->config->get('config_language_id') . "', name = '" . $this->db->escape($val) . "', option_id = '" . $option_id . "'");
                        }

                        $option_values[] = [
                            'option_value_id' => $value_id,
                            'quantity' => 999,
                            'subtract' => 1,
                            'price' => '',
                            'price_prefix' => '+',
                            'points' => '',
                            'points_prefix' => '+',
                            'weight' => '',
                            'weight_prefix' => '+'
                        ];
                    }

                    $data['product_option'][] = [
                        'option_id' => $option_id,
                        'type' => 'select',
                        'required' => 0,
                        'product_option_value' => $option_values
                    ];
                }
            }

            

            if ($product_id) {
                $this->model_catalog_product->editProduct($product_id, $data);
                $updated++;
            } else {
             
                if (!empty($data['product_description'][(int)$this->config->get('config_language_id')]['name']) 
                    && !empty($model)) {
                            $this->model_catalog_product->addProduct($data);
                            $added++;
                    }
                     else {
                    // skip invalid/empty row
                    continue;
                }
            }
        }

        fclose($handle);
        return "Import completed. Added: $added, Updated: $updated";
    }

    private function createCategoryPath(string $path): int {
        $categories = array_map('trim', explode('#', $path));
        $parent_id = 0;
        $last_id = 0;

        foreach ($categories as $name) {
           $name = mb_convert_encoding($name, 'UTF-8', 'ISO-8859-1');
            $query = $this->db->query("SELECT c.category_id FROM `" . DB_PREFIX . "category_description` cd JOIN `" . DB_PREFIX . "category` c ON cd.category_id = c.category_id WHERE cd.name = '" . $this->db->escape($name) . "' AND c.parent_id = '" . (int)$parent_id . "' LIMIT 1");
            if ($query->num_rows) {
                $last_id = $query->row['category_id'];
            } else {
                
                $this->db->query("INSERT INTO `" . DB_PREFIX . "category` SET parent_id = '" . $parent_id . "', sort_order = 0, status = 1");
                $category_id = $this->db->getLastId();
                $this->db->query("INSERT INTO `" . DB_PREFIX . "category_description` SET category_id = '" . $category_id . "', language_id = '" . (int)$this->config->get('config_language_id') . "', name = '" . $this->db->escape($name) . "', meta_title = '" . $this->db->escape($name) . "'");
                $this->db->query("INSERT INTO `" . DB_PREFIX . "category_to_store` SET category_id = '" . $category_id . "', store_id = 0");
             $key      = 'category_id';
                $value    = (string)$category_id;
                $store_id = 0;
                $lang_id  = (int)$this->config->get('config_language_id');

                $this->model_design_seo_url->addSeoUrl(
                    $key,
                    $value,
                    $this->generateSeoKeyword($name, $store_id, $lang_id),
                    $store_id,
                    $lang_id
                );
                            $level = 0;
                $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . $parent_id . "' ORDER BY level ASC");
                foreach ($query->rows as $result) {
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET category_id = '" . $category_id . "', path_id = '" . $result['path_id'] . "', level = '" . $level++ . "'");
                }
                $this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET category_id = '" . $category_id . "', path_id = '" . $category_id . "', level = '" . $level . "'");

                $last_id = $category_id;
            }
            $parent_id = $last_id;
        }

        return $last_id;
    }
    private function generateSeoKeyword($string, $store_id = 0, $language_id = 1) {
        // Base slug
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', html_entity_decode((string)$string, ENT_QUOTES, 'UTF-8')), '-'));
        if ($slug === '') $slug = 'item';

        $base = $slug;
        $i = 2; // next suffix
        while (true) {
            $q = $this->db->query("SELECT seo_url_id FROM `" . DB_PREFIX . "seo_url`
                WHERE keyword = '" . $this->db->escape($slug) . "'
                AND store_id = '" . (int)$store_id . "'
                AND language_id = '" . (int)$language_id . "'
                LIMIT 1");
            if (!$q->num_rows) break;
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }



   private function downloadImage(string $url): string {
    $path = 'catalog/imports/';
    $filename = basename(parse_url($url, PHP_URL_PATH));
    $imagePath = DIR_IMAGE . $path . $filename;

    if (!is_dir(DIR_IMAGE . $path)) {
        mkdir(DIR_IMAGE . $path, 0755, true);
    }

    // Use cURL instead of file_get_contents
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // in case of SSL issues
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200 && $data !== false) {
        file_put_contents($imagePath, $data);
        return $path . $filename;
    } else {
        // fallback image or log error
        return 'catalog/no_image.png';
    }
}

}
