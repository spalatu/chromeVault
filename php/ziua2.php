<?php

$nume = "ChromeVault";
$versiune = "1.0";
$mesaj = "Salut din PHP!";

echo "<h1>" . $nume . "</h1>";
echo "<p>" . $mesaj . "</p>";
echo "<p>Versiune: " . $versiune . "</p>";
?>

<script>
    console.log("<?= $mesaj ?>");
    console.log("Aplicatia: <?= $nume ?>");
</script>