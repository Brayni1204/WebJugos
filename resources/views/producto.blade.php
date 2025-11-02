<!DOCTYPE html>
<html>

<head>
    <title>Producto de prueba</title>
</head>

<body>
    <h1>🛍️ Zapatillas Deportivas</h1>
    <p>Precio: S/ 120.00</p>
    <form action="{{ route('pago.realizar') }}" method="GET">
        <button type="submit">Pagar con Mercado Pago</button>
    </form>
</body>

</html>
