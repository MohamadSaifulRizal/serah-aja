<?php

app('router')->setCompiledRoutes(
    array (
  'compiled' => 
  array (
    0 => false,
    1 => 
    array (
      '/up' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::tvfvd42trxxGTi0i',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'login',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'login.proses',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/logout' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'logout',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/supplier/simpan' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'supplier.simpan',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/master-barang/simpan' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'masterBarang.simpan',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/manajemen-akun' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'akun.indeks',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'akun.simpan',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/kategori-bahan-pokok' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'kategoriBahanPokok.indeks',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/kategori-bahan-pokok/simpan' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'kategoriBahanPokok.simpan',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/kategori-barang-inventaris' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'kategoriInventaris.indeks',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/kategori-barang-inventaris/simpan' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'kategoriInventaris.simpan',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/logistik/bahan-pokok' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'bahanPokok.indeks',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/logistik/bahan-pokok/detail' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'bahanPokok.detailKelompok',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/logistik/bahan-pokok/detail/unduh' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'bahanPokok.exportKelompok',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/logistik/bahan-pokok/unduh-semua' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'bahanPokok.exportSemua',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/logistik/barang-inventaris' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'inventaris.indeks',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/logistik/barang-inventaris/tambah' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'inventaris.formTambah',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/logistik/barang-inventaris/unduh' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'inventaris.exportExcel',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/logistik/barang-inventaris/simpan-batch' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'inventaris.simpanBatch',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/supplier' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'supplier.indeks',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/master-barang' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'masterBarang.indeks',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/dapur' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dapur.indeks',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'dapur.simpan',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/dapur-saya' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dapur.beranda',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/dapur-saya/laporan' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dapur.simpanLaporan',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/dapur-saya/beli-mandiri' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dapur.formBeliMandiri',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'dapur.simpanBeliMandiri',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
    ),
    2 => 
    array (
      0 => '{^(?|/ma(?|najemen\\-akun/([^/]++)(?|(*:38))|ster\\-barang/([^/]++)(?|(*:70)))|/kategori\\-ba(?|han\\-pokok/([^/]++)(?|(*:117))|rang\\-inventaris/([^/]++)(?|(*:154)))|/logistik/ba(?|han\\-pokok/(?|([^/]++)(?|(*:204))|tambah(*:219)|simpan\\-batch(*:240))|rang\\-inventaris/(?|([^/]++)(?|/kondisi(*:288)|(*:296))|riwayat(*:312)))|/s(?|upplier/([^/]++)(?|(*:346))|torage/(.*)(?|(*:369)))|/dapur/([^/]++)(?|(*:397)))/?$}sDu',
    ),
    3 => 
    array (
      38 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'akun.update',
          ),
          1 => 
          array (
            0 => 'akun',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'akun.hapus',
          ),
          1 => 
          array (
            0 => 'akun',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      70 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'masterBarang.update',
          ),
          1 => 
          array (
            0 => 'masterBarang',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'masterBarang.hapus',
          ),
          1 => 
          array (
            0 => 'masterBarang',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      117 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'kategoriBahanPokok.update',
          ),
          1 => 
          array (
            0 => 'kategori',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'kategoriBahanPokok.hapus',
          ),
          1 => 
          array (
            0 => 'kategori',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      154 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'kategoriInventaris.update',
          ),
          1 => 
          array (
            0 => 'kategori',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'kategoriInventaris.hapus',
          ),
          1 => 
          array (
            0 => 'kategori',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      204 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'bahanPokok.update',
          ),
          1 => 
          array (
            0 => 'bahanPokok',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'bahanPokok.hapus',
          ),
          1 => 
          array (
            0 => 'bahanPokok',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      219 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'bahanPokok.formTambah',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      240 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'bahanPokok.simpanBatch',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      288 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'inventaris.updateKondisi',
          ),
          1 => 
          array (
            0 => 'barangInventaris',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      296 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'inventaris.update',
          ),
          1 => 
          array (
            0 => 'barangInventaris',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'inventaris.hapus',
          ),
          1 => 
          array (
            0 => 'barangInventaris',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      312 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'inventaris.riwayat',
          ),
          1 => 
          array (
          ),
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      346 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'supplier.update',
          ),
          1 => 
          array (
            0 => 'supplier',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'supplier.hapus',
          ),
          1 => 
          array (
            0 => 'supplier',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      369 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'storage.local',
          ),
          1 => 
          array (
            0 => 'path',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'storage.local.upload',
          ),
          1 => 
          array (
            0 => 'path',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      397 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dapur.update',
          ),
          1 => 
          array (
            0 => 'dapur',
          ),
          2 => 
          array (
            'PUT' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'dapur.hapus',
          ),
          1 => 
          array (
            0 => 'dapur',
          ),
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        2 => 
        array (
          0 => 
          array (
            '_route' => 'dapur.detail',
          ),
          1 => 
          array (
            0 => 'dapur',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        3 => 
        array (
          0 => NULL,
          1 => NULL,
          2 => NULL,
          3 => NULL,
          4 => false,
          5 => false,
          6 => 0,
        ),
      ),
    ),
    4 => NULL,
  ),
  'attributes' => 
  array (
    'generated::tvfvd42trxxGTi0i' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'up',
      'action' => 
      array (
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:857:"function () {
                    $exception = null;

                    try {
                        \\Illuminate\\Support\\Facades\\Event::dispatch(new \\Illuminate\\Foundation\\Events\\DiagnosingHealth);
                    } catch (\\Throwable $e) {
                        if (app()->hasDebugModeEnabled()) {
                            throw $e;
                        }

                        report($e);

                        $exception = $e->getMessage();
                    }

                    return response(\\Illuminate\\Support\\Facades\\View::file(\'D:\\\\kuliah\\\\MKI\\\\Projek\\\\mbg2-main-revisi (7)\\\\mbg2-main\\\\vendor\\\\laravel\\\\framework\\\\src\\\\Illuminate\\\\Foundation\\\\Configuration\'.\'/../resources/health-up.blade.php\', [
                        \'exception\' => $exception,
                    ]), status: $exception ? 500 : 200);
                }";s:5:"scope";s:54:"Illuminate\\Foundation\\Configuration\\ApplicationBuilder";s:4:"this";N;s:4:"self";s:32:"00000000000005510000000000000000";}}',
        'as' => 'generated::tvfvd42trxxGTi0i',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'login' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'guest',
        ),
        'uses' => 'App\\Http\\Controllers\\AuthController@index',
        'controller' => 'App\\Http\\Controllers\\AuthController@index',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'login',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'login.proses' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'guest',
        ),
        'uses' => 'App\\Http\\Controllers\\AuthController@login',
        'controller' => 'App\\Http\\Controllers\\AuthController@login',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'login.proses',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'logout' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'logout',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'App\\Http\\Controllers\\AuthController@logout',
        'controller' => 'App\\Http\\Controllers\\AuthController@logout',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'logout',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'supplier.simpan' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'supplier/simpan',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'App\\Http\\Controllers\\SupplierController@simpan',
        'controller' => 'App\\Http\\Controllers\\SupplierController@simpan',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'supplier.simpan',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'masterBarang.simpan' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'master-barang/simpan',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'App\\Http\\Controllers\\MasterBarangController@simpan',
        'controller' => 'App\\Http\\Controllers\\MasterBarangController@simpan',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'masterBarang.simpan',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'akun.indeks' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'manajemen-akun',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:super_admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AkunController@indeks',
        'controller' => 'App\\Http\\Controllers\\AkunController@indeks',
        'as' => 'akun.indeks',
        'namespace' => NULL,
        'prefix' => '/manajemen-akun',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'akun.simpan' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'manajemen-akun',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:super_admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AkunController@simpan',
        'controller' => 'App\\Http\\Controllers\\AkunController@simpan',
        'as' => 'akun.simpan',
        'namespace' => NULL,
        'prefix' => '/manajemen-akun',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'akun.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'manajemen-akun/{akun}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:super_admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AkunController@update',
        'controller' => 'App\\Http\\Controllers\\AkunController@update',
        'as' => 'akun.update',
        'namespace' => NULL,
        'prefix' => '/manajemen-akun',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'akun.hapus' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'manajemen-akun/{akun}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:super_admin',
        ),
        'uses' => 'App\\Http\\Controllers\\AkunController@hapus',
        'controller' => 'App\\Http\\Controllers\\AkunController@hapus',
        'as' => 'akun.hapus',
        'namespace' => NULL,
        'prefix' => '/manajemen-akun',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'kategoriBahanPokok.indeks' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'kategori-bahan-pokok',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin,admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\KategoriController@indeks',
        'controller' => 'App\\Http\\Controllers\\KategoriController@indeks',
        'as' => 'kategoriBahanPokok.indeks',
        'namespace' => NULL,
        'prefix' => '/kategori-bahan-pokok',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'jenis' => 'bahan_pokok',
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'kategoriBahanPokok.simpan' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'kategori-bahan-pokok/simpan',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin,admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\KategoriController@simpan',
        'controller' => 'App\\Http\\Controllers\\KategoriController@simpan',
        'as' => 'kategoriBahanPokok.simpan',
        'namespace' => NULL,
        'prefix' => '/kategori-bahan-pokok',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'jenis' => 'bahan_pokok',
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'kategoriBahanPokok.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'kategori-bahan-pokok/{kategori}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin,admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\KategoriController@update',
        'controller' => 'App\\Http\\Controllers\\KategoriController@update',
        'as' => 'kategoriBahanPokok.update',
        'namespace' => NULL,
        'prefix' => '/kategori-bahan-pokok',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'jenis' => 'bahan_pokok',
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'kategoriBahanPokok.hapus' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'kategori-bahan-pokok/{kategori}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin,admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\KategoriController@hapus',
        'controller' => 'App\\Http\\Controllers\\KategoriController@hapus',
        'as' => 'kategoriBahanPokok.hapus',
        'namespace' => NULL,
        'prefix' => '/kategori-bahan-pokok',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'jenis' => 'bahan_pokok',
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'kategoriInventaris.indeks' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'kategori-barang-inventaris',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin,admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\KategoriController@indeks',
        'controller' => 'App\\Http\\Controllers\\KategoriController@indeks',
        'as' => 'kategoriInventaris.indeks',
        'namespace' => NULL,
        'prefix' => '/kategori-barang-inventaris',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'jenis' => 'inventaris',
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'kategoriInventaris.simpan' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'kategori-barang-inventaris/simpan',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin,admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\KategoriController@simpan',
        'controller' => 'App\\Http\\Controllers\\KategoriController@simpan',
        'as' => 'kategoriInventaris.simpan',
        'namespace' => NULL,
        'prefix' => '/kategori-barang-inventaris',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'jenis' => 'inventaris',
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'kategoriInventaris.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'kategori-barang-inventaris/{kategori}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin,admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\KategoriController@update',
        'controller' => 'App\\Http\\Controllers\\KategoriController@update',
        'as' => 'kategoriInventaris.update',
        'namespace' => NULL,
        'prefix' => '/kategori-barang-inventaris',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'jenis' => 'inventaris',
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'kategoriInventaris.hapus' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'kategori-barang-inventaris/{kategori}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin,admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\KategoriController@hapus',
        'controller' => 'App\\Http\\Controllers\\KategoriController@hapus',
        'as' => 'kategoriInventaris.hapus',
        'namespace' => NULL,
        'prefix' => '/kategori-barang-inventaris',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'jenis' => 'inventaris',
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'bahanPokok.indeks' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'logistik/bahan-pokok',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin,admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\BahanPokokController@indeks',
        'controller' => 'App\\Http\\Controllers\\BahanPokokController@indeks',
        'as' => 'bahanPokok.indeks',
        'namespace' => NULL,
        'prefix' => '/logistik/bahan-pokok',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'bahanPokok.detailKelompok' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'logistik/bahan-pokok/detail',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin,admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\BahanPokokController@detailKelompok',
        'controller' => 'App\\Http\\Controllers\\BahanPokokController@detailKelompok',
        'as' => 'bahanPokok.detailKelompok',
        'namespace' => NULL,
        'prefix' => '/logistik/bahan-pokok',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'bahanPokok.exportKelompok' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'logistik/bahan-pokok/detail/unduh',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin,admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\BahanPokokController@exportKelompok',
        'controller' => 'App\\Http\\Controllers\\BahanPokokController@exportKelompok',
        'as' => 'bahanPokok.exportKelompok',
        'namespace' => NULL,
        'prefix' => '/logistik/bahan-pokok',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'bahanPokok.exportSemua' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'logistik/bahan-pokok/unduh-semua',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin,admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\BahanPokokController@exportSemua',
        'controller' => 'App\\Http\\Controllers\\BahanPokokController@exportSemua',
        'as' => 'bahanPokok.exportSemua',
        'namespace' => NULL,
        'prefix' => '/logistik/bahan-pokok',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'bahanPokok.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'logistik/bahan-pokok/{bahanPokok}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin,admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\BahanPokokController@update',
        'controller' => 'App\\Http\\Controllers\\BahanPokokController@update',
        'as' => 'bahanPokok.update',
        'namespace' => NULL,
        'prefix' => '/logistik/bahan-pokok',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'bahanPokok.hapus' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'logistik/bahan-pokok/{bahanPokok}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin,admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\BahanPokokController@hapus',
        'controller' => 'App\\Http\\Controllers\\BahanPokokController@hapus',
        'as' => 'bahanPokok.hapus',
        'namespace' => NULL,
        'prefix' => '/logistik/bahan-pokok',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'inventaris.indeks' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'logistik/barang-inventaris',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin,admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\BarangInventarisController@indeks',
        'controller' => 'App\\Http\\Controllers\\BarangInventarisController@indeks',
        'as' => 'inventaris.indeks',
        'namespace' => NULL,
        'prefix' => '/logistik/barang-inventaris',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'inventaris.formTambah' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'logistik/barang-inventaris/tambah',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin,admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\BarangInventarisController@formTambah',
        'controller' => 'App\\Http\\Controllers\\BarangInventarisController@formTambah',
        'as' => 'inventaris.formTambah',
        'namespace' => NULL,
        'prefix' => '/logistik/barang-inventaris',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'inventaris.exportExcel' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'logistik/barang-inventaris/unduh',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin,admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\BarangInventarisController@exportExcel',
        'controller' => 'App\\Http\\Controllers\\BarangInventarisController@exportExcel',
        'as' => 'inventaris.exportExcel',
        'namespace' => NULL,
        'prefix' => '/logistik/barang-inventaris',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'inventaris.simpanBatch' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'logistik/barang-inventaris/simpan-batch',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin,admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\BarangInventarisController@simpanBatch',
        'controller' => 'App\\Http\\Controllers\\BarangInventarisController@simpanBatch',
        'as' => 'inventaris.simpanBatch',
        'namespace' => NULL,
        'prefix' => '/logistik/barang-inventaris',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'inventaris.updateKondisi' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'logistik/barang-inventaris/{barangInventaris}/kondisi',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin,admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\BarangInventarisController@updateKondisi',
        'controller' => 'App\\Http\\Controllers\\BarangInventarisController@updateKondisi',
        'as' => 'inventaris.updateKondisi',
        'namespace' => NULL,
        'prefix' => '/logistik/barang-inventaris',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'inventaris.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'logistik/barang-inventaris/{barangInventaris}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin,admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\BarangInventarisController@update',
        'controller' => 'App\\Http\\Controllers\\BarangInventarisController@update',
        'as' => 'inventaris.update',
        'namespace' => NULL,
        'prefix' => '/logistik/barang-inventaris',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'inventaris.hapus' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'logistik/barang-inventaris/{barangInventaris}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin,admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\BarangInventarisController@hapus',
        'controller' => 'App\\Http\\Controllers\\BarangInventarisController@hapus',
        'as' => 'inventaris.hapus',
        'namespace' => NULL,
        'prefix' => '/logistik/barang-inventaris',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'inventaris.riwayat' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'logistik/barang-inventaris/riwayat',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin,admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\BarangInventarisController@simpanRiwayat',
        'controller' => 'App\\Http\\Controllers\\BarangInventarisController@simpanRiwayat',
        'as' => 'inventaris.riwayat',
        'namespace' => NULL,
        'prefix' => '/logistik/barang-inventaris',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => '/',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin',
        ),
        'uses' => 'App\\Http\\Controllers\\DashboardController@indeks',
        'controller' => 'App\\Http\\Controllers\\DashboardController@indeks',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'supplier.indeks' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'supplier',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin',
        ),
        'uses' => 'App\\Http\\Controllers\\SupplierController@indeks',
        'controller' => 'App\\Http\\Controllers\\SupplierController@indeks',
        'as' => 'supplier.indeks',
        'namespace' => NULL,
        'prefix' => '/supplier',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'supplier.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'supplier/{supplier}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin',
        ),
        'uses' => 'App\\Http\\Controllers\\SupplierController@update',
        'controller' => 'App\\Http\\Controllers\\SupplierController@update',
        'as' => 'supplier.update',
        'namespace' => NULL,
        'prefix' => '/supplier',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'supplier.hapus' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'supplier/{supplier}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin',
        ),
        'uses' => 'App\\Http\\Controllers\\SupplierController@hapus',
        'controller' => 'App\\Http\\Controllers\\SupplierController@hapus',
        'as' => 'supplier.hapus',
        'namespace' => NULL,
        'prefix' => '/supplier',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'masterBarang.indeks' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'master-barang',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin',
        ),
        'uses' => 'App\\Http\\Controllers\\MasterBarangController@indeks',
        'controller' => 'App\\Http\\Controllers\\MasterBarangController@indeks',
        'as' => 'masterBarang.indeks',
        'namespace' => NULL,
        'prefix' => '/master-barang',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'masterBarang.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'master-barang/{masterBarang}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin',
        ),
        'uses' => 'App\\Http\\Controllers\\MasterBarangController@update',
        'controller' => 'App\\Http\\Controllers\\MasterBarangController@update',
        'as' => 'masterBarang.update',
        'namespace' => NULL,
        'prefix' => '/master-barang',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'masterBarang.hapus' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'master-barang/{masterBarang}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin',
        ),
        'uses' => 'App\\Http\\Controllers\\MasterBarangController@hapus',
        'controller' => 'App\\Http\\Controllers\\MasterBarangController@hapus',
        'as' => 'masterBarang.hapus',
        'namespace' => NULL,
        'prefix' => '/master-barang',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'bahanPokok.formTambah' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'logistik/bahan-pokok/tambah',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin',
        ),
        'uses' => 'App\\Http\\Controllers\\BahanPokokController@formTambah',
        'controller' => 'App\\Http\\Controllers\\BahanPokokController@formTambah',
        'as' => 'bahanPokok.formTambah',
        'namespace' => NULL,
        'prefix' => '/logistik/bahan-pokok',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'bahanPokok.simpanBatch' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'logistik/bahan-pokok/simpan-batch',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin',
        ),
        'uses' => 'App\\Http\\Controllers\\BahanPokokController@simpanBatch',
        'controller' => 'App\\Http\\Controllers\\BahanPokokController@simpanBatch',
        'as' => 'bahanPokok.simpanBatch',
        'namespace' => NULL,
        'prefix' => '/logistik/bahan-pokok',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dapur.indeks' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'dapur',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin',
        ),
        'uses' => 'App\\Http\\Controllers\\DapurController@indeks',
        'controller' => 'App\\Http\\Controllers\\DapurController@indeks',
        'as' => 'dapur.indeks',
        'namespace' => NULL,
        'prefix' => '/dapur',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dapur.simpan' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'dapur',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin',
        ),
        'uses' => 'App\\Http\\Controllers\\DapurController@simpan',
        'controller' => 'App\\Http\\Controllers\\DapurController@simpan',
        'as' => 'dapur.simpan',
        'namespace' => NULL,
        'prefix' => '/dapur',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dapur.update' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'dapur/{dapur}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin',
        ),
        'uses' => 'App\\Http\\Controllers\\DapurController@update',
        'controller' => 'App\\Http\\Controllers\\DapurController@update',
        'as' => 'dapur.update',
        'namespace' => NULL,
        'prefix' => '/dapur',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dapur.hapus' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'dapur/{dapur}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin',
        ),
        'uses' => 'App\\Http\\Controllers\\DapurController@hapus',
        'controller' => 'App\\Http\\Controllers\\DapurController@hapus',
        'as' => 'dapur.hapus',
        'namespace' => NULL,
        'prefix' => '/dapur',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dapur.detail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'dapur/{dapur}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_logistik,super_admin',
        ),
        'uses' => 'App\\Http\\Controllers\\DapurController@detail',
        'controller' => 'App\\Http\\Controllers\\DapurController@detail',
        'as' => 'dapur.detail',
        'namespace' => NULL,
        'prefix' => '/dapur',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dapur.beranda' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'dapur-saya',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\DapurController@beranda',
        'controller' => 'App\\Http\\Controllers\\DapurController@beranda',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dapur.beranda',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dapur.simpanLaporan' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'dapur-saya/laporan',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\DapurController@simpanLaporan',
        'controller' => 'App\\Http\\Controllers\\DapurController@simpanLaporan',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dapur.simpanLaporan',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dapur.formBeliMandiri' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'dapur-saya/beli-mandiri',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\DapurController@formBeliMandiri',
        'controller' => 'App\\Http\\Controllers\\DapurController@formBeliMandiri',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dapur.formBeliMandiri',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dapur.simpanBeliMandiri' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'dapur-saya/beli-mandiri',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'role:admin_dapur',
        ),
        'uses' => 'App\\Http\\Controllers\\DapurController@simpanBeliMandiri',
        'controller' => 'App\\Http\\Controllers\\DapurController@simpanBeliMandiri',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dapur.simpanBeliMandiri',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'storage.local' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'storage/{path}',
      'action' => 
      array (
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:3:{s:4:"disk";s:5:"local";s:6:"config";a:5:{s:6:"driver";s:5:"local";s:4:"root";s:71:"D:\\kuliah\\MKI\\Projek\\mbg2-main-revisi (7)\\mbg2-main\\storage\\app/private";s:5:"serve";b:1;s:5:"throw";b:0;s:6:"report";b:0;}s:12:"isProduction";b:0;}s:8:"function";s:323:"function (\\Illuminate\\Http\\Request $request, string $path) use ($disk, $config, $isProduction) {
                    return (new \\Illuminate\\Filesystem\\ServeFile(
                        $disk,
                        $config,
                        $isProduction
                    ))($request, $path);
                }";s:5:"scope";s:47:"Illuminate\\Filesystem\\FilesystemServiceProvider";s:4:"this";N;s:4:"self";s:32:"00000000000005560000000000000000";}}',
        'as' => 'storage.local',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'path' => '.*',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'storage.local.upload' => 
    array (
      'methods' => 
      array (
        0 => 'PUT',
      ),
      'uri' => 'storage/{path}',
      'action' => 
      array (
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:3:{s:4:"disk";s:5:"local";s:6:"config";a:5:{s:6:"driver";s:5:"local";s:4:"root";s:71:"D:\\kuliah\\MKI\\Projek\\mbg2-main-revisi (7)\\mbg2-main\\storage\\app/private";s:5:"serve";b:1;s:5:"throw";b:0;s:6:"report";b:0;}s:12:"isProduction";b:0;}s:8:"function";s:325:"function (\\Illuminate\\Http\\Request $request, string $path) use ($disk, $config, $isProduction) {
                    return (new \\Illuminate\\Filesystem\\ReceiveFile(
                        $disk,
                        $config,
                        $isProduction
                    ))($request, $path);
                }";s:5:"scope";s:47:"Illuminate\\Filesystem\\FilesystemServiceProvider";s:4:"this";N;s:4:"self";s:32:"00000000000005580000000000000000";}}',
        'as' => 'storage.local.upload',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'path' => '.*',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
  ),
)
);
