<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;   // レコード一覧の型設定
use Maatwebsite\Excel\Concerns\WithColumnWidths; // カラムの幅設定
use Maatwebsite\Excel\Concerns\WithHeadings;     // ヘッダーの表示設定
use Maatwebsite\Excel\Concerns\WithMapping;      // レコード一覧の表示設定
// use Maatwebsite\Excel\Concerns\ShouldAutoSize; // implementsに指定するだけで自動調整してくれるが、日本語(マルチバイト文字)に対応してないかも。column_max_lengths関数で自動調整

class UsersExport implements FromCollection, WithHeadings, WithColumnWidths, WithMapping
{
    protected array $headers; // ヘッダー
    protected collection $rows; // レコード一覧
    protected array $column_max_lengths; // 各カラムの最大文字数をもつ配列

    public function __construct()
    {
        $this->header = [
            'ID',
            '名前',
            '性別',
            '年齢',
            '住所',
            '電話番号',
            'メールアドレス',
            '作成日',
            '更新日',
        ];

        $this->rows = User::all()->makeHidden(['password']);

        $this->column_max_lengths = $this->set_Column_max_lengths();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    // レコード一覧をコレクションで扱う
    public function collection()
    {
        return $this->rows;
    }

    // ヘッダーの表示を加工
    public function headings(): array
    {
        return $this->header;
    }

    // カラムの幅を設定
    public function columnWidths(): array
    {
        $column_max_lengths = $this->column_max_lengths;

        // 各カラムの最大文字数を元に幅を設定
        return [
            'A' => $column_max_lengths[0],
            'B' => $column_max_lengths[1],
            'C' => $column_max_lengths[2],
            'D' => $column_max_lengths[3],
            'E' => $column_max_lengths[4],
            'F' => $column_max_lengths[5],
            'G' => $column_max_lengths[6],
            'H' => $column_max_lengths[7],
            'I' => $column_max_lengths[8],
        ];
    }

    // 個別レコードの表示を加工
    public function map($row): array
    {
        $arr_row = $row->toArray();   // 個々のレコードは配列で返す必要がある為型変換 collection -> array 
        $arr_row['created_at'] = date('Y-m-d H:i:s', strtotime($arr_row['created_at'])); // 2段階の加工 string型->date型->フォーマット変更
        $arr_row['updated_at'] = date('Y-m-d H:i:s', strtotime($arr_row['updated_at']));

        return  $arr_row;
    }

    // 幅の自動調整(ShouldAutoSizeの代わり)。 各カラムの値で一番文字数の多いものとヘッダーの文字数を比較する
    public function set_Column_max_lengths()
    {
        // 最初のレコードからkeyのみを配列で取得
        $header_keys = array_keys($this->rows->first()->toArray());

        // 各カラムの値一覧を取得
        $per_column_values = array_map(function ($key) {
            return $this->rows->pluck($key);
        }, $header_keys);

        // 値一覧の値を全て文字数に変換する
        $per_column_str_lengths = array_map(function ($values) {
            $str_length = $values->map(function ($value) {
                return strlen($value);           // 日本語の方がアルファベットより幅があるためmb_strlenではなくstrlenを採用
            });                                  // strlen => 日本語は3byte アルフファベットは1byte, mb_strlen マルチバイトか否かに関わらず全てが1byte
            return $str_length;
        }, $per_column_values);

        // 各カラムの値一覧から文字数の最大値を取得
        $per_column_max_length = array_map(function ($column_str_lengths) {
            return max($column_str_lengths->toArray());
        }, $per_column_str_lengths);

        // カラム毎に値の最大文字数とヘッダーの文字数を比較し、大きい方をカラム幅の基準にする。
        $max_lengths = array_map(function ($column_max_length, $header) {
            $header_length = strlen($header);
            return $column_max_length < $header_length ? $header_length : $column_max_length;
        }, $per_column_max_length, $this->header);

        // 文字数が少ないと幅が狭すぎる為調整。カラム幅自動調整完了。
        $adjusted_max_lengths = array_map(function ($max_length) {
            if ($max_length < 5) return 5;
            return $max_length;
        }, $max_lengths);

        return $adjusted_max_lengths;
    }
}