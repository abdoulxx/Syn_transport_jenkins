<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Page de test - Articles</title>
    <style>
        .article {
            border: 1px solid #ccc;
            padding: 16px;
            margin: 16px;
        }
    </style>
</head>
<body>
    <h1>Liste des articles</h1>
    <?php
    // Liste des articles
    $articles = [
        ['id' => 1, 'name' => 'Article 1', 'price' => 1000],
        ['id' => 2, 'name' => 'Article 2', 'price' => 2000],
        ['id' => 3, 'name' => 'Article 3', 'price' => 3000],
    ];

    foreach ($articles as $article) {
        echo "<div class='article'>";
        echo "<h2>{$article['name']}</h2>";
        echo "<p>Prix: {$article['price']} FCFA</p>";
        echo "<form action='pay.php' method='POST'>";
        echo "<input type='hidden' name='article_id' value='{$article['id']}'>";
        echo "<input type='hidden' name='article_name' value='{$article['name']}'>";
        echo "<input type='hidden' name='article_price' value='{$article['price']}'>";
        echo "<button type='submit'>Acheter</button>";
        echo "</form>";
        echo "</div>";
    }
    ?>
</body>
</html>
