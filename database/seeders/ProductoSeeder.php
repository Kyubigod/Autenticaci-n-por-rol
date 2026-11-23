<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $categoriaIds = Categoria::pluck('id')->toArray();

        if (empty($categoriaIds)) {
            $this->command->warn('No hay categorías registradas. Ejecuta primero CategoriaSeeder.');
            return;
        }

        $productos = [
            ['nombre' => 'Laptop Gamer Machenike L15', 'descripcion' => 'Laptop con RTX 5060 y memoria DDR5', 'precio' => 24500.00, 'existencias' => 5],
            ['nombre' => 'Teclado Mecánico Attack Shark X68 HE', 'descripcion' => 'Teclado magnético 65% de respuesta rápida', 'precio' => 1450.00, 'existencias' => 12],
            ['nombre' => 'Mouse Ultraligero Attack Shark X3', 'descripcion' => 'Mouse inalámbrico sensor PAW3395', 'precio' => 890.00, 'existencias' => 20],
            ['nombre' => 'Audífonos Razer BlackShark V3 X', 'descripcion' => 'Headset inalámbrico HyperSpeed', 'precio' => 1850.00, 'existencias' => 8],
            ['nombre' => 'Micrófono Dinámico Maono PD200X', 'descripcion' => 'Micrófono USB/XLR para streaming y podcast', 'precio' => 1620.00, 'existencias' => 0],
            ['nombre' => 'Monitor Curvo 27 Pulgadas 165Hz', 'descripcion' => 'Panel VA Full HD 1ms tiempo de respuesta', 'precio' => 4200.00, 'existencias' => 6],
            ['nombre' => 'Tablet Lenovo Legion Y700', 'descripcion' => 'Tablet gaming 8.8 pulgadas 165Hz', 'precio' => 8900.00, 'existencias' => 4],
            ['nombre' => 'Stylus Lenovo Tab Pen Plus', 'descripcion' => 'Lápiz óptico recargable de baja latencia', 'precio' => 950.00, 'existencias' => 15],
            ['nombre' => 'Memoria RAM 16GB DDR5 5600MHz', 'descripcion' => 'Módulo SODIMM para laptop de alto rendimiento', 'precio' => 1150.00, 'existencias' => 9],
            ['nombre' => 'SSD NVMe M.2 1TB Gen4', 'descripcion' => 'Unidad de estado sólido lectura 7000MB/s', 'precio' => 1490.00, 'existencias' => 18],
            ['nombre' => 'Silla Ergonómica de Oficina Pro', 'descripcion' => 'Soporte lumbar ajustable y malla transpirable', 'precio' => 3600.00, 'existencias' => 3],
            ['nombre' => 'Base Enfriadora para Laptop RGB', 'descripcion' => '6 ventiladores silenciosos y altura regulable', 'precio' => 580.00, 'existencias' => 25],
            ['nombre' => 'Hub USB-C 7 en 1 HDMI 4K', 'descripcion' => 'Adaptador multipuerto con carga rápida PD', 'precio' => 640.00, 'existencias' => 0],
            ['nombre' => 'Webcam Full HD 1080p con Micrófono', 'descripcion' => 'Cámara web con tapa de privacidad', 'precio' => 720.00, 'existencias' => 14],
            ['nombre' => 'Mousepad Extendido XXL Control', 'descripcion' => 'Tapete 90x40cm bordes cosidos antideslizante', 'precio' => 320.00, 'existencias' => 30],
            ['nombre' => 'Cargador Rápido GaN 100W Dual', 'descripcion' => 'Cubo cargador compacto USB-C doble', 'precio' => 790.00, 'existencias' => 11],
            ['nombre' => 'Cable Trenzado USB-C a USB-C 2m', 'descripcion' => 'Soporta transferencia de datos y 100W', 'precio' => 190.00, 'existencias' => 40],
            ['nombre' => 'Teclado Inalámbrico Compacto Oficina', 'descripcion' => 'Teclado silencioso conexión Bluetooth y 2.4G', 'precio' => 540.00, 'existencias' => 0],
            ['nombre' => 'Soporte Articulado para Monitor', 'descripcion' => 'Brazo hidráulico VESA 75/100 hasta 32 pulgadas', 'precio' => 850.00, 'existencias' => 7],
            ['nombre' => 'Bocinas Estéreo Escritorio 2.0', 'descripcion' => 'Altavoces compactos alimentación USB', 'precio' => 460.00, 'existencias' => 16],
            ['nombre' => 'Kit de Desarmadores de Precisión', 'descripcion' => 'Estuche magnético 64 puntas para electrónica', 'precio' => 390.00, 'existencias' => 22],
            ['nombre' => 'Pasta Térmica Alto Rendimiento 4g', 'descripcion' => 'Conductividad térmica superior para CPU/GPU', 'precio' => 240.00, 'existencias' => 35],
            ['nombre' => 'Regulador de Voltaje 8 Contactos', 'descripcion' => 'Protección contra picos de corriente', 'precio' => 680.00, 'existencias' => 10],
            ['nombre' => 'Router Wi-Fi 6 Doble Banda AX3000', 'descripcion' => '4 antenas externas de alta ganancia', 'precio' => 1590.00, 'existencias' => 0],
            ['nombre' => 'Funda Acolchada para Laptop 15.6', 'descripcion' => 'Estuche resistente a salpicaduras con bolsillo', 'precio' => 350.00, 'existencias' => 19],
        ];

        foreach ($productos as $index => $item) {
            Producto::firstOrCreate(
                ['nombre' => $item['nombre']],
                [
                    'descripcion'  => $item['descripcion'],
                    'categoria_id' => $categoriaIds[$index % count($categoriaIds)],
                    'precio'       => $item['precio'],
                    'existencias'  => $item['existencias'],
                    'estado'       => $item['existencias'] > 0 ? 'Disponible' : 'Agotado',
                ]
            );
        }
    }
}