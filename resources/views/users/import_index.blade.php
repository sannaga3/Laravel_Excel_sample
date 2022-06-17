<h1>インポート一覧</h1>
@if ($error ?? '')
    <div>{{ $error }}</div>
@endif

<div class="upload">
    <form action="{{ route('users.import.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <p>ヘッダーの行を入力してください</p>
        <div><input type="number" name="headingRow"></div>
        <p>DBへ登録を開始する行を入力してください</p>
        <div><input type="number" name="startRow"></div>
        <p>CSVデータを選択してください。</p>
        <input type="file" name="csvFile" />
        <button type="submit" name="importType" value="csv">インポート</button>
    </form>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>名前</th>
        <th>性別</th>
        <th>年齢</th>
        <th>住所</th>
        <th>電話番号</th>
        <th>メールアドレス</th>
    </tr>

    @if (isset($users))
        @foreach ($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->gender }}</td>
                <td>{{ $user->age }}</td>
                <td>{{ $user->address }}</td>
                <td>{{ $user->tel }}</td>
                <td>{{ $user->email }}</td>
            </tr>
        @endforeach
    @else
        まだファイルがインポートされていません
    @endif
</table>

<a href="{{ route('users.index') }}">ユーザー一覧</a>
