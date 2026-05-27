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

<?php

$numere = [4, 7, 12, 3, 8, 15, 6, 9, 2, 11];

$pare = 0;
$impare = 0;

for ($i = 0; $i < count($numere); $i++) {
    if ($numere[$i] % 2 == 0) {
        $pare++;
    } else {
        $impare++;
    }
}

echo "<h2>Par sau impar</h2>";
echo "<p>Numere: " . implode(", ", $numere) . "</p>";
echo "<p>Pare: " . $pare . "</p>";
echo "<p>Impare: " . $impare . "</p>";
?>