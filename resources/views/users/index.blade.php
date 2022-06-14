<h1>ユーザー一覧</h1>
<a href="{{ route('users.create') }}">新規追加</a>
<form action="{{ route('users.export') }}" method="POST">
    @csrf
    <button type="submit" name="exportType" value="csv">CSV出力</button>
    <button type="submit" name="exportType" value="excel">Excel出力</button>
</form>

<table>
    <tr>
        <th>ID</th>
        <th>名前</th>
        <th>性別</th>
        <th>年齢</th>
        <th>住所</th>
        <th>電話番号</th>
        <th>メールアドレス</th>
        <th>詳細</th>
        <th>変更</th>
        <th>削除</th>
    </tr>
    @foreach ($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->gender }}</td>
            <td>{{ $user->age }}</td>
            <td>{{ $user->address }}</td>
            <td>{{ $user->tel }}</td>
            <td>{{ $user->email }}</td>
            <td><a href="{{ route('users.show', $user->id) }}">詳細</a></td>
            <td><a href="{{ route('users.edit', $user->id) }}">編集</a></td>
            <td>
                <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                    id="delete_{{ $user->id }}">
                    @csrf
                    @method('delete')
                    <input type="button" class="btn login-button" data-id="{{ $user->id }}"
                        onclick="deletePost(this);" value="削除">
                </form>
            </td>
        </tr>
    @endforeach
</table>

<script>
    function deletePost(e) {
        if (!window.confirm('本当に削除しますか？')) {
            return false;
        }
        document.getElementById('delete_' + e.dataset.id).submit();
    };
</script>
