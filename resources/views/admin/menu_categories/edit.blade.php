<!DOCTYPE html>
<html>
<head>
    <title>Sửa danh mục món ăn</title>
</head>
<body>
    <h1>Sửa danh mục: {{ $category->name }}</h1>

    <form action="{{ route('menu-categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')
        <label>Tên danh mục:</label><br>
        <input type="text" name="name" value="{{ $category->name }}" required><br><br>

        <label>Mô tả:</label><br>
        <textarea name="description">{{ $category->description }}</textarea><br><br>

        <button type="submit">Cập nhật</button>
    </form>
</body>
</html>
