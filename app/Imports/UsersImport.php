<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Hash;

class UsersImport implements ToModel, WithValidation, WithHeadingRow, WithStartRow
{
    protected int $headingRow;
    protected int $startRow;

    public function __construct($params)
    {
        $this->headingRow = intval($params['headingRow']);
        $this->startRow = intval($params['startRow']);
    }

    // 各コメントアウト先頭の番号はメソッドの処理順
    // ④レコードの生成
    public function model(array $row)
    {
        // idは自動で振られる為CSV側のIDをruleから外したが、自動で取得されていたので削除。
        array_shift($row);

        return new User([
            'name'     => $row['名前'],
            'gender'     => $row['性別'],
            'age'   => $row['年齢'],
            'address'  => $row['住所'],
            'tel'      => $row['電話番号'],
            'email'    => $row['メールアドレス'],
            'password' => Hash::make('password')
        ]);

        // WithValidationの問題がある為、csvのカラム番号ではなくカラム名で取得すべき
        // return new User([
        //     'name'     => $row[1],
        //     'gender'     => $row[2],
        //     'age'   => $row[3],
        //     'address'  => $row[4],
        //     'tel'      => $row[5],
        //     'email'    => $row[6],
        //     'password' => Hash::make('password')
        // ]);
    }

    // ③バリデーション
    public function rules(): array
    {
        return [
            // WithValidationは公式やネットで紹介されている記述で動作しない。
            // ruleに関するissueがいくつも挙がっており、下記記述でのみ動作した。
            // https://github.com/SpartnerNL/Laravel-Excel/issues/2975

            'ID'  => 'required|integer',
            '名前' => 'required|string',
            '性別' => 'required|boolean',
            '年齢' => 'required|integer',
            '住所' => 'required|string',
            '電話番号' => 'required|string',
            'メールアドレス' => 'required',

            // 以下は間違った記述方法
            // '*.1' => "required|string",  // 名前
            // '*.2' => "required|boolean", // 性別
            // '*.3' => "required|integer", // 年齢
            // '*.4' => "required|string",  // 住所
            // '*.5' => "required|string",  // 電話番号
            // '*.6' => "required|unique:users", // メールアドレス

            // 1 => "required|string",  // 名前
            // 2 => "required|boolean", // 性別
            // 3 => "required|integer", // 年齢
            // 4 => "required|string",  // 住所
            // 5 => "required|string",  // 電話番号
            // 6 => "required|unique:users", // メールアドレス
        ];
    }

    // ②ヘッダー(カラム)の行指定
    public function headingRow(): int
    {
        return $this->headingRow;
    }

    // ①リスト(レコード)の開始行指定
    public function startRow(): int
    {
        return $this->startRow;
    }
}