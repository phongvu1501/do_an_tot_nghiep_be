<!DOCTYPE html>
<html>
<head>
    <title>Thêm danh mục món ăn</title>
</head>
<body>
    <h1>Thêm danh mục món ăn</h1>

    <form action="{{ route('menu-categories.store') }}" method="POST">
        @csrf
        <label>Tên danh mục:</label><br>
        <input type="text" name="name" required><br><br>

        <label>Mô tả:</label><br>
        <textarea name="description"></textarea><br><br>

        <button type="submit">Lưu</button>
    </form>
</body>
</html>
