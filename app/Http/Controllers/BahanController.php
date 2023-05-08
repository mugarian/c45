<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreBahanRequest;
use App\Http\Requests\UpdateBahanRequest;
use Attribute;

class BahanController extends Controller
{
   /**
    * Display a listing of the resource.
    */
   public function index()
   {
      /**
       * $columns : mengumpulkan semua data atribut dari pengambilan nama atribut tabel bahan database c45
       */
      $columns = DB::select("SELECT `COLUMN_NAME` as 'attribute' FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='c45'
      AND `TABLE_NAME`='bahans';");

      /**
       * $case : inisiasi array case untuk wadah semua nilai mining
       * $case['total] : membuat array atribut total untuk menyimpan semua nilai mining atribut total
       * $attributes : membuat array attributes untuk mengumpulkan semua atribut yang dibutuhkan
       */
      $case = [];
      $case['total'] = [];
      $attributes = [];

      /**
       * membersihkan atribut dari id & timestamps laravel
       * $case[attribut] : membuat array untuk setiap atribut yang telah dipilih pada array case
       * $attributes[] : mengumpulkan data atribut yang sudah dibersihkan pada array attributes
       */
      foreach ($columns as $column) {
         if ($column->attribute == 'id' || $column->attribute == 'created_at' || $column->attribute == 'updated_at') continue;
         $case[$column->attribute] = [];
         $attributes[] = $column->attribute;
      }

      /**
       * $end_column : memisahkan attribut label pada atribut yang telah dibersihkan
       * $end : mengambil array atau atribut label pada array atribut
       * $end_attrib : mengambil value array atau nama label pada atribut terakhir
       */
      $end_column = array_slice($columns, -3, 1);
      $end = end($end_column);
      $end_attrib = $end->attribute;

      /**
       * unset : menghapus atribut label pada array case
       * arrayy_pop : menghapus array terakhir atau atribut label pada array kumpulan atributes
       */
      unset($case[$end_attrib]);
      array_pop($attributes);

      /**
       * $data_label : mendapatkan grouping nama data label pada tabel bahan database c45
       * $labels : membuat array pengumpulan nilai label
       * $labels jumlah : inisiasi nilai jumlah data dari semua nilai label
       */
      $data_label = DB::table('bahans')->select($end_attrib . ' as label')->groupBy($end_attrib)->get();
      $labels = [];
      $labels[] = 'jumlah';
      foreach ($data_label as $dl) {
         // memasukkan semua data_label ke dalam array labels
         $labels[] = $dl->label;
      }


      /**
       * memasukkan semua atribut, data atribut, dan label kedalam case untuk mengumpulkan nilai data keperluan mining c45
       * $data_attribute : mendapatkan grouping nama data atribut pada tabel bahan database c45
       */
      foreach ($attributes as $attribute) {
         $data_attribute = DB::table('bahans')->select($attribute . ' as data')->groupBy($attribute)->get();

         foreach ($data_attribute as $da) {
            // $case[$attribute][data atribut] : inisiasi array kosong untuk value array baru pendukung data root
            $case[$attribute][$da->data] = [];

            foreach ($labels as $label) {
               // menginisiasikan penamaan atribut untuk memudahkan penamaan root pada update or insert root
               $case[$attribute][$da->data]['atribute'] = $attribute;
               $case[$attribute][$da->data]['data_atribute'] = $da->data;

               /**
                * perhitungan data berdasarkan nama label dan nama data atribut
                * pemisahan label jumlah untuk pembeda pengkondisian query sql tanpa atribut label
                * atribut 'total' pada array case dinisiasikan secara manual karena tidak terdapat pada array $attributes
                */
               if ($label == 'jumlah') {
                  /**
                   * $attribut = $da->data : pencocokkan nama atribut atau kolom tabel dengan data kolom atribut tersebut
                   * atribut total & label jumlah : menghitung semua data atribut
                   */
                  $case[$attribute][$da->data][$label] = DB::table('bahans')->where($attribute, '=', $da->data)->count();
                  $case['total'][$label] = DB::table('bahans')->count();
               } else {
                  /**
                   * $attribute = $da->data : pencocokkan nama atribut dengan nilai data kolom &
                   * $end_attrib = $label : pencocokkan atribut akhir atau atribut label dengan data label yang dimaksud
                   */
                  $case[$attribute][$da->data][$label] = DB::table('bahans')->where($attribute, '=', $da->data)->where($end_attrib, '=', $label)->count();
                  $case['total'][$label] = DB::table('bahans')->where($end_attrib, '=', $label)->count();
               }
            }

            /**
             * pengisiasian array entropy[nama data atribut][data pendukung root]
             * array entropy dibuat untuk menyingkatkan data yang dibutuhkan pada perhitungan rumus entropy
             */
            $entropy[$da->data]['atribut'] = $attribute;
            $entropy[$da->data]['jumlah'] = $case[$attribute][$da->data]['jumlah'];
            $entropy[$da->data]['no'] = $case[$attribute][$da->data]['no'];
            $entropy[$da->data]['yes'] = $case[$attribute][$da->data]['yes'];

            /**
             * *=====================================
             * * RUMUS ENTROPY SETIAP DATA ATRIBUT
             * *=====================================
             */
            $entropy[$da->data]['entropy'] =
               (
                  (-$entropy[$da->data]['no'] / $entropy[$da->data]['jumlah']) *
                  log($entropy[$da->data]['no'] / $entropy[$da->data]['jumlah'], 2)
               ) +
               (
                  (-$entropy[$da->data]['yes'] / $entropy[$da->data]['jumlah']) *
                  log($entropy[$da->data]['yes'] / $entropy[$da->data]['jumlah'], 2)
               );

            /**
             * is_nan(entropy) : penyeleksian rumus entropy bernilai NAN menjadi 0 karena perhitungan log 0
             * array_unshift(entropy) : pemindahan array key entropy menjadi pertama di array case[atribut][data atribut] untuk memudahkan perbandingan nilai entropy
             */
            $entropy[$da->data]['entropy'] = (is_nan($entropy[$da->data]['entropy']) ? 0 : $entropy[$da->data]['entropy']);
            array_unshift($case[$attribute][$da->data], $entropy[$da->data]['entropy']);
         }
      }

      /**
       * pengisiasian array entropy[atribut total][data pendukung root]
       * array total diinisiasikan secara manual karena tidak terdapat di array atribut
       * array entropy dibuat untuk menyingkatkan data yang dibutuhkan pada perhitungan rumus entropy
       */
      $entropy['total']['atribut'] = 'total';
      $entropy['total']['jumlah'] = $case['total']['jumlah'];
      $entropy['total']['no'] = $case['total']['no'];
      $entropy['total']['yes'] = $case['total']['yes'];

      /**
       * *=============================
       * * RUMUS ENTROPY ATRIBUT TOTAL
       * *=============================
       */
      $entropy['total']['entropy'] =
         (-$case['total']['no'] / $entropy['total']['jumlah'] * log($case['total']['no'] / $entropy['total']['jumlah'], 2)) +
         (-$case['total']['yes'] / $entropy['total']['jumlah'] * log($case['total']['yes'] / $entropy['total']['jumlah'], 2));

      /**
       * is_nan(entropy) : penyeleksian rumus entropy bernilai NAN menjadi 0 karena perhitungan log 0
       * penginisiasian entropy[total] pada case[total]
       */
      $entropy['total']['entropy'] = (is_nan($entropy['total']['entropy']) ? 0 : $entropy['total']['entropy']);
      $case['total']['entropy'] = $entropy['total']['entropy'];

      /**
       * *=============================
       * * INISIASI PERHITUNGAN GAIN
       * *=============================
       */
      foreach ($attributes as $attribute) {
         $data_attribute = DB::table('bahans')->select($attribute . ' as data')->groupBy($attribute)->get();

         /**
          * $sum_entropy : inisiasi penjumlahan entropy setiap data
          * bernilai 0 kembali setiap atribut agar tidak terjadi penumpukan nilai penjumlahan setiap entropy data atribut
          */
         $sum_entropy = 0;

         /**
          * *===============================================
          * * RUMUS GAIN: PENJUMLAHAN SETIAP ENTROPY
          * *===============================================
          */
         foreach ($data_attribute as $da) {
            $sum_entropy += (($entropy[$da->data]['jumlah'] / $entropy['total']['jumlah']) * $entropy[$da->data]['entropy']);

            // is_nan(sum_entropy) penyeleksian $sum_entropy bernilai NAN menjadi 0
            $sum_entropy = (is_nan($sum_entropy) ? 0 : $sum_entropy);
         }

         // inisiasi array baru untuk nilai gain dan penamaan atribut gain
         $gain[$attribute] = [];

         /**
          * *===========================================================================
          * * RUMUS GAIN: PENGURANGAN ENTROPY TOTAL DENGAN JUMLAH ENTROPY SETIAP ATRIBUT
          * *===========================================================================
          */
         $gain[$attribute]['gain'] = $entropy['total']['entropy'] - $sum_entropy;

         // penamaan atribut pada array gain untuk memudahkan penamaan node
         $gain[$attribute]['atribut'] = $attribute;

         // inisiasi array baru untuk array gain pada array case[atribut]['gain']
         // $case[$attribute]['gain'] = [];
         $case[$attribute]['gain'] = $gain[$attribute]['gain'];
      }

      /**
       * $gain_atribut : nama atribut dengan gain tertinggi
       * $nodes : pembuatan array node dari gain tertinggi
       * ? unset(nodes[gain]) : menghilangkan array gain untuk memudahkan pemilahan data atribut?
       */
      $gain_atribut = max($gain)['atribut'];
      $nodes = $case[$gain_atribut];
      unset($nodes['gain']);

      /**
       * TODO: upsert node and root.
       * TODO: cara
       */

      $last_nodes = DB::table('nodes')->select('level')->orderBy('id', 'asc')->get();
      $last_level = $last_nodes->isEmpty() ? 1 : $last_nodes[0]->level;

      DB::table('nodes')->upsert(
         [
            'nama' => $gain_atribut,
            'level' => $last_level,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
         ],
         'nama',
         ['level', 'updated_at']
      );

      foreach ($nodes as $node) {
         if ($node['yes'] == 0) {
            $label = 'no';
         } elseif ($node['no'] == 0) {
            $label = 'yes';
         } else {
            $label = 'node';
         }
         DB::table('roots')->upsert(
            [
               'node_id' => $last_level,
               'nama' => $node['data_atribute'],
               'jumlah' => $node['jumlah'],
               'no' => $node['no'],
               'yes' => $node['yes'],
               'entropy' => $node[0],
               'label' => $label,
               'created_at' => date('Y-m-d H:i:s'),
               'updated_at' => date('Y-m-d H:i:s')
            ],
            'nama',
            ['node_id', 'jumlah', 'no', 'yes', 'entropy', 'label', 'updated_at']
         );
      }

      return dd('sukses');
   }

   public function getAttribute()
   {
   }

   public function entropy()
   {
      //
   }

   public function gain()
   {
      //
   }

   public function node()
   {
      //
   }

   public function root()
   {
   }

   /**
    * Show the form for creating a new resource.
    */
   public function create()
   {
      //
   }

   /**
    * Store a newly created resource in storage.
    */
   public function store(StoreBahanRequest $request)
   {
      //
   }

   /**
    * Display the specified resource.
    */
   public function show(Bahan $bahan)
   {
      //
   }

   /**
    * Show the form for editing the specified resource.
    */
   public function edit(Bahan $bahan)
   {
      //
   }

   /**
    * Update the specified resource in storage.
    */
   public function update(UpdateBahanRequest $request, Bahan $bahan)
   {
      //
   }

   /**
    * Remove the specified resource from storage.
    */
   public function destroy(Bahan $bahan)
   {
      //
   }
}
