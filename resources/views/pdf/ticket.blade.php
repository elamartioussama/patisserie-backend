<!-- <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket de Commande</title>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #ccc; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Ticket de Commande de {{ $order->user->name }}</h2>
        <p>Date : {{ $order->created_at->format('d/m/Y H:i') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>Prix par produit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}-{{ $item->product->description }}</td>
                    <td>{{ $item->product->price }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->product->price * $item->quantity, 2) }} dh</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="text-align: right; margin-top: 20px;"><strong>Total : {{ number_format($order->total_price, 2) }} €</strong></p>
</body>
</html> -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Commande</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #000;
            padding: 20px;
            background-color: #fff;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #f3e884ec;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 24px;
            color: #000;
        }

        .header p {
            margin: 5px 0;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background-color: #f3e884ec;
            color: #000;
            padding: 10px;
            font-size: 14px;
            text-align: left;
            border-bottom: 2px solid #ccc;
        }

        td {
            padding: 10px;
            font-size: 13px;
            border-bottom: 1px solid #eee;
        }

        .total {
            text-align: right;
            margin-top: 20px;
            font-size: 16px;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Ticket de Commande</h2>
        <p><strong>Client :</strong> {{ $order->user->name }}</p>
        <p><strong>Tel :</strong> {{ $order->user->tel }}</p>

        <p><strong>Date :</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Prix Unitaire</th>
                <th>Quantite</th>
                <th>Sous-total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product->name }} {{ $item->product->description }}</td>
                    <td>{{ number_format($item->product->price, 2) }} DH</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->product->price * $item->quantity, 2) }} DH</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="total">Total : {{ number_format($order->total_price, 2) }} DH</p>

    <div class="footer">
        Merci pour votre commande !  
    </div>
</body>
</html>

