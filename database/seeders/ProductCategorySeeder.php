<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Categorías globales básicas para el sistema.
     * Admin Kromerce las gestiona con traducciones oficiales.
     */
    public function run(): void
    {
        $now = now();

        $categories = [
            [
                'id' => Str::uuid(),
                'name' => 'General',
                'slug' => 'general',
                'description' => 'Productos generales y misceláneos',
                'level' => 0,
                'order' => 0,
                'status' => 'active',
                'translations' => json_encode([
                    'es' => ['name' => 'General', 'description' => 'Productos generales y misceláneos'],
                    'en' => ['name' => 'General', 'description' => 'General and miscellaneous products'],
                    'fr' => ['name' => 'Général', 'description' => 'Produits généraux et divers'],
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Electrónica',
                'slug' => 'electronica',
                'description' => 'Dispositivos electrónicos y accesorios',
                'level' => 0,
                'order' => 1,
                'status' => 'active',
                'translations' => json_encode([
                    'es' => ['name' => 'Electrónica', 'description' => 'Dispositivos electrónicos y accesorios'],
                    'en' => ['name' => 'Electronics', 'description' => 'Electronic devices and accessories'],
                    'fr' => ['name' => 'Électronique', 'description' => 'Appareils électroniques et accessoires'],
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Ropa y Accesorios',
                'slug' => 'ropa-accesorios',
                'description' => 'Vestimenta, calzado y complementos',
                'level' => 0,
                'order' => 2,
                'status' => 'active',
                'translations' => json_encode([
                    'es' => ['name' => 'Ropa y Accesorios', 'description' => 'Vestimenta, calzado y complementos'],
                    'en' => ['name' => 'Clothing & Accessories', 'description' => 'Clothing, footwear and accessories'],
                    'fr' => ['name' => 'Vêtements et Accessoires', 'description' => 'Vêtements, chaussures et accessoires'],
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Hogar y Jardín',
                'slug' => 'hogar-jardin',
                'description' => 'Artículos para el hogar, muebles y jardinería',
                'level' => 0,
                'order' => 3,
                'status' => 'active',
                'translations' => json_encode([
                    'es' => ['name' => 'Hogar y Jardín', 'description' => 'Artículos para el hogar, muebles y jardinería'],
                    'en' => ['name' => 'Home & Garden', 'description' => 'Home items, furniture and gardening'],
                    'fr' => ['name' => 'Maison et Jardin', 'description' => 'Articles pour la maison, meubles et jardinage'],
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Alimentos y Bebidas',
                'slug' => 'alimentos-bebidas',
                'description' => 'Productos comestibles y bebidas',
                'level' => 0,
                'order' => 4,
                'status' => 'active',
                'translations' => json_encode([
                    'es' => ['name' => 'Alimentos y Bebidas', 'description' => 'Productos comestibles y bebidas'],
                    'en' => ['name' => 'Food & Beverages', 'description' => 'Edible products and beverages'],
                    'fr' => ['name' => 'Aliments et Boissons', 'description' => 'Produits comestibles et boissons'],
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Salud y Belleza',
                'slug' => 'salud-belleza',
                'description' => 'Productos de cuidado personal y cosméticos',
                'level' => 0,
                'order' => 5,
                'status' => 'active',
                'translations' => json_encode([
                    'es' => ['name' => 'Salud y Belleza', 'description' => 'Productos de cuidado personal y cosméticos'],
                    'en' => ['name' => 'Health & Beauty', 'description' => 'Personal care products and cosmetics'],
                    'fr' => ['name' => 'Santé et Beauté', 'description' => 'Produits de soin personnel et cosmétiques'],
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Deportes y Ocio',
                'slug' => 'deportes-ocio',
                'description' => 'Equipamiento deportivo y artículos de recreación',
                'level' => 0,
                'order' => 6,
                'status' => 'active',
                'translations' => json_encode([
                    'es' => ['name' => 'Deportes y Ocio', 'description' => 'Equipamiento deportivo y artículos de recreación'],
                    'en' => ['name' => 'Sports & Leisure', 'description' => 'Sports equipment and recreational items'],
                    'fr' => ['name' => 'Sports et Loisirs', 'description' => 'Équipement sportif et articles de loisir'],
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Libros y Papelería',
                'slug' => 'libros-papeleria',
                'description' => 'Libros, revistas y material de oficina',
                'level' => 0,
                'order' => 7,
                'status' => 'active',
                'translations' => json_encode([
                    'es' => ['name' => 'Libros y Papelería', 'description' => 'Libros, revistas y material de oficina'],
                    'en' => ['name' => 'Books & Stationery', 'description' => 'Books, magazines and office supplies'],
                    'fr' => ['name' => 'Livres et Papeterie', 'description' => 'Livres, magazines et fournitures de bureau'],
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Juguetes y Niños',
                'slug' => 'juguetes-ninos',
                'description' => 'Juguetes, juegos y artículos para niños',
                'level' => 0,
                'order' => 8,
                'status' => 'active',
                'translations' => json_encode([
                    'es' => ['name' => 'Juguetes y Niños', 'description' => 'Juguetes, juegos y artículos para niños'],
                    'en' => ['name' => 'Toys & Kids', 'description' => 'Toys, games and items for children'],
                    'fr' => ['name' => 'Jouets et Enfants', 'description' => 'Jouets, jeux et articles pour enfants'],
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Automotriz',
                'slug' => 'automotriz',
                'description' => 'Accesorios y repuestos para vehículos',
                'level' => 0,
                'order' => 9,
                'status' => 'active',
                'translations' => json_encode([
                    'es' => ['name' => 'Automotriz', 'description' => 'Accesorios y repuestos para vehículos'],
                    'en' => ['name' => 'Automotive', 'description' => 'Vehicle accessories and spare parts'],
                    'fr' => ['name' => 'Automobile', 'description' => 'Accessoires et pièces de rechange pour véhicules'],
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Mascotas',
                'slug' => 'mascotas',
                'description' => 'Alimentos y accesorios para mascotas',
                'level' => 0,
                'order' => 10,
                'status' => 'active',
                'translations' => json_encode([
                    'es' => ['name' => 'Mascotas', 'description' => 'Alimentos y accesorios para mascotas'],
                    'en' => ['name' => 'Pets', 'description' => 'Pet food and accessories'],
                    'fr' => ['name' => 'Animaux', 'description' => 'Nourriture et accessoires pour animaux'],
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Arte y Manualidades',
                'slug' => 'arte-manualidades',
                'description' => 'Materiales de arte, manualidades y hobbies',
                'level' => 0,
                'order' => 11,
                'status' => 'active',
                'translations' => json_encode([
                    'es' => ['name' => 'Arte y Manualidades', 'description' => 'Materiales de arte, manualidades y hobbies'],
                    'en' => ['name' => 'Arts & Crafts', 'description' => 'Art materials, crafts and hobbies'],
                    'fr' => ['name' => 'Arts et Artisanat', 'description' => 'Matériaux d\'art, artisanat et loisirs'],
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Herramientas y Construcción',
                'slug' => 'herramientas-construccion',
                'description' => 'Herramientas, materiales de construcción y ferretería',
                'level' => 0,
                'order' => 12,
                'status' => 'active',
                'translations' => json_encode([
                    'es' => ['name' => 'Herramientas y Construcción', 'description' => 'Herramientas, materiales de construcción y ferretería'],
                    'en' => ['name' => 'Tools & Construction', 'description' => 'Tools, construction materials and hardware'],
                    'fr' => ['name' => 'Outils et Construction', 'description' => 'Outils, matériaux de construction et quincaillerie'],
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Software y Servicios Digitales',
                'slug' => 'software-servicios-digitales',
                'description' => 'Licencias de software, cursos y servicios digitales',
                'level' => 0,
                'order' => 13,
                'status' => 'active',
                'translations' => json_encode([
                    'es' => ['name' => 'Software y Servicios Digitales', 'description' => 'Licencias de software, cursos y servicios digitales'],
                    'en' => ['name' => 'Software & Digital Services', 'description' => 'Software licenses, courses and digital services'],
                    'fr' => ['name' => 'Logiciels et Services Numériques', 'description' => 'Licences de logiciels, cours et services numériques'],
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Ofertas y Promociones',
                'slug' => 'ofertas-promociones',
                'description' => 'Productos en descuento y promociones especiales',
                'level' => 0,
                'order' => 99,
                'status' => 'active',
                'is_featured' => true,
                'translations' => json_encode([
                    'es' => ['name' => 'Ofertas y Promociones', 'description' => 'Productos en descuento y promociones especiales'],
                    'en' => ['name' => 'Deals & Promotions', 'description' => 'Discounted products and special promotions'],
                    'fr' => ['name' => 'Offres et Promotions', 'description' => 'Produits en promotion et offres spéciales'],
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Crear categorías usando firstOrNew para manejar correctamente el UUID
        foreach ($categories as $categoryData) {
            $slug = $categoryData['slug'];

            // Buscar o crear nueva instancia
            $category = ProductCategory::firstOrNew(['slug' => $slug]);

            // Si es nuevo, asignar el UUID del seeder
            if (!$category->exists) {
                $category->id = $categoryData['id'];
            }

            // Quitar id y slug de los datos a asignar (slug ya está en la búsqueda)
            $data = array_diff_key($categoryData, ['id' => true, 'slug' => true]);
            $category->fill($data);
            $category->save();
        }

        $this->command->info('Creadas ' . count($categories) . ' categorías globales con traducciones.');
        $this->command->info('Las tiendas pueden usar estas categorías o solicitar nuevas al admin de Kromerce.');
    }

    /**
     * Obtener las categorías sugeridas para mostrar en el panel de admin.
     *
     * @return array
     */
    public static function getSuggestedCategories(): array
    {
        return [
            ['name' => 'General', 'slug' => 'general', 'description' => 'Productos generales y misceláneos'],
            ['name' => 'Electrónica', 'slug' => 'electronica', 'description' => 'Dispositivos electrónicos y accesorios'],
            ['name' => 'Ropa y Accesorios', 'slug' => 'ropa-accesorios', 'description' => 'Vestimenta, calzado y complementos'],
            ['name' => 'Hogar y Jardín', 'slug' => 'hogar-jardin', 'description' => 'Artículos para el hogar, muebles y jardinería'],
            ['name' => 'Alimentos y Bebidas', 'slug' => 'alimentos-bebidas', 'description' => 'Productos comestibles y bebidas'],
            ['name' => 'Salud y Belleza', 'slug' => 'salud-belleza', 'description' => 'Productos de cuidado personal y cosméticos'],
            ['name' => 'Deportes y Ocio', 'slug' => 'deportes-ocio', 'description' => 'Equipamiento deportivo y artículos de recreación'],
            ['name' => 'Ofertas', 'slug' => 'ofertas', 'description' => 'Productos en descuento'],
        ];
    }
}
