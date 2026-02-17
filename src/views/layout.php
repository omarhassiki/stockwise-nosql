<?php

function render_layout($title, $content) {
  $path = $_SERVER['REQUEST_URI'] ?? '';
  $is = function($needle) use ($path) { return strpos($path, $needle) !== false; };

  echo "<!doctype html><html><head><meta charset='utf-8'>";
  echo "<meta name='viewport' content='width=device-width,initial-scale=1'>";
  echo "<title>" . htmlspecialchars($title) . "</title>";
  echo "<link rel='stylesheet' href='/stockwise-nosql/public/assets/style.css'>";
  echo "</head><body>";

  echo "<div class='container'>";
  echo "<div class='header'>
          <div>
            <h1>StockWise</h1>
            <div class='subtitle'></div>
          </div>
          <div class='small'>" . htmlspecialchars($title) . "</div>
        </div>";

  echo "<div class='nav'>
          <a class='".($is('/public/') && !$is('products.php') && !$is('suppliers.php') && !$is('orders.php') ? "active":"")."' href='/stockwise-nosql/public/'>Accueil</a>
          <a class='".($is('products.php') ? "active":"")."' href='/stockwise-nosql/public/products.php'>Produits</a>
          <a class='".($is('suppliers.php') ? "active":"")."' href='/stockwise-nosql/public/suppliers.php'>Fournisseurs</a>
          <a class='".($is('orders.php') ? "active":"")."' href='/stockwise-nosql/public/orders.php'>Commandes</a>
        </div>";

  echo "<div class='card'>" . $content . "</div>";
  echo "</div></body></html>";
}